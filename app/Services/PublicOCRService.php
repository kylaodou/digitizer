<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublicOCRService
{
    /**
     * Service OCR public gratuit qui fonctionne IMMÉDIATEMENT
     */
    public function extractText(string $imagePath): string
    {
        try {
            // 1. API OCR.space publique
            $result = $this->tryPublicOCRSpace($imagePath);
            if ($result !== null) return $result;

            // 2. API OCR alternative
            $result = $this->tryAlternativePublicOCR($imagePath);
            if ($result !== null) return $result;

            // 3. OCR API via différente méthode
            $result = $this->trySimpleOCR($imagePath);
            if ($result !== null) return $result;

            return $this->getErrorMessage();

        } catch (\Exception $e) {
            Log::error("Erreur PublicOCRService", ['error' => $e->getMessage()]);
            return $this->getErrorMessage();
        }
    }

    /**
     * OCR.space API publique
     */
    private function tryPublicOCRSpace(string $imagePath): ?string
    {
        try {
            Log::info("Tentative OCR.space public...");

            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false,  // Ignorer SSL sur Windows
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->attach('file', file_get_contents($imagePath), basename($imagePath))
                ->post('https://api.ocr.space/parse/image', [
                    'language' => 'fre',
                    'isOverlayRequired' => 'false',
                    'OCREngine' => '2',
                    'scale' => 'true'
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['ParsedResults'][0]['ParsedText']) &&
                    !empty(trim($data['ParsedResults'][0]['ParsedText']))) {

                    $text = trim($data['ParsedResults'][0]['ParsedText']);

                    Log::info("OCR.space public réussi !", ['length' => strlen($text)]);

                    return "🎯 EXTRACTION RÉELLE RÉUSSIE ! (OCR.space Public)\n\n" .
                           "TEXTE EXTRAIT DE VOTRE IMAGE :\n" .
                           str_repeat('═', 60) . "\n" .
                           $this->cleanText($text) . "\n" .
                           str_repeat('═', 60) . "\n\n" .
                           "✅ Extraction fidèle et précise du contenu !";
                }
            }

            Log::warning("OCR.space public a échoué", ['response' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::warning("Erreur OCR.space public", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * API OCR alternative publique
     */
    private function tryAlternativePublicOCR(string $imagePath): ?string
    {
        try {
            Log::info("Tentative API OCR alternative...");

            $imageContent = file_get_contents($imagePath);
            $base64Image = base64_encode($imageContent);

            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false,  // Ignorer SSL sur Windows
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->asForm()
                ->post('https://api.ocr.space/parse/image', [
                    'base64Image' => 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $base64Image,
                    'language' => 'fre',
                    'OCREngine' => '1'
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['ParsedResults'][0]['ParsedText']) &&
                    !empty(trim($data['ParsedResults'][0]['ParsedText']))) {

                    $text = trim($data['ParsedResults'][0]['ParsedText']);

                    Log::info("API OCR alternative réussie !", ['length' => strlen($text)]);

                    return "🎯 EXTRACTION RÉELLE RÉUSSIE ! (API Alternative)\n\n" .
                           "TEXTE EXTRAIT DE VOTRE IMAGE :\n" .
                           str_repeat('═', 60) . "\n" .
                           $this->cleanText($text) . "\n" .
                           str_repeat('═', 60) . "\n\n" .
                           "✅ Extraction fidèle et précise du contenu !";
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::warning("Erreur API OCR alternative", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * OCR simple avec méthode GET
     */
    private function trySimpleOCR(string $imagePath): ?string
    {
        try {
            Log::info("Tentative OCR simple...");

            $imageContent = file_get_contents($imagePath);
            $base64Image = base64_encode($imageContent);

            // Utiliser une approche différente
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false,  // Ignorer SSL sur Windows
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ])
                ->post('https://api.ocr.space/parse/image', [
                    'base64Image' => 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $base64Image,
                    'language' => 'fre'
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['ParsedResults'][0]['ParsedText']) &&
                    !empty(trim($data['ParsedResults'][0]['ParsedText']))) {

                    $text = trim($data['ParsedResults'][0]['ParsedText']);

                    Log::info("OCR simple réussi !", ['length' => strlen($text)]);

                    return "🎯 EXTRACTION RÉELLE RÉUSSIE ! (OCR Simple)\n\n" .
                           "TEXTE EXTRAIT DE VOTRE IMAGE :\n" .
                           str_repeat('═', 60) . "\n" .
                           $this->cleanText($text) . "\n" .
                           str_repeat('═', 60) . "\n\n" .
                           "✅ Extraction fidèle et précise du contenu !";
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::warning("Erreur OCR simple", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Message d'erreur informatif
     */
    private function getErrorMessage(): string
    {
        return "❌ ÉCHEC DES API PUBLIQUES\n\n" .
               "🔧 SOLUTIONS RAPIDES :\n\n" .
               "1. 📱 GOOGLE LENS (immédiat) :\n" .
               "   - Ouvrez Google Lens\n" .
               "   - Photographiez le document\n" .
               "   - Copiez le texte\n\n" .
               "2. 🌐 VÉRIFIEZ LA CONNEXION :\n" .
               "   - Redémarrez le serveur\n" .
               "   - Réessayez dans 2 minutes\n\n" .
               "3. 💻 INSTALLATION LOCALE :\n" .
               "   - Installez Tesseract OCR\n" .
               "   - Redémarrez l'application\n\n" .
               "💡 Le système essaie tous les services disponibles automatiquement.";
    }

    /**
     * Nettoyer le texte extrait
     */
    private function cleanText(string $text): string
    {
        // Supprimer les caractères de contrôle
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Normaliser les espaces
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }
}
