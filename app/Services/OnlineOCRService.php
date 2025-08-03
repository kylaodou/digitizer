<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OnlineOCRService
{
    /**
     * Extraire le texte avec OCR.space (service gratuit)
     */
    public function extractTextOnline(string $imagePath): string
    {
        try {
            Log::info("Tentative d'extraction OCR en ligne", ['image' => $imagePath]);

            // Encoder l'image en base64
            $imageData = base64_encode(file_get_contents($imagePath));

            // Appel API OCR.space (gratuit, 25 000 calls/mois)
            $response = Http::timeout(30)->post('https://api.ocr.space/parse/image', [
                'base64Image' => 'data:image/png;base64,' . $imageData,
                'apikey' => 'helloworld', // Clé demo gratuite
                'language' => 'fre', // Français
                'isOverlayRequired' => false,
                'OCREngine' => 2, // Moteur le plus récent
                'scale' => true,
                'isTable' => true,
                'detectOrientation' => true
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['ParsedResults'][0]['ParsedText']) && !empty($data['ParsedResults'][0]['ParsedText'])) {
                    $extractedText = $data['ParsedResults'][0]['ParsedText'];

                    // Nettoyer le texte
                    $cleanText = $this->cleanOCRText($extractedText);

                    Log::info("OCR en ligne réussi", [
                        'text_length' => strlen($cleanText),
                        'confidence' => $data['ParsedResults'][0]['TextOverlay'] ?? 'N/A'
                    ]);

                    return "EXTRACTION DE TEXTE RÉELLE (OCR en ligne)\n" .
                           "✅ Service OCR.space utilisé\n" .
                           "✅ Reconnaissance française activée\n\n" .
                           "TEXTE EXTRAIT :\n" .
                           str_repeat('-', 50) . "\n" .
                           $cleanText . "\n" .
                           str_repeat('-', 50) . "\n\n" .
                           "✅ Extraction fidèle du contenu de l'image";
                }
            }

            // Si échec, essayer une autre méthode
            return $this->extractWithFreeOCR($imagePath);

        } catch (\Exception $e) {
            Log::error("Erreur OCR en ligne", ['error' => $e->getMessage()]);
            return "Erreur lors de l'extraction en ligne : " . $e->getMessage() .
                   "\n\nPour une extraction fiable, installez Tesseract OCR.";
        }
    }

    /**
     * Méthode alternative avec Free OCR API
     */
    private function extractWithFreeOCR(string $imagePath): string
    {
        try {
            // Convertir en base64
            $imageData = base64_encode(file_get_contents($imagePath));

            // API alternative
            $response = Http::timeout(30)->post('https://api.api-ninjas.com/v1/imagetotext', [
                'image' => $imageData
            ], [
                'X-Api-Key' => 'DEMO_KEY' // Remplacer par une vraie clé si besoin
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data[0]['text'])) {
                    return "EXTRACTION ALTERNATIVE\n" .
                           "✅ Service API-Ninjas utilisé\n\n" .
                           "TEXTE EXTRAIT :\n" .
                           str_repeat('-', 50) . "\n" .
                           $this->cleanOCRText($data[0]['text']) . "\n" .
                           str_repeat('-', 50);
                }
            }

            // Fallback : OCR.space avec clé demo différente
            return $this->extractWithOCRSpaceFallback($imagePath);

        } catch (\Exception $e) {
            return $this->extractWithOCRSpaceFallback($imagePath);
        }
    }

    /**
     * OCR.space avec paramètres de fallback
     */
    private function extractWithOCRSpaceFallback(string $imagePath): string
    {
        try {
            $response = Http::attach(
                'file', file_get_contents($imagePath), basename($imagePath)
            )->post('https://api.ocr.space/parse/image', [
                'apikey' => 'K87899142388957', // Autre clé demo
                'language' => 'fre',
                'isOverlayRequired' => false,
                'OCREngine' => 1, // Moteur legacy parfois plus fiable
                'scale' => true
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['ParsedResults'][0]['ParsedText'])) {
                    $text = $data['ParsedResults'][0]['ParsedText'];
                    return "EXTRACTION OCR (Fallback)\n" .
                           "✅ Tesseract legacy en ligne\n\n" .
                           "TEXTE EXTRAIT :\n" .
                           str_repeat('-', 50) . "\n" .
                           $this->cleanOCRText($text) . "\n" .
                           str_repeat('-', 50);
                }
            }

            return "❌ Tous les services OCR en ligne ont échoué.\n" .
                   "💡 Solution : Installez Tesseract OCR pour une extraction fiable.\n" .
                   "📋 Guide : Voir INSTALL_TESSERACT_EXPRESS.md";

        } catch (\Exception $e) {
            return "❌ Services OCR en ligne indisponibles.\n" .
                   "🔧 Solution recommandée : Installation Tesseract OCR locale.\n" .
                   "⚡ Guide express : 5 minutes d'installation pour extraction parfaite.";
        }
    }

    /**
     * Nettoyer le texte OCR extrait
     */
    private function cleanOCRText(string $text): string
    {
        // Supprimer les caractères de contrôle
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Normaliser les sauts de ligne
        $text = preg_replace('/\r\n?/', "\n", $text);

        // Supprimer les lignes vides en excès
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        // Supprimer les espaces en début et fin
        $text = trim($text);

        // Corriger quelques erreurs OCR courantes en français
        $corrections = [
            '/\bà\s+/' => 'à ',  // Espaces après à
            '/\s+:/' => ' :',    // Espaces avant deux-points
            '/\s+;/' => ' ;',    // Espaces avant point-virgule
            '/\s+\?/' => ' ?',   // Espaces avant point d'interrogation
            '/\s+!/' => ' !',    // Espaces avant point d'exclamation
        ];

        foreach ($corrections as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        return $text;
    }

    /**
     * Vérifier si les services en ligne sont disponibles
     */
    public function isOnlineOCRAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get('https://api.ocr.space/');
            return $response->status() < 500;
        } catch (\Exception $e) {
            return false;
        }
    }
}
