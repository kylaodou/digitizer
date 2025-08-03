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
            'file' => ['required', 'file', 'mimes:pdf,jpeg,jpg,png', 'max:10240'], // 10MB max
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

        // Generate thumbnail for images
        $thumbnailPath = null;
        if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            $thumbnailPath = $this->generateThumbnail($filePath);
        }

        // Create document record
        $document = Document::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'thumbnail_path' => $thumbnailPath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'page_count' => $pageCount,
            'scan_type' => $request->document_type, // Renommé pour éviter confusion avec AI document_type
            'processing_cost' => $processingCost,
            'status' => 'uploaded',
        ]);

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
     * Generate thumbnail for image files.
     */
    private function generateThumbnail(string $filePath): string
    {
        $thumbnailPath = 'thumbnails/' . Str::uuid() . '.jpg';
        $fullPath = storage_path('app/public/' . $filePath);
        $thumbnailFullPath = storage_path('app/public/' . $thumbnailPath);

        // Create thumbnail directory if it doesn't exist
        $thumbnailDir = dirname($thumbnailFullPath);
        if (!is_dir($thumbnailDir)) {
            mkdir($thumbnailDir, 0755, true);
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
            // If thumbnail generation fails, return null
            return null;
        }

        return null;
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

            // Obtenir le chemin du fichier image
            $imagePath = storage_path('app/public/' . $document->file_path);

            if (!file_exists($imagePath)) {
                Log::error("Fichier image introuvable : $imagePath");
                $extractedText = "ERREUR: Fichier image introuvable pour l'extraction OCR.";
            } else {
                Log::info("Extraction OCR en cours avec PerfectOCRService...");
                // Utiliser notre service OCR parfait
                $extractedText = $perfectOCRService->extractText($imagePath);
                Log::info("Extraction OCR terminée", ['text_length' => strlen($extractedText)]);
            }

            // Update document with OCR results
            $document->update([
                'ocr_text' => $extractedText,
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
}
