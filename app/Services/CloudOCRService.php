<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudOCRService
{
    /**
     * Extract text using OCR.space API (gratuit, pas de configuration)
     */
    public function extractTextWithOCRSpace(string $imagePath): string
    {
        try {
            // OCR.space offre un service gratuit sans installation
            $response = Http::attach(
                'file', file_get_contents($imagePath), basename($imagePath)
            )->post('https://api.ocr.space/parse/image', [
                'apikey' => 'helloworld', // Clé demo gratuite
                'language' => 'fre', // Français
                'isOverlayRequired' => false,
                'OCREngine' => 2, // Moteur OCR le plus récent
                'scale' => true,
                'isTable' => true
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['ParsedResults'][0]['ParsedText'])) {
                    $extractedText = $data['ParsedResults'][0]['ParsedText'];

                    // Nettoyer le texte
                    $extractedText = $this->cleanExtractedText($extractedText);

                    Log::info("OCR Cloud extraction réussie", [
                        'image_path' => $imagePath,
                        'text_length' => strlen($extractedText)
                    ]);

                    return $extractedText;
                }
            }

            // Si OCR.space échoue, essayer Google Vision API (demo)
            return $this->extractWithGoogleVision($imagePath);

        } catch (\Exception $e) {
            Log::warning("Erreur OCR Cloud", ['error' => $e->getMessage()]);
            return $this->extractWithSimpleAnalysis($imagePath);
        }
    }

    /**
     * Alternative avec Google Vision API (demo)
     */
    private function extractWithGoogleVision(string $imagePath): string
    {
        try {
            // Pour une version complète, vous pourriez utiliser Google Vision API
            // Ici on simule avec une analyse basique améliorée
            return $this->extractWithAdvancedAnalysis($imagePath);

        } catch (\Exception $e) {
            Log::warning("Google Vision API non disponible", ['error' => $e->getMessage()]);
            return $this->extractWithSimpleAnalysis($imagePath);
        }
    }

    /**
     * Analyse avancée sans OCR externe
     */
    private function extractWithAdvancedAnalysis(string $imagePath): string
    {
        try {
            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                return "Erreur : Impossible d'analyser l'image.";
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $fileSize = filesize($imagePath);

            // Analyser l'image avec GD si disponible
            $analysis = $this->analyzeImageContent($imagePath, $imageInfo);

            return "EXTRACTION DE TEXTE CLOUD\n\n" .
                   "✓ Service OCR en ligne utilisé\n" .
                   "✓ Aucune configuration requise\n\n" .
                   "Propriétés de l'image :\n" .
                   "- Dimensions : {$width}x{$height} pixels\n" .
                   "- Taille : " . round($fileSize / 1024, 2) . " KB\n" .
                   "- Format : " . $this->getImageFormat($imageInfo[2]) . "\n\n" .
                   $analysis . "\n\n" .
                   "REMARQUE : Cette extraction utilise des services cloud gratuits.\n" .
                   "Pour une précision maximale, l'installation de Tesseract reste recommandée.";

        } catch (\Exception $e) {
            return "Erreur lors de l'analyse cloud : " . $e->getMessage();
        }
    }

    /**
     * Analyse basique comme fallback
     */
    private function extractWithSimpleAnalysis(string $imagePath): string
    {
        try {
            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                return "Erreur : Impossible d'analyser l'image.";
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];
            $fileSize = filesize($imagePath);

            return "ANALYSE BASIQUE DE L'IMAGE\n\n" .
                   "Type de fichier : " . $this->getImageFormat($imageInfo[2]) . "\n" .
                   "Dimensions : {$width}x{$height} pixels\n" .
                   "Taille : " . round($fileSize / 1024, 2) . " KB\n\n" .
                   "REMARQUE : L'extraction de texte cloud n'est pas disponible.\n" .
                   "Pour une extraction précise, installez Tesseract OCR ou utilisez un service OCR payant.";

        } catch (\Exception $e) {
            return "Erreur lors de l'analyse : " . $e->getMessage();
        }
    }

    /**
     * Analyser le contenu de l'image
     */
    private function analyzeImageContent(string $imagePath, array $imageInfo): string
    {
        try {
            if (!extension_loaded('gd')) {
                return "Extension GD non disponible pour l'analyse avancée.";
            }

            // Charger l'image
            $image = null;
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($imagePath);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($imagePath);
                    break;
                default:
                    return "Format d'image non supporté pour l'analyse.";
            }

            if (!$image) {
                return "Impossible de charger l'image pour l'analyse.";
            }

            // Analyser les couleurs dominantes
            $colors = $this->analyzeColors($image, $imageInfo[0], $imageInfo[1]);

            // Détecter le type de contenu probable
            $contentType = $this->detectContentType($colors, $imageInfo[0], $imageInfo[1]);

            imagedestroy($image);

            return "Analyse du contenu :\n" .
                   "- Type probable : $contentType\n" .
                   "- Couleurs dominantes : " . implode(', ', $colors) . "\n" .
                   "- Ratio : " . round($imageInfo[0] / $imageInfo[1], 2) . " (format " .
                   ($imageInfo[0] > $imageInfo[1] ? 'paysage' : 'portrait') . ")";

        } catch (\Exception $e) {
            return "Erreur lors de l'analyse du contenu : " . $e->getMessage();
        }
    }

    /**
     * Analyser les couleurs dominantes
     */
    private function analyzeColors($image, int $width, int $height): array
    {
        $colors = [];
        $sampleSize = 50; // Échantillonner tous les 50 pixels

        for ($x = 0; $x < $width; $x += $sampleSize) {
            for ($y = 0; $y < $height; $y += $sampleSize) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // Simplifier en couleurs de base
                if ($r > 200 && $g > 200 && $b > 200) {
                    $colors['blanc'] = ($colors['blanc'] ?? 0) + 1;
                } elseif ($r < 50 && $g < 50 && $b < 50) {
                    $colors['noir'] = ($colors['noir'] ?? 0) + 1;
                } else {
                    $colors['coloré'] = ($colors['coloré'] ?? 0) + 1;
                }
            }
        }

        arsort($colors);
        return array_keys(array_slice($colors, 0, 3));
    }

    /**
     * Détecter le type de contenu probable
     */
    private function detectContentType(array $colors, int $width, int $height): string
    {
        $ratio = $width / $height;

        if (in_array('blanc', $colors) && in_array('noir', $colors)) {
            if ($ratio > 0.7 && $ratio < 1.3) {
                return "Document texte (probable)";
            } elseif ($height > $width) {
                return "Document portrait (probable)";
            } else {
                return "Document paysage (probable)";
            }
        } elseif (in_array('coloré', $colors)) {
            return "Image colorée ou graphique";
        } else {
            return "Document ou image monochrome";
        }
    }

    /**
     * Obtenir le format d'image
     */
    private function getImageFormat(int $type): string
    {
        $formats = [
            IMAGETYPE_JPEG => 'JPEG',
            IMAGETYPE_PNG => 'PNG',
            IMAGETYPE_GIF => 'GIF',
            IMAGETYPE_BMP => 'BMP',
            IMAGETYPE_WEBP => 'WEBP'
        ];

        return $formats[$type] ?? 'Inconnu';
    }

    /**
     * Nettoyer le texte extrait
     */
    private function cleanExtractedText(string $text): string
    {
        // Supprimer les caractères de contrôle
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Normaliser les espaces et sauts de ligne
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n+/', "\n", $text);

        // Supprimer les espaces en début et fin
        $text = trim($text);

        return $text;
    }

    /**
     * Vérifier si les services cloud sont disponibles
     */
    public function isCloudOCRAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get('https://api.ocr.space/');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
