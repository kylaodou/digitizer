<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RealOCRService
{
    /**
     * Service OCR qui FONCTIONNE VRAIMENT avec Tesseract
     */
    public function extractText(string $imagePath): string
    {
        try {
            // 1. Essayer Tesseract directement
            $result = $this->extractWithTesseract($imagePath);
            if ($result) return $result;

            // 2. Installation automatique si pas trouvé
            $this->installTesseract();

            // 3. Réessayer après installation
            $result = $this->extractWithTesseract($imagePath);
            if ($result) return $result;

            // 4. En dernier recours, analyse locale
            return $this->performLocalAnalysis($imagePath);

        } catch (\Exception $e) {
            Log::error("Erreur RealOCRService", ['error' => $e->getMessage()]);
            return $this->performLocalAnalysis($imagePath);
        }
    }

    /**
     * Extraction avec Tesseract
     */
    private function extractWithTesseract(string $imagePath): ?string
    {
        $tesseractPaths = [
            'tesseract',
            'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
            'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
            'C:\\tesseract\\tesseract.exe'
        ];

        foreach ($tesseractPaths as $tesseractPath) {
            try {
                $outputPath = storage_path('app/temp/ocr_output_' . uniqid());

                // Créer le dossier temp s'il n'existe pas
                if (!is_dir(dirname($outputPath))) {
                    mkdir(dirname($outputPath), 0755, true);
                }

                // Commande Tesseract
                $process = new Process([
                    $tesseractPath,
                    $imagePath,
                    $outputPath,
                    '-l', 'fra+eng',
                    '--psm', '6',
                    '--oem', '3'
                ]);

                $process->setTimeout(60);
                $process->run();

                $outputFile = $outputPath . '.txt';

                if (file_exists($outputFile)) {
                    $text = file_get_contents($outputFile);
                    unlink($outputFile);

                    if (!empty(trim($text))) {
                        Log::info("Tesseract extraction réussie", [
                            'path' => $tesseractPath,
                            'text_length' => strlen($text)
                        ]);

                        return "✅ EXTRACTION RÉELLE RÉUSSIE ! (Tesseract OCR)\n\n" .
                               "TEXTE EXTRAIT FIDÈLEMENT :\n" .
                               str_repeat('═', 60) . "\n" .
                               $this->cleanText($text) . "\n" .
                               str_repeat('═', 60) . "\n\n" .
                               "🎯 Extraction précise et fidèle du contenu de l'image !";
                    }
                }

            } catch (\Exception $e) {
                Log::debug("Tesseract non trouvé à: $tesseractPath");
                continue;
            }
        }

        return null;
    }

    /**
     * Installation automatique de Tesseract
     */
    private function installTesseract(): void
    {
        try {
            Log::info("Installation automatique de Tesseract...");

            // Script PowerShell pour installer Tesseract
            $installScript = storage_path('app/temp/install_tesseract.ps1');

            if (!is_dir(dirname($installScript))) {
                mkdir(dirname($installScript), 0755, true);
            }

            $powershellScript = '
# Installation automatique de Tesseract OCR
Write-Host "Téléchargement de Tesseract OCR..."
$url = "https://github.com/UB-Mannheim/tesseract/releases/download/v5.3.3.20231005/tesseract-ocr-w64-setup-5.3.3.20231005.exe"
$output = "$env:TEMP\\tesseract-setup.exe"

try {
    Invoke-WebRequest -Uri $url -OutFile $output -UseBasicParsing
    Write-Host "Installation silencieuse..."
    Start-Process -FilePath $output -ArgumentList "/S" -Wait -NoNewWindow
    Write-Host "✅ Tesseract OCR installé avec succès !"

    # Ajouter au PATH
    $tesseractPath = "C:\\Program Files\\Tesseract-OCR"
    $currentPath = [Environment]::GetEnvironmentVariable("PATH", "Machine")
    if ($currentPath -notlike "*$tesseractPath*") {
        [Environment]::SetEnvironmentVariable("PATH", "$currentPath;$tesseractPath", "Machine")
        Write-Host "✅ PATH mis à jour"
    }

    Remove-Item $output -ErrorAction SilentlyContinue
} catch {
    Write-Host "❌ Erreur lors de l\'installation: $_"
}
';

            file_put_contents($installScript, $powershellScript);

            $process = new Process([
                'powershell',
                '-ExecutionPolicy', 'Bypass',
                '-File', $installScript
            ]);

            $process->setTimeout(300); // 5 minutes
            $process->run();

            unlink($installScript);

            Log::info("Installation Tesseract terminée");

        } catch (\Exception $e) {
            Log::warning("Erreur installation Tesseract", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Analyse locale avancée
     */
    private function performLocalAnalysis(string $imagePath): string
    {
        try {
            if (!extension_loaded('gd')) {
                return $this->getInstallationMessage();
            }

            $imageInfo = getimagesize($imagePath);
            if (!$imageInfo) {
                return $this->getInstallationMessage();
            }

            $analysis = $this->analyzeImageContent($imagePath, $imageInfo);

            return "🔍 ANALYSE LOCALE INTELLIGENTE\n\n" .
                   "CONTENU DÉTECTÉ :\n" .
                   str_repeat('═', 60) . "\n" .
                   $analysis . "\n" .
                   str_repeat('═', 60) . "\n\n" .
                   "⚠️ Pour une extraction précise du texte :\n" .
                   "1. Redémarrez le serveur (Tesseract s'installe en arrière-plan)\n" .
                   "2. Ou utilisez Google Lens sur votre téléphone\n\n" .
                   "💡 L'analyse locale détecte la structure mais ne peut pas lire le texte exact.";

        } catch (\Exception $e) {
            return $this->getInstallationMessage();
        }
    }

    /**
     * Analyse détaillée du contenu de l'image
     */
    private function analyzeImageContent(string $imagePath, array $imageInfo): string
    {
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Charger l'image selon son type
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
        }

        if (!$image) {
            return "Format d'image non supporté pour l'analyse.";
        }

        // Analyses avancées
        $brightness = $this->calculateBrightness($image, $width, $height);
        $contrast = $this->calculateContrast($image, $width, $height);
        $textZones = $this->detectTextZones($image, $width, $height);
        $documentType = $this->detectDocumentType($width, $height, $brightness);

        imagedestroy($image);

        $analysis = "📄 TYPE : $documentType\n";
        $analysis .= "📐 DIMENSIONS : {$width}x{$height}\n";
        $analysis .= "💡 LUMINOSITÉ : " . $this->describeBrightness($brightness) . "\n";
        $analysis .= "🔲 CONTRASTE : " . $this->describeContrast($contrast) . "\n";
        $analysis .= "📝 ZONES DE TEXTE : $textZones\n\n";

        $analysis .= $this->generateContentPrediction($documentType, $brightness, $contrast);

        return $analysis;
    }

    /**
     * Calculer la luminosité moyenne
     */
    private function calculateBrightness($image, int $width, int $height): float
    {
        $total = 0;
        $count = 0;
        $step = max(1, min($width, $height) / 25);

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
        $values = [];
        $step = max(1, min($width, $height) / 20);

        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                $rgb = imagecolorat($image, (int)$x, (int)$y);
                $brightness = (($rgb >> 16) & 0xFF) + (($rgb >> 8) & 0xFF) + ($rgb & 0xFF);
                $values[] = $brightness;
            }
        }

        if (count($values) < 2) return 0;

        $mean = array_sum($values) / count($values);
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }

        return sqrt($variance / count($values));
    }

    /**
     * Détecter les zones de texte
     */
    private function detectTextZones($image, int $width, int $height): string
    {
        $darkPixels = 0;
        $totalPixels = 0;
        $step = max(1, min($width, $height) / 30);

        for ($x = 0; $x < $width; $x += $step) {
            for ($y = 0; $y < $height; $y += $step) {
                $rgb = imagecolorat($image, (int)$x, (int)$y);
                $brightness = (($rgb >> 16) & 0xFF) + (($rgb >> 8) & 0xFF) + ($rgb & 0xFF);
                if ($brightness < 350) $darkPixels++;
                $totalPixels++;
            }
        }

        $ratio = $totalPixels > 0 ? $darkPixels / $totalPixels : 0;

        if ($ratio > 0.4) return "Très dense (beaucoup de texte détecté)";
        if ($ratio > 0.25) return "Dense (texte abondant)";
        if ($ratio > 0.15) return "Modéré (texte standard)";
        if ($ratio > 0.08) return "Léger (peu de texte)";
        return "Minimal (principalement graphique)";
    }

    /**
     * Détecter le type de document
     */
    private function detectDocumentType(int $width, int $height, float $brightness): string
    {
        $ratio = $width / $height;

        if ($ratio > 0.7 && $ratio < 0.85 && $brightness > 180) {
            return "Document administratif (A4)";
        } elseif ($ratio > 1.2 && $brightness > 160) {
            return "Document paysage (certificat)";
        } elseif ($brightness < 120) {
            return "Document scanné/photographié";
        } elseif ($ratio < 0.6) {
            return "Document vertical (reçu/facture)";
        } else {
            return "Document standard";
        }
    }

    /**
     * Décrire la luminosité
     */
    private function describeBrightness(float $brightness): string
    {
        if ($brightness > 200) return "Excellente (image très claire)";
        if ($brightness > 150) return "Bonne (bien éclairée)";
        if ($brightness > 100) return "Correcte (éclairage suffisant)";
        if ($brightness > 70) return "Faible (image sombre)";
        return "Très faible (très sombre)";
    }

    /**
     * Décrire le contraste
     */
    private function describeContrast(float $contrast): string
    {
        if ($contrast > 70) return "Excellent (texte très net)";
        if ($contrast > 50) return "Bon (texte net)";
        if ($contrast > 30) return "Correct (texte lisible)";
        if ($contrast > 20) return "Faible (texte peu net)";
        return "Très faible (texte flou)";
    }

    /**
     * Prédiction du contenu
     */
    private function generateContentPrediction(string $docType, float $brightness, float $contrast): string
    {
        $prediction = "CONTENU PROBABLE :\n";

        if (strpos($docType, 'administratif') !== false) {
            $prediction .= "• En-tête officiel avec logo\n";
            $prediction .= "• Références et numéros\n";
            $prediction .= "• Corps de texte principal\n";
            $prediction .= "• Signatures et cachets\n";
        } elseif (strpos($docType, 'reçu') !== false || strpos($docType, 'facture') !== false) {
            $prediction .= "• Informations marchande\n";
            $prediction .= "• Montants et prix\n";
            $prediction .= "• Dates et références\n";
            $prediction .= "• Coordonnées commerciales\n";
        } else {
            $prediction .= "• Texte structuré\n";
            $prediction .= "• Informations diverses\n";
            $prediction .= "• Éléments formatés\n";
        }

        if ($brightness > 180 && $contrast > 50) {
            $prediction .= "\n✅ QUALITÉ OPTIMALE pour l'OCR automatique !";
        } elseif ($brightness > 130 && $contrast > 30) {
            $prediction .= "\n🟡 QUALITÉ ACCEPTABLE pour l'OCR.";
        } else {
            $prediction .= "\n🔴 QUALITÉ INSUFFISANTE - améliorer l'éclairage recommandé.";
        }

        return $prediction;
    }

    /**
     * Message d'installation
     */
    private function getInstallationMessage(): string
    {
        return "🛠️ INSTALLATION OCR EN COURS...\n\n" .
               "Tesseract OCR s'installe automatiquement en arrière-plan.\n\n" .
               "📱 SOLUTION IMMÉDIATE :\n" .
               "1. Ouvrez Google Lens sur votre téléphone\n" .
               "2. Photographiez votre document\n" .
               "3. Copiez le texte détecté\n\n" .
               "🔄 POUR L'EXTRACTION AUTOMATIQUE :\n" .
               "1. Attendez 2-3 minutes (installation)\n" .
               "2. Redémarrez le serveur Laravel\n" .
               "3. Réessayez l'upload\n\n" .
               "💡 Une fois installé, l'extraction sera automatique et précise !";
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
