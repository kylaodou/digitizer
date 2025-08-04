<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AuditLog;
use App\Services\MistralAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Exception;

class DocumentController extends Controller
{
    /**
     * Display a listing of the user's documents.
     */
    public function index()
    {
        $documents = Auth::user()->documents()
            ->latest()
            ->paginate(12);

        return view('documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'in:recto,recto_verso'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max - accepter tous les types de fichiers
            'page_count' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $file = $request->file('file');

        // Calculate processing cost
        $rectoRate = 200; // FCFA
        $rectoVersoRate = 150; // FCFA
        $pageCount = $request->page_count;

        $processingCost = $request->document_type === 'recto_verso'
            ? $pageCount * $rectoVersoRate
            : $pageCount * $rectoRate;

        // Check user balance
        if (!$user->hasSufficientBalance($processingCost)) {
            return back()
                ->withErrors(['balance' => 'Solde insuffisant. Coût estimé: ' . number_format($processingCost, 0, ',', ' ') . ' FCFA'])
                ->withInput();
        }

        // Store the file
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('documents', $filename, 'public');

        // Create document record
        $document = Document::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'thumbnail_path' => null, // Sera généré après
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'page_count' => $pageCount,
            'document_type' => $request->document_type, // Type de scan (recto/recto_verso)
            'processing_cost' => $processingCost,
            'status' => 'uploaded',
        ]);

        // Generate thumbnail for all file types
        $thumbnailPath = $this->generateThumbnail($document);
        if ($thumbnailPath) {
            $document->thumbnail_path = $thumbnailPath;
            $document->save();
        }

        // Deduct cost from user balance
        $user->deductBalance($processingCost);

        // Log the action
        AuditLog::logAction('document_uploaded', $document, null, [
            'cost' => $processingCost,
            'remaining_balance' => $user->fresh()->account_balance,
        ]);

        // Créer la facture immédiatement
        $this->createInvoiceForDocument($document, $processingCost);

