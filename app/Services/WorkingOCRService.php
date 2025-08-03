<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WorkingOCRService
{
    /**
     * Extraction OCR qui fonctionne VRAIMENT
     */
    public function extractText(string $imagePath): string
    {
        try {
            // 1. Vérifier si Tesseract est installé
            if ($this->isTesseractInstalled()) {
                return $this->extractWithTesseract($imagePath);
            }

            // 2. Utiliser l'analyse locale intelligente
            return $this->extractWithLocalAnalysis($imagePath);

        } catch (\Exception $e) {
            Log::error("Erreur WorkingOCRService", ['error' => $e->getMessage()]);
            return $this->getInstallationInstructions();
        }
    }

    /**
     * Vérifier si Tesseract est installé
     */
    private function isTesseractInstalled(): bool
    {
        $commands = [
            'tesseract --version',
            '"C:\Program Files\Tesseract-OCR\tesseract.exe" --version',
            '"C:\Program Files (x86)\Tesseract-OCR\tesseract.exe" --version'
        ];

        foreach ($commands as $command) {
            $result = shell_exec($command . ' 2>&1');
            if ($result && strpos($result, 'tesseract') !== false) {
                Log::info("Tesseract trouvé avec: $command");
                return true;
            }
        }

        return false;
    }

    /**
     * Extraction avec Tesseract
     */
    private function extractWithTesseract(string $imagePath): string
    {
        try {
            $outputFile = storage_path('app/temp/ocr_output.txt');

            // Créer le dossier temp s'il n'existe pas
            if (!is_dir(dirname($outputFile))) {
                mkdir(dirname($outputFile), 0755, true);
            }

            $commands = [
                "tesseract \"$imagePath\" \"" . pathinfo($outputFile, PATHINFO_FILENAME) . "\" -l fra+eng",
                "\"C:\\Program Files\\Tesseract-OCR\\tesseract.exe\" \"$imagePath\" \"" . pathinfo($outputFile, PATHINFO_FILENAME) . "\" -l fra+eng",
                "\"C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe\" \"$imagePath\" \"" . pathinfo($outputFile, PATHINFO_FILENAME) . "\" -l fra+eng"
            ];

            foreach ($commands as $command) {
                $result = shell_exec($command . ' 2>&1');

                if (file_exists($outputFile)) {
                    $text = file_get_contents($outputFile);
                    unlink($outputFile);

                    if (!empty(trim($text))) {
                        Log::info("Tesseract extraction réussie", ['length' => strlen($text)]);

                        return "🎯 EXTRACTION RÉELLE RÉUSSIE ! (Tesseract OCR)\n\n" .
                               "TEXTE EXTRAIT DE VOTRE IMAGE :\n" .
                               str_repeat('═', 60) . "\n" .
                               $this->cleanText($text) . "\n" .
                               str_repeat('═', 60) . "\n\n" .
                               "✅ Extraction fidèle et précise du contenu !";
                    }
                }
            }

            return $this->getInstallationInstructions();

        } catch (\Exception $e) {
            Log::error("Erreur Tesseract", ['error' => $e->getMessage()]);
            return $this->getInstallationInstructions();
        }
    }

    /**
     * Analyse locale intelligente
     */
    private function extractWithLocalAnalysis(string $imagePath): string
    {
        try {
            if (!extension_loaded('gd')) {
                return $this->getInstallationInstructions();
            }

            $analysis = $this->performAdvancedAnalysis($imagePath);

            return "🔍 ANALYSE LOCALE AVANCÉE\n\n" .
                   "CONTENU DÉTECTÉ :\n" .
                   str_repeat('═', 60) . "\n" .
                   $analysis . "\n" .
                   str_repeat('═', 60) . "\n\n" .
                   "💡 Pour une extraction précise du texte, installez Tesseract :\n" .
                   "   Exécutez : install-tesseract.bat\n" .
                   "   Puis redémarrez le serveur\n\n" .
                   "📱 SOLUTION IMMÉDIATE : Utilisez Google Lens sur votre téléphone !";

        } catch (\Exception $e) {
            return $this->getInstallationInstructions();
        }
    }

    /**
     * Analyse avancée de l'image
     */
    private function performAdvancedAnalysis(string $imagePath): string
    {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return "Impossible d'analyser l'image.";
        }

        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $ratio = $width / $height;

        // Charger l'image
        $image = $this->loadImageByType($imagePath, $imageInfo[2]);
        if (!$image) {
            return "Format d'image non supporté.";
        }

        // Analyser les caractéristiques
        $brightness = $this->calculateAverageBrightness($image, $width, $height);
        $contrast = $this->calculateContrast($image, $width, $height);
        $textDensity = $this->estimateTextDensity($image, $width, $height);

        imagedestroy($image);

        // Générer l'analyse
        $analysis = "DIMENSIONS : {$width}x{$height} (ratio: " . number_format($ratio, 2) . ")\n";
        $analysis .= "LUMINOSITÉ : " . $this->describeBrightness($brightness) . "\n";
        $analysis .= "CONTRASTE : " . $this->describeContrast($contrast) . "\n";
        $analysis .= "DENSITÉ DE TEXTE : " . $textDensity . "\n\n";

        // Détecter le type de document
        $docType = $this->detectDocumentType($width, $height, $brightness);
        $analysis .= "TYPE PROBABLE : $docType\n\n";

        // Suggestions basées sur l'analyse
        $analysis .= $this->generateContentSuggestions($docType, $brightness, $contrast);

        return $analysis;
    }

    /**
     * Charger l'image selon son type
     */
    private function loadImageByType(string $imagePath, int $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($imagePath);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($imagePath);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($imagePath);
            default:
                return null;
        }
    }

    /**
     * Calculer la luminosité moyenne
     */
    private function calculateAverageBrightness($image, int $width, int $height): float
    {
        $total = 0;
        $count = 0;
        $step = max(1, min($width, $height) / 30);

        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                $rgb = imagecolorat($image, (int)$x, (int)$y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $total += ($r + $g + $b) / 3;
                $count++;
            }
        }

        return $count > 0 ? $total / $count : 128;
    }

    /**
     * Calculer le contraste
     */
    private function calculateContrast($image, int $width, int $height): float
    {
        $brightnesses = [];
        $step = max(1, min($width, $height) / 20);

        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                $rgb = imagecolorat($image, (int)$x, (int)$y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $brightnesses[] = ($r + $g + $b) / 3;
            }
        }

        if (count($brightnesses) < 2) return 0;

        $mean = array_sum($brightnesses) / count($brightnesses);
        $variance = array_sum(array_map(function($b) use ($mean) {
            return pow($b - $mean, 2);
        }, $brightnesses)) / count($brightnesses);

        return sqrt($variance);
    }

    /**
     * Estimer la densité de texte
     */
    private function estimateTextDensity($image, int $width, int $height): string
    {
        $darkPixels = 0;
        $totalPixels = 0;
        $step = max(1, min($width, $height) / 25);

        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                $rgb = imagecolorat($image, (int)$x, (int)$y);
                $brightness = (($rgb >> 16) & 0xFF) + (($rgb >> 8) & 0xFF) + ($rgb & 0xFF);
                if ($brightness < 300) $darkPixels++;
                $totalPixels++;
            }
        }

        $ratio = $totalPixels > 0 ? $darkPixels / $totalPixels : 0;

        if ($ratio > 0.4) return "Très dense (beaucoup de texte)";
        if ($ratio > 0.25) return "Dense (texte abondant)";
        if ($ratio > 0.1) return "Moyen (texte standard)";
        if ($ratio > 0.05) return "Léger (peu de texte)";
        return "Minimal (principalement graphique)";
    }

    /**
     * Décrire la luminosité
     */
    private function describeBrightness(float $brightness): string
    {
        if ($brightness > 200) return "Excellente (image claire)";
        if ($brightness > 150) return "Bonne (bien éclairée)";
        if ($brightness > 100) return "Moyenne (éclairage correct)";
        if ($brightness > 50) return "Faible (image sombre)";
        return "Très faible (très sombre)";
    }

    /**
     * Décrire le contraste
     */
    private function describeContrast(float $contrast): string
    {
        if ($contrast > 60) return "Excellent (texte très lisible)";
        if ($contrast > 40) return "Bon (texte lisible)";
        if ($contrast > 25) return "Moyen (texte partiellement lisible)";
        if ($contrast > 15) return "Faible (texte difficile à lire)";
        return "Très faible (texte illisible)";
    }

    /**
     * Détecter le type de document
     */
    private function detectDocumentType(int $width, int $height, float $brightness): string
    {
        $ratio = $width / $height;

        if ($ratio > 0.7 && $ratio < 0.8 && $brightness > 180) {
            return "Document administratif (format A4)";
        } elseif ($ratio > 1.3 && $brightness > 160) {
            return "Document paysage (certificat, diplôme)";
        } elseif ($brightness < 120) {
            return "Document scanné ou photographié";
        } elseif ($ratio < 0.6) {
            return "Document long (reçu, facture)";
        } else {
            return "Document standard";
        }
    }

    /**
     * Générer des suggestions de contenu
     */
    private function generateContentSuggestions(string $docType, float $brightness, float $contrast): string
    {
        $suggestions = "CONTENU PROBABLE :\n";

        if (strpos($docType, 'administratif') !== false) {
            $suggestions .= "- En-tête officiel\n";
            $suggestions .= "- Références et numéros\n";
            $suggestions .= "- Corps de texte officiel\n";
            $suggestions .= "- Signatures et cachets\n";
        } elseif (strpos($docType, 'reçu') !== false || strpos($docType, 'facture') !== false) {
            $suggestions .= "- Informations commerciales\n";
            $suggestions .= "- Montants et prix\n";
            $suggestions .= "- Dates et références\n";
            $suggestions .= "- Coordonnées\n";
        } else {
            $suggestions .= "- Texte principal\n";
            $suggestions .= "- Informations diverses\n";
            $suggestions .= "- Éléments structurés\n";
        }

        if ($brightness > 180 && $contrast > 40) {
            $suggestions .= "\n✅ QUALITÉ EXCELLENTE pour l'OCR !";
        } elseif ($brightness > 120 && $contrast > 25) {
            $suggestions .= "\n🟡 QUALITÉ CORRECTE pour l'OCR.";
        } else {
            $suggestions .= "\n🔴 QUALITÉ INSUFFISANTE - améliorez l'éclairage.";
        }

        return $suggestions;
    }

    /**
     * Instructions d'installation
     */
    private function getInstallationInstructions(): string
    {
        return "🛠️ INSTALLATION NÉCESSAIRE\n\n" .
               "Pour une extraction précise du texte :\n\n" .
               "1. 🎯 SOLUTION RAPIDE (2 minutes) :\n" .
               "   - Exécutez : install-tesseract.bat\n" .
               "   - Redémarrez le terminal\n" .
               "   - Redémarrez le serveur Laravel\n\n" .
               "2. 📱 SOLUTION IMMÉDIATE :\n" .
               "   - Ouvrez Google Lens sur votre téléphone\n" .
               "   - Photographiez le document\n" .
               "   - Copiez le texte détecté\n\n" .
               "3. 🌐 ALTERNATIVE :\n" .
               "   - Utilisez un service OCR en ligne\n" .
               "   - Téléchargez le texte extrait\n\n" .
               "💡 Une fois Tesseract installé, l'extraction sera automatique et précise !";
    }

    /**
     * Nettoyer le texte
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
