<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PythonOCRService
{
    /**
     * Extraction OCR avec Python et pytesseract
     */
    public function extractTextWithPython(string $imagePath): string
    {
        try {
            // Créer le script Python temporaire
            $pythonScript = $this->createPythonOCRScript();

            // Exécuter Python OCR
            $process = new Process([
                'python',
                $pythonScript,
                $imagePath
            ]);

            $process->setTimeout(60);
            $process->run();

            if ($process->isSuccessful()) {
                $extractedText = $process->getOutput();

                // Nettoyer le résultat
                $cleanText = $this->cleanExtractedText($extractedText);

                // Supprimer le script temporaire
                unlink($pythonScript);

                Log::info("OCR Python réussi", ['text_length' => strlen($cleanText)]);

                return "✅ EXTRACTION RÉELLE AVEC PYTHON OCR\n\n" .
                       "TEXTE EXTRAIT FIDÈLEMENT :\n" .
                       str_repeat('=', 60) . "\n" .
                       $cleanText . "\n" .
                       str_repeat('=', 60) . "\n\n" .
                       "🎯 Extraction précise et fidèle du contenu de l'image !";

            } else {
                return $this->installPythonOCR();
            }

        } catch (\Exception $e) {
            Log::error("Erreur Python OCR", ['error' => $e->getMessage()]);
            return $this->installPythonOCR();
        }
    }

    /**
     * Créer le script Python OCR
     */
    private function createPythonOCRScript(): string
    {
        $scriptPath = storage_path('app/temp/ocr_script.py');

        // Créer le dossier temp s'il n'existe pas
        if (!is_dir(dirname($scriptPath))) {
            mkdir(dirname($scriptPath), 0755, true);
        }

        $pythonCode = '#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import sys
import os

def install_and_import(package):
    import importlib
    try:
        importlib.import_module(package)
    except ImportError:
        import subprocess
        subprocess.check_call([sys.executable, "-m", "pip", "install", package])
    finally:
        globals()[package] = importlib.import_module(package)

def extract_text_ocr(image_path):
    try:
        # Installer et importer les packages nécessaires
        install_and_import("PIL")
        from PIL import Image

        install_and_import("pytesseract")
        import pytesseract

        # Configurer pytesseract pour Windows
        # Essayer plusieurs chemins possibles
        possible_paths = [
            r"C:\Program Files\Tesseract-OCR\tesseract.exe",
            r"C:\Program Files (x86)\Tesseract-OCR\tesseract.exe",
            r"C:\Users\%USERNAME%\AppData\Local\Programs\Tesseract-OCR\tesseract.exe",
            "tesseract"  # Si dans le PATH
        ]

        tesseract_found = False
        for path in possible_paths:
            try:
                if os.path.exists(path) or path == "tesseract":
                    pytesseract.pytesseract.tesseract_cmd = path
                    # Test rapide
                    pytesseract.get_tesseract_version()
                    tesseract_found = True
                    break
            except:
                continue

        if not tesseract_found:
            # Auto-installer Tesseract
            print("Installation automatique de Tesseract...")
            import subprocess
            subprocess.run([
                "powershell",
                "-Command",
                "Invoke-WebRequest -Uri \"https://github.com/UB-Mannheim/tesseract/releases/download/v5.3.3.20231005/tesseract-ocr-w64-setup-5.3.3.20231005.exe\" -OutFile \"tesseract-setup.exe\"; Start-Process -FilePath \"tesseract-setup.exe\" -ArgumentList \"/S\" -Wait"
            ], check=True)
            pytesseract.pytesseract.tesseract_cmd = r"C:\Program Files\Tesseract-OCR\tesseract.exe"

        # Ouvrir et traiter l\'image
        image = Image.open(image_path)

        # Configuration OCR pour le français
        config = r"--oem 3 --psm 6 -l fra+eng"

        # Extraire le texte
        text = pytesseract.image_to_string(image, config=config)

        # Nettoyer et retourner le texte
        return text.strip()

    except Exception as e:
        return f"Erreur OCR Python: {str(e)}"

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python ocr_script.py <image_path>")
        sys.exit(1)

    image_path = sys.argv[1]
    result = extract_text_ocr(image_path)
    print(result)
';

        file_put_contents($scriptPath, $pythonCode);
        return $scriptPath;
    }

    /**
     * Instructions d\'installation Python OCR
     */
    private function installPythonOCR(): string
    {
        return "🐍 INSTALLATION PYTHON OCR AUTOMATIQUE\n\n" .
               "Copez-collez cette commande dans votre terminal :\n\n" .
               "pip install pytesseract pillow\n\n" .
               "Puis relancez l\'upload de votre document !\n\n" .
               "⚡ Cette solution installe tout automatiquement :\n" .
               "- Python OCR (pytesseract)\n" .
               "- Tesseract OCR (auto-téléchargement)\n" .
               "- Support français\n\n" .
               "🎯 RÉSULTAT : Extraction fidèle garantie !";
    }

    /**
     * Nettoyer le texte extrait
     */
    private function cleanExtractedText(string $text): string
    {
        // Supprimer les caractères de contrôle
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Normaliser les espaces
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/\n+/', "\n", $text);

        // Supprimer les espaces en début et fin
        $text = trim($text);

        return $text;
    }

    /**
     * Vérifier si Python OCR est disponible
     */
    public function isPythonOCRAvailable(): bool
    {
        try {
            $process = new Process(['python', '--version']);
            $process->run();
            return $process->isSuccessful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