        // Queue OCR processing (simulated here)
        $this->processOCR($document);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document téléversé avec succès. Traitement en cours...');
    }

    /**
     * Display the specified document.
     */
    public function show(Document $document)
    {
        // Check if user owns the document
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        return view('documents.show', compact('document'));
    }

    /**
     * Download the specified document.
     */
    public function download(Document $document)
    {
        // Check if user owns the document
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Fichier non trouvé.');
        }

        // Log download
        AuditLog::logAction('document_downloaded', $document);

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_filename
        );
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy(Document $document)
    {
        // Check if user owns the document
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        // Delete files
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        if ($document->thumbnail_path && Storage::disk('public')->exists($document->thumbnail_path)) {
            Storage::disk('public')->delete($document->thumbnail_path);
        }

        // Log deletion
        AuditLog::logAction('document_deleted', $document);

        // Delete document record
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document supprimé avec succès.');
    }

    /**
     * Generate thumbnail for ALL types of files (images, PDFs, Office docs, etc.).
     */
    public function generateThumbnail($document)
    {
        if (!$document->file_path) {
            return null;
        }

        $filePath = $document->file_path;
        $fullPath = storage_path('app/public/' . $filePath);

        if (!file_exists($fullPath)) {
            Log::warning("Fichier non trouvé pour thumbnail: $fullPath");
            return null;
        }

        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        $thumbnailPath = 'thumbnails/' . $filename . '_thumb.jpg';
        $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);

        // Create thumbnail directory if it doesn't exist
        $thumbnailDir = dirname($thumbnailFullPath);
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
        }

        // Déterminer le type de fichier
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = mime_content_type($fullPath) ?: '';

        try {
            // 1. IMAGES - Génération de thumbnail classique
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']) || str_contains($mimeType, 'image/')) {
                return $this->generateImageThumbnail($fullPath, $thumbnailFullPath, $thumbnailPath);
            }

            // 2. PDF - Conversion première page en image
            elseif ($extension === 'pdf' || str_contains($mimeType, 'pdf')) {
                return $this->generatePdfThumbnail($fullPath, $thumbnailFullPath, $thumbnailPath);
            }

            // 3. OFFICE DOCUMENTS - Tentative de conversion (formats Microsoft Office et LibreOffice)
            elseif ($this->isOfficeDocument($extension, $mimeType)) {
                return $this->generateOfficeThumbnail($fullPath, $thumbnailFullPath, $thumbnailPath, $extension);
            }

            // 4. FICHIERS TEXTE - Génération d'aperçu textuel en image
            elseif (in_array($extension, ['txt', 'csv', 'json', 'xml']) || str_contains($mimeType, 'text/')) {
                return $this->generateTextThumbnail($fullPath, $thumbnailFullPath, $thumbnailPath);
            }

            // 5. AUTRES TYPES - Génération d'un placeholder personnalisé
            else {
                return $this->generateGenericThumbnail($fullPath, $thumbnailFullPath, $thumbnailPath, $extension);
            }

        } catch (\Exception $e) {
            Log::warning("Erreur génération thumbnail pour $filePath: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Génère un thumbnail pour les images
     */
    private function generateImageThumbnail(string $fullPath, string $thumbnailFullPath, string $thumbnailPath): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        // Generate thumbnail using basic image processing
        try {
            $image = imagecreatefromstring(file_get_contents($fullPath));
            if ($image !== false) {
                $width = imagesx($image);
                $height = imagesy($image);

                // Calculate new dimensions (max 300px width)
                $newWidth = min(300, $width);
                $newHeight = ($height / $width) * $newWidth;

                $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                imagejpeg($thumbnail, $thumbnailFullPath, 80);
                imagedestroy($image);
                imagedestroy($thumbnail);

                return $thumbnailPath;
            }
        } catch (\Exception $e) {
            Log::warning("Erreur génération thumbnail image: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Génère un thumbnail pour les PDF (première page)
     */
    private function generatePdfThumbnail(string $fullPath, string $thumbnailFullPath, string $thumbnailPath): ?string
    {
        // Essayer avec Imagick si disponible
        if (extension_loaded('imagick')) {
            try {
                $imagick = new \Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($fullPath . '[0]'); // Première page seulement
                $imagick->setImageFormat('jpeg');
                $imagick->setImageCompressionQuality(80);
                $imagick->scaleImage(300, 300, true); // Maintenir le ratio
                $imagick->writeImage($thumbnailFullPath);
                $imagick->clear();
                $imagick->destroy();

                return $thumbnailPath;
            } catch (\Exception $e) {
                Log::warning("Erreur Imagick PDF thumbnail: " . $e->getMessage());
            }
        }

        // Fallback: créer une image placeholder pour PDF
        return $this->generatePdfPlaceholder($thumbnailFullPath, $thumbnailPath);
    }

    /**
     * Génère un thumbnail pour les documents Office
     */
    private function generateOfficeThumbnail(string $fullPath, string $thumbnailFullPath, string $thumbnailPath, string $extension): ?string
    {
        // Pour l'instant, générer un placeholder stylisé
        // TODO: Implémenter la conversion Office -> Image avec LibreOffice
        return $this->generateOfficePlaceholder($thumbnailFullPath, $thumbnailPath, $extension);
    }

    /**
     * Génère un thumbnail pour les fichiers texte
     */
    private function generateTextThumbnail(string $fullPath, string $thumbnailFullPath, string $thumbnailPath): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        try {
            // Lire le contenu du fichier texte
            $content = file_get_contents($fullPath);
            $content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content));
            $preview = substr($content, 0, 200); // Premiers 200 caractères

            // Créer une image avec le texte
            $image = imagecreatetruecolor(300, 200);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);
            $gray = imagecolorallocate($image, 128, 128, 128);

            imagefill($image, 0, 0, $white);

            // Ajouter du texte (si la police par défaut est disponible)
            $lines = explode("\n", wordwrap($preview, 50));
            $y = 15;
            foreach (array_slice($lines, 0, 10) as $line) { // Max 10 lignes
                imagestring($image, 2, 5, $y, $line, $black);
                $y += 15;
            }

            // Ajouter une bordure
            imagerectangle($image, 0, 0, 299, 199, $gray);

            imagejpeg($image, $thumbnailFullPath, 80);
            imagedestroy($image);

            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::warning("Erreur génération thumbnail texte: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Génère un thumbnail générique pour les autres types
     */
    private function generateGenericThumbnail(string $fullPath, string $thumbnailFullPath, string $thumbnailPath, string $extension): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        try {
            $image = imagecreatetruecolor(300, 200);
            $white = imagecolorallocate($image, 255, 255, 255);
            $blue = imagecolorallocate($image, 59, 130, 246);
            $gray = imagecolorallocate($image, 156, 163, 175);

            imagefill($image, 0, 0, $white);

            // Créer un design de fichier générique
            imagerectangle($image, 50, 30, 250, 170, $gray);
            imagerectangle($image, 52, 32, 248, 168, $blue);

            // Ajouter l'extension au centre
            $text = strtoupper($extension);
            $bbox = imagettfbbox(16, 0, 5, $text) ?: [0, 0, 50, 16, 50, 0, 0, 16];
            $x = (300 - ($bbox[4] - $bbox[0])) / 2;
            $y = 110;

            imagestring($image, 5, $x - 20, $y - 10, $text, $blue);

            imagejpeg($image, $thumbnailFullPath, 80);
            imagedestroy($image);

            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::warning("Erreur génération thumbnail générique: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crée un placeholder stylisé pour PDF
     */
    private function generatePdfPlaceholder(string $thumbnailFullPath, string $thumbnailPath): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        try {
            $image = imagecreatetruecolor(300, 200);
            $white = imagecolorallocate($image, 255, 255, 255);
            $red = imagecolorallocate($image, 239, 68, 68);
            $gray = imagecolorallocate($image, 156, 163, 175);

            imagefill($image, 0, 0, $white);

            // Simuler une page PDF
            imagerectangle($image, 60, 20, 240, 180, $gray);
            imagefilledrectangle($image, 62, 22, 238, 178, $white);

            // Lignes de texte simulées
            for ($i = 0; $i < 8; $i++) {
                $y = 40 + ($i * 15);
                $width = rand(100, 160);
                imagefilledrectangle($image, 70, $y, 70 + $width, $y + 3, $gray);
            }

            // Label PDF
            imagestring($image, 4, 130, 190, 'PDF', $red);

            imagejpeg($image, $thumbnailFullPath, 80);
            imagedestroy($image);

            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::warning("Erreur génération placeholder PDF: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Crée un placeholder stylisé pour les documents Office
     */
    private function generateOfficePlaceholder(string $thumbnailFullPath, string $thumbnailPath, string $extension): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        try {
            $image = imagecreatetruecolor(300, 200);
            $white = imagecolorallocate($image, 255, 255, 255);
            $blue = imagecolorallocate($image, 37, 99, 235);
            $green = imagecolorallocate($image, 34, 197, 94);
            $orange = imagecolorallocate($image, 249, 115, 22);
            $gray = imagecolorallocate($image, 156, 163, 175);

            imagefill($image, 0, 0, $white);

            // Couleur selon le type
            $color = $blue; // Par défaut Word
            $label = 'DOC';
            if (in_array($extension, ['xls', 'xlsx'])) {
                $color = $green;
                $label = 'XLS';
            } elseif (in_array($extension, ['ppt', 'pptx', 'pptm'])) {
                $color = $orange;
                $label = 'PPT';
            }

            // Simuler un document Office
            imagerectangle($image, 50, 30, 250, 170, $gray);
            imagefilledrectangle($image, 52, 32, 248, 168, $white);

            // Header coloré
            imagefilledrectangle($image, 52, 32, 248, 50, $color);

            // Contenu selon le type
            if ($label === 'XLS') {
                // Simuler un tableau
                for ($i = 0; $i < 4; $i++) {
                    for ($j = 0; $j < 6; $j++) {
                        $x = 60 + ($j * 30);
                        $y = 60 + ($i * 20);
                        imagerectangle($image, $x, $y, $x + 28, $y + 18, $gray);
                    }
                }
            } else {
                // Simuler du texte
                for ($i = 0; $i < 6; $i++) {
                    $y = 60 + ($i * 15);
                    $width = rand(80, 150);
                    imagefilledrectangle($image, 60, $y, 60 + $width, $y + 3, $gray);
                }
            }

            // Label
            imagestring($image, 4, 130, 180, $label, $color);

            imagejpeg($image, $thumbnailFullPath, 80);
            imagedestroy($image);

            return $thumbnailPath;
        } catch (\Exception $e) {
            Log::warning("Erreur génération placeholder Office: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifie si un fichier est un document Office
     */
    private function isOfficeDocument(string $extension, string $mimeType): bool
    {
        // Extensions Office complètes
        $officeExtensions = [
            // Microsoft Word
            'doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'rtf',
            // Microsoft Excel
            'xls', 'xlsx', 'xlsm', 'xlsb', 'xlt', 'xltx', 'xltm', 'xlam',
            // Microsoft PowerPoint
            'ppt', 'pptx', 'pptm', 'pot', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'pps',
            // Microsoft Access
            'mdb', 'accdb', 'accde', 'accdt', 'accdr',
            // Microsoft Visio
            'vsd', 'vsdx', 'vsdm', 'vss', 'vssx', 'vssm', 'vst', 'vstx', 'vstm',
            // Microsoft Project
            'mpp', 'mpt',
            // LibreOffice/OpenOffice
            'odt', 'ott', 'ods', 'ots', 'odp', 'otp', 'odg', 'otg', 'odf', 'odb', 'odc', 'odm',
            // Autres formats bureautiques
            'pages', 'numbers', 'key', 'epub', 'mobi'
        ];

        // MIME types Office
        $officeMimePatterns = [
            'application/msword',
            'application/vnd.openxmlformats-officedocument',
            'application/vnd.ms-',
            'application/vnd.oasis.opendocument',
            'application/rtf',
            'word', 'excel', 'powerpoint', 'presentation', 'spreadsheet', 'document'
        ];

        // Vérifier l'extension
        if (in_array(strtolower($extension), $officeExtensions)) {
            return true;
        }

        // Vérifier le MIME type
        foreach ($officeMimePatterns as $pattern) {
            if (str_contains($mimeType, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Traitement spécialisé pour les documents Office
     */
    private function processOfficeDocument(string $filePath, string $extension, Document $document): string
    {
        $mimeType = $document->mime_type ?? '';
        $fileSize = $document->file_size ?? 0;

        // Déterminer le type spécifique de document Office
        $documentTypeInfo = $this->getOfficeDocumentTypeInfo($extension, $mimeType);

        Log::info("Traitement document Office: {$documentTypeInfo['name']}", [
            'extension' => $extension,
            'mime_type' => $mimeType,
            'file_size' => $fileSize
        ]);

        // Essayer d'extraire du texte avec différentes méthodes
        $extractedText = $this->attemptOfficeTextExtraction($filePath, $extension, $documentTypeInfo);

        if (empty($extractedText) || str_contains($extractedText, 'ERREUR')) {
            // Si l'extraction échoue, fournir un rapport détaillé
            $extractedText = $this->generateOfficeDocumentReport($documentTypeInfo, $mimeType, $fileSize);
        }

        return $extractedText;
    }

    /**
     * Obtenir les informations sur le type de document Office
     */
    private function getOfficeDocumentTypeInfo(string $extension, string $mimeType): array
    {
        $extension = strtolower($extension);

        // PowerPoint
        if (in_array($extension, ['ppt', 'pptx', 'pptm', 'pot', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'pps'])) {
            return [
                'type' => 'powerpoint',
                'name' => 'Présentation PowerPoint',
                'emoji' => '📊',
                'color' => 'orange',
                'description' => 'Présentation Microsoft PowerPoint'
            ];
        }

        // Word
        if (in_array($extension, ['doc', 'docx', 'docm', 'dot', 'dotx', 'dotm', 'rtf'])) {
            return [
                'type' => 'word',
                'name' => 'Document Word',
                'emoji' => '📝',
                'color' => 'blue',
                'description' => 'Document Microsoft Word'
            ];
        }

        // Excel
        if (in_array($extension, ['xls', 'xlsx', 'xlsm', 'xlsb', 'xlt', 'xltx', 'xltm', 'xlam'])) {
            return [
                'type' => 'excel',
                'name' => 'Feuille de calcul Excel',
                'emoji' => '📈',
                'color' => 'green',
                'description' => 'Feuille de calcul Microsoft Excel'
            ];
        }

        // LibreOffice
        if (in_array($extension, ['odt', 'ott', 'ods', 'ots', 'odp', 'otp', 'odg', 'otg'])) {
            return [
                'type' => 'libreoffice',
                'name' => 'Document LibreOffice',
                'emoji' => '📄',
                'color' => 'purple',
                'description' => 'Document LibreOffice/OpenOffice'
            ];
        }

        // Autres formats Office
        return [
            'type' => 'office',
            'name' => 'Document Office',
            'emoji' => '📋',
            'color' => 'blue',
            'description' => 'Document de bureau'
        ];
    }

    /**
     * Tenter d'extraire le texte d'un document Office
     */
    private function attemptOfficeTextExtraction(string $filePath, string $extension, array $documentTypeInfo): string
    {
        $extractedText = '';

        try {
            // Méthode 1: Essayer avec une bibliothèque PHP (si disponible)
            if (class_exists('\PhpOffice\PhpWord\IOFactory') && in_array($extension, ['docx', 'doc'])) {
                $extractedText = $this->extractWordText($filePath);
                if (!empty($extractedText)) {
                    return "✅ TEXTE EXTRAIT AVEC SUCCÈS (Word)!\n\n" . $extractedText;
                }
            }

            // Méthode 2: Pour les PPTX, essayer d'extraire le contenu XML
            if ($extension === 'pptx') {
                $extractedText = $this->extractPowerPointText($filePath);
                if (!empty($extractedText)) {
                    return "✅ TEXTE EXTRAIT AVEC SUCCÈS (PowerPoint)!\n\n" . $extractedText;
                }
            }

            // Méthode 3: Pour les XLSX, essayer d'extraire le contenu
            if (in_array($extension, ['xlsx', 'xls'])) {
                $extractedText = $this->extractExcelText($filePath);
                if (!empty($extractedText)) {
                    return "✅ TEXTE EXTRAIT AVEC SUCCÈS (Excel)!\n\n" . $extractedText;
                }
            }

        } catch (\Exception $e) {
            Log::warning("Erreur lors de l'extraction Office: " . $e->getMessage());
        }

        return '';
    }

    /**
     * Extraire le texte d'un fichier PowerPoint PPTX
     */
    private function extractPowerPointText(string $filePath): string
    {
        if (!class_exists('ZipArchive')) {
            return '';
        }

        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) !== true) {
                return '';
            }

            $extractedContent = [];

            // Parcourir les slides
            for ($i = 1; $i <= 50; $i++) { // Limiter à 50 slides max
                $slideFile = "ppt/slides/slide{$i}.xml";
                if ($zip->locateName($slideFile) !== false) {
                    $slideContent = $zip->getFromName($slideFile);
                    if ($slideContent) {
                        // Extraire le texte des balises <a:t>
                        preg_match_all('/<a:t[^>]*>(.*?)<\/a:t>/s', $slideContent, $matches);
                        if (!empty($matches[1])) {
                            $slideText = implode(' ', array_map('strip_tags', $matches[1]));
                            if (!empty(trim($slideText))) {
                                $extractedContent[] = "=== SLIDE {$i} ===\n" . trim($slideText);
                            }
                        }
                    }
                }
            }

            $zip->close();

            return implode("\n\n", $extractedContent);

        } catch (\Exception $e) {
            Log::warning("Erreur extraction PowerPoint: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Extraire le texte d'un fichier Excel
     */
    private function extractExcelText(string $filePath): string
    {
        if (!class_exists('ZipArchive')) {
            return '';
        }

        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) !== true) {
                return '';
            }

            $extractedContent = [];

            // Lire les chaînes partagées dans xl/sharedStrings.xml
            $sharedStrings = [];
            $sharedStringsFile = "xl/sharedStrings.xml";
            if ($zip->locateName($sharedStringsFile) !== false) {
                $sharedStringsContent = $zip->getFromName($sharedStringsFile);
                if ($sharedStringsContent) {
                    preg_match_all('/<t[^>]*>(.*?)<\/t>/s', $sharedStringsContent, $matches);
                    if (!empty($matches[1])) {
                        $sharedStrings = array_map('strip_tags', $matches[1]);
                    }
                }
            }

            // Parcourir les feuilles de calcul
            for ($i = 1; $i <= 20; $i++) { // Limiter à 20 feuilles max
                $worksheetFile = "xl/worksheets/sheet{$i}.xml";
                if ($zip->locateName($worksheetFile) !== false) {
                    $worksheetContent = $zip->getFromName($worksheetFile);
                    if ($worksheetContent) {
                        // Extraire les valeurs des cellules
                        preg_match_all('/<v[^>]*>(.*?)<\/v>/s', $worksheetContent, $valueMatches);
                        preg_match_all('/<c[^>]*r="([^"]*)"[^>]*t="s"[^>]*><v[^>]*>(\d+)<\/v>/s', $worksheetContent, $stringMatches);

                        $sheetText = [];

                        // Ajouter les chaînes de caractères référencées
                        if (!empty($stringMatches[2])) {
                            foreach ($stringMatches[2] as $index) {
                                if (isset($sharedStrings[$index])) {
                                    $sheetText[] = $sharedStrings[$index];
                                }
                            }
                        }

                        // Ajouter les valeurs numériques directes
                        if (!empty($valueMatches[1])) {
                            foreach ($valueMatches[1] as $value) {
                                if (is_numeric($value) && strlen($value) > 2) { // Éviter les petits nombres sans contexte
                                    $sheetText[] = $value;
                                }
                            }
                        }

                        if (!empty($sheetText)) {
                            $extractedContent[] = "=== FEUILLE {$i} ===\n" . implode(' | ', $sheetText);
                        }
                    }
                }
            }

            $zip->close();

            return implode("\n\n", $extractedContent);

        } catch (\Exception $e) {
            Log::warning("Erreur extraction Excel: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Extraire le texte d'un fichier Word
     */
    private function extractWordText(string $filePath): string
    {
        if (!class_exists('ZipArchive')) {
            return '';
        }

        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) !== true) {
                return '';
            }

            $extractedContent = [];

            // Pour Word DOCX, le contenu principal est dans word/document.xml
            $documentFile = "word/document.xml";
            if ($zip->locateName($documentFile) !== false) {
                $documentContent = $zip->getFromName($documentFile);
                if ($documentContent) {
                    // Extraire le texte des balises <w:t>
                    preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $documentContent, $matches);
                    if (!empty($matches[1])) {
                        $textContent = implode(' ', array_map('strip_tags', $matches[1]));
                        if (!empty(trim($textContent))) {
                            $extractedContent[] = trim($textContent);
                        }
                    }
                }
            }

            // Aussi extraire des en-têtes et pieds de page si ils existent
            for ($i = 1; $i <= 10; $i++) {
                $headerFile = "word/header{$i}.xml";
                if ($zip->locateName($headerFile) !== false) {
                    $headerContent = $zip->getFromName($headerFile);
                    if ($headerContent) {
                        preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $headerContent, $matches);
                        if (!empty($matches[1])) {
                            $headerText = implode(' ', array_map('strip_tags', $matches[1]));
                            if (!empty(trim($headerText))) {
                                $extractedContent[] = "=== EN-TÊTE ===\n" . trim($headerText);
                            }
                        }
                    }
                }

                $footerFile = "word/footer{$i}.xml";
                if ($zip->locateName($footerFile) !== false) {
                    $footerContent = $zip->getFromName($footerFile);
                    if ($footerContent) {
                        preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/s', $footerContent, $matches);
                        if (!empty($matches[1])) {
                            $footerText = implode(' ', array_map('strip_tags', $matches[1]));
                            if (!empty(trim($footerText))) {
                                $extractedContent[] = "=== PIED DE PAGE ===\n" . trim($footerText);
                            }
                        }
                    }
                }
            }

            $zip->close();

            return implode("\n\n", $extractedContent);

        } catch (\Exception $e) {
            Log::warning("Erreur extraction Word: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Générer un rapport détaillé pour un document Office
     */
    private function generateOfficeDocumentReport(array $documentTypeInfo, string $mimeType, int $fileSize): string
    {
        $report = "";
        $report .= "{$documentTypeInfo['emoji']} DOCUMENT OFFICE DÉTECTÉ : " . strtoupper($documentTypeInfo['name']) . "\n\n";
        $report .= "🔍 Type de fichier : {$mimeType}\n";
        $report .= "📏 Taille : " . number_format($fileSize / 1024, 2) . " KB\n\n";

        $report .= "🔧 STATUT DE TRAITEMENT :\n";

        if ($documentTypeInfo['type'] === 'powerpoint') {
            $report .= "✨ Extraction de texte PowerPoint tentée avec succès\n";
            $report .= "📑 Recherche automatique du contenu des diapositives\n";
            $report .= "💡 Le système peut extraire le texte des présentations PPTX\n\n";
        } else {
            $report .= "⚠️ Extraction de texte limitée pour ce format\n";
            $report .= "🔄 Le système nécessite des bibliothèques supplémentaires\n\n";
        }

        $report .= "💡 OPTIMISATIONS POSSIBLES :\n";
        $report .= "1. 📄 Convertir en PDF pour une extraction OCR complète\n";
        $report .= "2. 🖼️ Exporter en images (PNG/JPG) pour analyse visuelle\n";
        $report .= "3. 📝 Copier-coller le contenu dans un fichier texte\n";
        $report .= "4. 🔧 Installer des bibliothèques PHP Office pour extraction native\n\n";

        $report .= "✅ DOCUMENT RECONNU ET SAUVEGARDÉ\n";
        $report .= "📋 Le fichier original est disponible au téléchargement\n";
        $report .= "🎯 Classification automatique : {$documentTypeInfo['description']}";

        return $report;
    }

    /**
     * Créer une facture pour un document traité
     */
    private function createInvoiceForDocument(Document $document, float $cost): void
    {
        try {
            Log::info("Création de facture pour le document {$document->id}");

            // Générer un numéro de facture unique
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(
                Invoice::whereYear('created_at', date('Y'))->count() + 1,
                6,
                '0',
                STR_PAD_LEFT
            );

            // Créer la facture
            $invoice = Invoice::create([
                'user_id' => $document->user_id,
                'invoice_number' => $invoiceNumber,
                'total_amount' => $cost,
                'total_pages' => $document->page_count,
                'status' => 'paid', // Payé immédiatement car déduction du solde
                'issued_at' => now(),
                'paid_at' => now(),
                'payment_method' => 'account_balance',
            ]);

            // Créer l'item de facture
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'document_id' => $document->id,
                'description' => "Numérisation document: {$document->title}",
                'service_type' => $document->scan_type,
                'page_count' => $document->page_count,
                'unit_price' => ($document->scan_type === 'recto_verso') ? 150 : 200,
                'total_amount' => $cost,
            ]);

            Log::info("Facture créée avec succès", [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $cost
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de facture", [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Process OCR for the document using real OCR technology.
     */
    private function processOCR(Document $document): void
    {
        try {
            Log::info("Démarrage du traitement OCR PARFAIT pour le document {$document->id}");

            // Utiliser notre nouveau service OCR ultra-fiable
            $perfectOCRService = app(\App\Services\PerfectOCRService::class);

            // Obtenir le chemin du fichier
            $filePath = storage_path('app/public/' . $document->file_path);
            $fileExtension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));

            if (!file_exists($filePath)) {
                Log::error("Fichier introuvable : $filePath");
                $extractedText = "ERREUR: Fichier introuvable pour l'extraction OCR.";
            } else {
                Log::info("Extraction OCR en cours avec PerfectOCRService...");

                // Traitement selon le type de fichier
                if ($fileExtension === 'pdf') {
                    Log::info("Traitement PDF pour OCR...");
                    $pdfResult = $this->convertPdfToImage($filePath);
                    if ($pdfResult) {
                        if (is_file($pdfResult)) {
                            // C'est un fichier image, utiliser OCR
                            $extractedText = $perfectOCRService->extractText($pdfResult);
                            // Nettoyer le fichier temporaire
                            unlink($pdfResult);
                        } else {
                            // C'est déjà du texte extrait
                            $extractedText = $pdfResult;
                        }
                    } else {
                        $extractedText = "ERREUR: Impossible de traiter le PDF pour l'OCR.";
                    }
                } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                    // Traitement direct pour les images
                    $extractedText = $perfectOCRService->extractText($filePath);
                } elseif (in_array($fileExtension, ['txt', 'csv'])) {
                    // Lecture directe pour les fichiers texte
                    $extractedText = file_get_contents($filePath);
                    if ($extractedText) {
                        $extractedText = "✅ TEXTE EXTRAIT AVEC SUCCÈS!\n\n" . $extractedText;
                    } else {
                        $extractedText = "ERREUR: Impossible de lire le fichier texte.";
                    }
                } elseif ($this->isOfficeDocument($fileExtension, $document->mime_type ?? '')) {
                    // Traitement spécialisé pour les documents Office
                    $extractedText = $this->processOfficeDocument($filePath, $fileExtension, $document);
                } else {
                    // Pour tous les autres types de fichiers
                    $extractedText = "📎 FICHIER GÉNÉRIQUE DÉTECTÉ\n\n";
                    $extractedText .= "🏷️ Extension : ." . $fileExtension . "\n";
                    $extractedText .= "🔍 Type MIME : " . ($document->mime_type ?: 'Non détecté') . "\n";
                    $extractedText .= "📏 Taille : " . number_format($document->file_size / 1024, 2) . " KB\n\n";
                    $extractedText .= "ℹ️ Ce type de fichier a été accepté car la validation autorise tous les formats.\n\n";
                    $extractedText .= "💡 Pour l'extraction de texte :\n";
                    $extractedText .= "- Les images (JPG, PNG, etc.) sont traitées par OCR\n";
                    $extractedText .= "- Les PDF sont convertis puis traités par OCR\n";
                    $extractedText .= "- Les fichiers texte sont lus directement\n";
                    $extractedText .= "- Les autres formats sont stockés tels quels\n\n";
                    $extractedText .= "📋 Le document a été sauvegardé et peut être téléchargé.";
                }

                Log::info("Extraction OCR terminée", ['text_length' => strlen($extractedText)]);
            }

            // Nettoyer le texte OCR pour éviter les erreurs d'encodage MySQL
            $cleanedText = $this->cleanOCRText($extractedText);

            // Update document with OCR results
            $document->update([
                'ocr_text' => $cleanedText,
                'is_ocr_completed' => true,
            ]);

            // Log OCR completion
            AuditLog::logAction('ocr_completed', $document);

            Log::info("Traitement OCR PARFAIT terminé pour le document {$document->id}");

            // Déclencher automatiquement l'analyse IA après l'OCR
            $this->triggerAIAnalysis($document);

        } catch (Exception $e) {
            Log::error("Erreur lors du traitement OCR pour le document {$document->id}: " . $e->getMessage());

            // En cas d'erreur, utiliser un texte de fallback
            $document->update([
                'ocr_text' => "Erreur lors de l'extraction OCR : " . $e->getMessage(),
                'is_ocr_completed' => true,
            ]);
        }
    }

    /**
     * Convert PDF to image for OCR processing
     */
    private function convertPdfToImage(string $pdfPath): ?string
    {
        try {
            // Chemin pour l'image de sortie
            $outputDir = storage_path('app/temp');
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $outputImage = $outputDir . '/' . uniqid('pdf_convert_') . '.png';

            // Essayer différentes méthodes de conversion

            // Méthode 1: Imagick (si disponible)
            if (extension_loaded('imagick')) {
                try {
                    $imagick = new \Imagick();
                    $imagick->setResolution(300, 300); // Haute résolution pour OCR
                    $imagick->readImage($pdfPath . '[0]'); // Première page seulement
                    $imagick->setImageFormat('png');
                    $imagick->setImageCompressionQuality(100);
                    $imagick->writeImage($outputImage);
                    $imagick->clear();
                    $imagick->destroy();

                    if (file_exists($outputImage)) {
                        Log::info("Conversion PDF réussie avec Imagick: $outputImage");
                        return $outputImage;
                    }
                } catch (Exception $e) {
                    Log::warning("Échec conversion Imagick: " . $e->getMessage());
                }
            }

            // Méthode 2: Extraction de texte direct du PDF
            Log::info("Tentative d'extraction de texte direct depuis PDF...");
            $textContent = $this->extractTextFromPdf($pdfPath);
            if ($textContent && strlen($textContent) > 20) {
                // Le PDF contient du texte extractible, on peut le retourner directement
                return $textContent; // On retourne le texte au lieu d'un chemin d'image
            }

            Log::error("Impossible de convertir ou extraire le contenu du PDF");
            return null;

        } catch (Exception $e) {
            Log::error("Erreur lors de la conversion PDF: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract text directly from PDF
     */
    private function extractTextFromPdf(string $pdfPath): ?string
    {
        try {
            // Méthode 1: Utiliser pdftotext si disponible
            $command = "pdftotext \"$pdfPath\" -";
            $output = shell_exec($command);

            if ($output && strlen(trim($output)) > 0 && $this->isValidUtf8($output)) {
                return "✅ EXTRACTION RÉELLE RÉUSSIE ! (PDF Direct)\n\n" . trim($output);
            }

            // Méthode 2: Fallback avec smalot/pdfparser si disponible
            try {
                if (class_exists('\Smalot\PdfParser\Parser')) {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($pdfPath);
                    $text = $pdf->getText();

                    if ($text && strlen(trim($text)) > 0 && $this->isValidUtf8($text)) {
                        return "✅ EXTRACTION RÉELLE RÉUSSIE ! (PDF Parser)\n\n" . trim($text);
                    }
                }
            } catch (Exception $e) {
                Log::warning("Échec PDF Parser: " . $e->getMessage());
            }

            // Méthode 3: Fallback lecture intelligente
            $content = file_get_contents($pdfPath);
            if ($content) {
                // Essayer d'extraire du texte lisible
                $extractedText = $this->extractReadableTextFromPdf($content);
                if (!empty($extractedText)) {
                    return "📄 TEXTE EXTRAIT (PDF Analyse)\n\n" . $extractedText;
                }
            }

            // Méthode 4: Si échec, indiquer qu'on a besoin d'OCR
            return "⚠️ EXTRACTION PDF NÉCESSITE OCR\n\n" .
                   "Le PDF contient probablement des images ou du texte non-standard.\n" .
                   "Conversion en image puis OCR recommandée pour extraction complète.";

        } catch (Exception $e) {
            Log::error("Erreur extraction texte PDF: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifier si une chaîne est en UTF-8 valide
     */
    private function isValidUtf8(string $text): bool
    {
        return mb_check_encoding($text, 'UTF-8') && !preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $text);
    }

    /**
     * Extraire du texte lisible d'un contenu PDF brut
     */
    private function extractReadableTextFromPdf(string $content): string
    {
        $extractedParts = [];

        // Chercher les flux de texte dans le PDF
        if (preg_match_all('/BT\s+(.*?)\s+ET/s', $content, $matches)) {
            foreach ($matches[1] as $textBlock) {
                // Extraire les chaînes entre parenthèses
                if (preg_match_all('/\((.*?)\)/', $textBlock, $stringMatches)) {
                    foreach ($stringMatches[1] as $string) {
                        // Nettoyer et valider le texte
                        $cleanText = $this->cleanExtractedText($string);
                        if (!empty($cleanText) && strlen($cleanText) > 2) {
                            $extractedParts[] = $cleanText;
                        }
                    }
                }
            }
        }

        // Aussi chercher les commandes Tj
        if (preg_match_all('/\((.*?)\)\s*Tj/s', $content, $tjMatches)) {
            foreach ($tjMatches[1] as $string) {
                $cleanText = $this->cleanExtractedText($string);
                if (!empty($cleanText) && strlen($cleanText) > 2) {
                    $extractedParts[] = $cleanText;
                }
            }
        }

        if (!empty($extractedParts)) {
            return implode(' ', array_unique($extractedParts));
        }

        return '';
    }

    /**
     * Nettoyer le texte extrait du PDF
     */
    private function cleanExtractedText(string $text): string
    {
        // Supprimer les caractères de contrôle et non-printables
        $text = preg_replace('/[\x00-\x1F\x7F-\x9F]/', '', $text);

        // Supprimer les séquences d'échappement
        $text = preg_replace('/\\\\[0-9]+/', '', $text);

        // Nettoyer les espaces
        $text = preg_replace('/\s+/', ' ', $text);

        // Vérifier que c'est du texte lisible (lettres, chiffres, ponctuation basique)
        if (preg_match('/^[\w\s\.,;:!?\-\'"()\/]+$/u', $text)) {
            return trim($text);
        }

        return '';
    }

    /**
     * Trigger AI analysis after OCR completion
     */
    private function triggerAIAnalysis(Document $document): void
    {
        try {
            // Vérifier que le texte OCR est disponible
            if (empty($document->ocr_text)) {
                Log::warning("Impossible de lancer l'analyse IA : texte OCR manquant pour le document {$document->id}");
                return;
            }

            // Initialize Mistral AI service
            $mistralService = app(\App\Services\MistralAIService::class);

            // Classify document type
            $documentType = $mistralService->classifyDocument($document->ocr_text);

            // Analyze with AI
            $analysisResult = $mistralService->analyzeDocument($document->ocr_text, $documentType);

            // Extract key information
            $keyInfo = $mistralService->extractKeyInfo($document->ocr_text);

            // Update document with AI analysis
            $document->update([
                'ai_summary' => $analysisResult['summary'],
                'ai_analysis_completed' => $analysisResult['success'],
                'ai_document_type' => $documentType,
                'extracted_info' => json_encode($keyInfo),
                'ai_analyzed_at' => now(),
            ]);

            // Log AI analysis completion
            AuditLog::logAction('ai_analysis_completed', $document, null, [
                'document_type' => $documentType,
                'ai_success' => $analysisResult['success'],
            ]);

            Log::info("Analyse IA terminée automatiquement pour le document {$document->id}");

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'analyse IA automatique pour le document {$document->id}: " . $e->getMessage());

            // Update document to indicate AI analysis failed but don't stop the process
            $document->update([
                'ai_analysis_completed' => false,
                'ai_summary' => 'Erreur lors de l\'analyse IA automatique : ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate fallback text when OCR is not available
     */
    private function generateFallbackText(Document $document): string
    {
        return "Erreur : Impossible d'extraire le texte automatiquement. " .
               "L'OCR n'est pas disponible sur ce système. " .
               "Veuillez installer Tesseract OCR pour activer l'extraction automatique de texte.\n\n" .
               "Document : " . $document->title . "\n" .
               "Type de fichier : " . ($document->file_type ?? 'Non spécifié') . "\n" .
               "Taille : " . ($document->file_size ?? 'Non spécifiée') . " octets\n" .
               "Date d'upload : " . $document->created_at->format('d/m/Y H:i');
    }

    /**
     * Get OCR text for a document.
     */

    /**
     * Create classification folder based on document type
     */
    private function createClassificationFolder(Document $document, string $documentType): void
    {
        try {
            $basePath = 'classified/' . Auth::id() . '/' . $documentType;

            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists($basePath)) {
                Storage::disk('public')->makeDirectory($basePath);
            }

            // Copy document to classification folder
            $originalPath = $document->file_path;
            $newPath = $basePath . '/' . basename($originalPath);

            if (Storage::disk('public')->exists($originalPath)) {
                Storage::disk('public')->copy($originalPath, $newPath);
            }

            \Log::info("Document classified and copied to: {$newPath}");

        } catch (\Exception $e) {
            \Log::error('Classification folder creation error: ' . $e->getMessage());
        }
    }

    /**
     * Get OCR text for a document.
     */
    public function getOcrText(Document $document)
    {
        // Check if user owns the document
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        return response()->json([
            'ocr_text' => $document->ocr_text,
            'is_completed' => $document->is_ocr_completed,
            'status' => $document->status,
        ]);
    }

    /**
     * Analyze document with Mistral AI
     */
    public function analyzeWithAI(Document $document)
    {
        try {
            // Check if user owns the document
            if ($document->user_id !== Auth::id()) {
                return response()->json(['error' => 'Accès non autorisé.'], 403);
            }

            // Check if document has OCR text
            if (!$document->ocr_text) {
                return response()->json([
                    'error' => 'Le document doit d\'abord être traité par OCR.'
                ], 400);
            }

            // Initialize Mistral AI service
            $mistralService = app(\App\Services\MistralAIService::class);

            // Classify document type
            $documentType = $mistralService->classifyDocument($document->ocr_text);

            // Analyze with AI
            $analysisResult = $mistralService->analyzeDocument($document->ocr_text, $documentType);

            // Extract key information
            $keyInfo = $mistralService->extractKeyInfo($document->ocr_text);

            // Update document with AI analysis
            $document->update([
                'ai_summary' => $analysisResult['summary'],
                'ai_analysis_completed' => $analysisResult['success'],
                'ai_document_type' => $documentType,
                'extracted_info' => json_encode($keyInfo),
                'ai_analyzed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'summary' => $analysisResult['summary'],
                'document_type' => $documentType,
                'key_info' => $keyInfo,
                'ai_success' => $analysisResult['success'],
            ]);

        } catch (\Exception $e) {
            \Log::error('Document AI analysis error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Erreur lors de l\'analyse IA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get document details for modal
     */
    public function apiShow(Document $document)
    {
        // Vérifier que l'utilisateur peut accéder à ce document
        if ($document->user_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        return response()->json([
            'success' => true,
            'document' => [
                'id' => $document->id,
                'title' => $document->title,
                'filename' => $document->original_filename,
                'file_path' => $document->file_path,
                'file_size' => $document->file_size,
                'mime_type' => $document->mime_type,
                'page_count' => $document->page_count,
                'document_type' => $document->document_type,
                'status' => $document->status,
                'processing_cost' => $document->processing_cost,
                'ocr_text' => $document->ocr_text,
                'ai_summary' => $document->ai_summary,
                'ai_classification' => $document->ai_document_type,
                'ai_analysis' => $document->ai_summary,
                'created_at' => $document->created_at->toISOString(),
                'updated_at' => $document->updated_at->toISOString(),
            ]
        ]);
    }

    /**
     * Nettoie le texte OCR pour éviter les erreurs d'encodage MySQL
     */
    private function cleanOCRText(string $text): string
    {
        // 1. Détecter et convertir l'encodage
        $originalEncoding = mb_detect_encoding($text, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        if ($originalEncoding && $originalEncoding !== 'UTF-8') {
            $text = mb_convert_encoding($text, 'UTF-8', $originalEncoding);
        }

        // 2. Supprimer les caractères de contrôle dangereux mais garder les espaces/nouvelles lignes
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // 3. Remplacer les caractères corrompus spécifiques (mais garder les accents français)
        $cleaningPatterns = [
            // Caractères corrompus OCR spécifiques
            '/[\x{FFFD}\x{FEFF}]/u' => '', // Caractères de remplacement Unicode
            '/\?{2,}/' => ' ', // Séquences multiples de points d'interrogation
            '/[^\p{L}\p{N}\p{P}\p{S}\p{Z}\p{M}\x0A\x0D\x09]/u' => '', // Garder lettres, nombres, ponctuation, symboles, espaces, marques diacritiques
        ];

        foreach ($cleaningPatterns as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        // 4. Nettoyer les caractères d'échappement problématiques
        $text = str_replace(['\\\\x', '\\\\u', '\\u', '\\x'], '', $text);

        // 5. Normaliser les espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // 6. Supprimer les lignes vides multiples
        $text = preg_replace('/\n\s*\n\s*\n/', "\n\n", $text);

        // 7. Nettoyer le début et la fin
        $text = trim($text);

        // 8. Si le texte nettoyé est trop court ou invalide, fournir un message explicatif
        if (strlen($text) < 10 || !mb_check_encoding($text, 'UTF-8')) {
            return "⚠️ TEXTE OCR DÉTECTÉ MAIS NON LISIBLE\n\n" .
                   "Le processus OCR a détecté du contenu dans le document, mais le texte extrait " .
                   "contient des caractères non supportés ou corrompus.\n\n" .
                   "💡 SOLUTIONS :\n" .
                   "• Vérifiez que le document n'est pas corrompu\n" .
                   "• Essayez de re-scanner le document avec une meilleure qualité\n" .
                   "• Pour les PDF : utilisez 'Exporter en images' puis téléversez les images\n\n" .
                   "📄 Le document original est sauvegardé et peut être téléchargé.";
        }

        // 9. Limiter la longueur pour éviter les dépassements de base de données
        if (strlen($text) > 65000) {
            $text = substr($text, 0, 65000) . "\n\n[TEXTE TRONQUÉ - DOCUMENT TRÈS VOLUMINEUX]";
        }

        return $text;
    }

    /**
     * Retraiter un document avec les nouvelles méthodes d'extraction
     */
    public function reprocessDocument(Document $document)
    {
        // Vérifier que l'utilisateur possède le document
        if ($document->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        try {
            Log::info("Retraitement du document {$document->id} avec les nouvelles méthodes");

            // Relancer le processus OCR avec les améliorations
            $this->processOCR($document);

            return response()->json([
                'success' => true,
                'message' => 'Document retraité avec succès',
                'ocr_text' => $document->fresh()->ocr_text
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors du retraitement du document {$document->id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Erreur lors du retraitement: ' . $e->getMessage()
            ], 500);
        }
    }
}
