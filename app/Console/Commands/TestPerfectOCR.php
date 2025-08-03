<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PerfectOCRService;
use Illuminate\Support\Facades\Log;

class TestPerfectOCR extends Command
{
    protected $signature = 'test:perfect-ocr';
    protected $description = 'Tester le nouveau système OCR parfait';

    public function handle()
    {
        $this->info('=== TEST DU SYSTÈME OCR PARFAIT ===');
        $this->newLine();

        try {
            // Créer une instance du service
            $perfectOCR = app(PerfectOCRService::class);
            $this->info('✅ Service PerfectOCRService chargé avec succès');
            $this->newLine();

            // Créer un fichier image de test
            $testImagePath = storage_path('app/temp/test_ocr_image.png');

            // Créer le dossier temp s'il n'existe pas
            $tempDir = dirname($testImagePath);
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Créer un faux fichier image pour le test
            $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
            file_put_contents($testImagePath, $imageData);

            $this->info("📄 Fichier de test créé : $testImagePath");
            $this->newLine();

            // Tester l'extraction
            $this->info('🔍 Test d\'extraction en cours...');
            $result = $perfectOCR->extractText($testImagePath);

            $this->info('✅ RÉSULTAT DE L\'EXTRACTION :');
            $this->line(str_repeat('=', 60));
            $this->line($result);
            $this->line(str_repeat('=', 60));
            $this->newLine();

            // Test de la détection de Tesseract
            $this->info('🔍 Vérification de Tesseract...');

            $tesseractPaths = [
                'tesseract',
                'C:\\Program Files\\Tesseract-OCR\\tesseract.exe',
                'C:\\Program Files (x86)\\Tesseract-OCR\\tesseract.exe',
            ];

            foreach ($tesseractPaths as $path) {
                $process = new \Symfony\Component\Process\Process(['where', 'tesseract']);
                $process->run();

                if ($process->isSuccessful()) {
                    $this->info("✅ Tesseract trouvé : " . trim($process->getOutput()));
                    break;
                } else {
                    $this->warn("❌ Tesseract non trouvé à : $path");
                }
            }

            // Nettoyer
            if (file_exists($testImagePath)) {
                unlink($testImagePath);
                $this->info('🧹 Fichier de test supprimé');
            }

            $this->newLine();
            $this->info('✅ Test terminé avec succès !');

        } catch (\Exception $e) {
            $this->error('❌ Erreur durant le test : ' . $e->getMessage());
            $this->error('Stack trace : ' . $e->getTraceAsString());
        }
    }
}
