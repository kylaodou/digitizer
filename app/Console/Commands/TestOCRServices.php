<?php

namespace App\Console\Commands;

use App\Services\OCRService;
use App\Services\CloudOCRService;
use Illuminate\Console\Command;

class TestOCRServices extends Command
{
    protected $signature = 'ocr:test {imagePath?}';
    protected $description = 'Tester les différents services OCR disponibles';

    public function handle()
    {
        $this->info('🔍 Test des services OCR disponibles...');
        $this->newLine();

        // Test Tesseract
        $this->info('1. Test Tesseract OCR :');
        $ocrService = app(OCRService::class);

        if ($ocrService->isOcrAvailable()) {
            $this->info('✅ Tesseract OCR est installé et disponible');
        } else {
            $this->warn('❌ Tesseract OCR n\'est pas installé');
        }
        $this->newLine();

        // Test services cloud
        $this->info('2. Test services OCR cloud :');
        $cloudService = app(CloudOCRService::class);

        if ($cloudService->isCloudOCRAvailable()) {
            $this->info('✅ Services OCR cloud disponibles (OCR.space)');
        } else {
            $this->warn('❌ Services OCR cloud non disponibles');
        }
        $this->newLine();

        // Test avec une image si fournie
        $imagePath = $this->argument('imagePath');
        if ($imagePath && file_exists($imagePath)) {
            $this->info('3. Test d\'extraction sur votre image :');
            $this->info("📄 Fichier : $imagePath");

            try {
                $extractedText = $ocrService->extractTextWithFallback($imagePath);
                $this->info('✅ Extraction réussie !');
                $this->newLine();
                $this->line('📝 Texte extrait :');
                $this->line(str_repeat('-', 50));
                $this->line(substr($extractedText, 0, 500) . (strlen($extractedText) > 500 ? '...' : ''));
                $this->line(str_repeat('-', 50));
            } catch (\Exception $e) {
                $this->error('❌ Erreur lors de l\'extraction : ' . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('💡 Recommandations :');

        if (!$ocrService->isOcrAvailable() && !$cloudService->isCloudOCRAvailable()) {
            $this->warn('- Aucun service OCR n\'est disponible');
            $this->warn('- Installez Tesseract pour la meilleure qualité');
            $this->warn('- Ou vérifiez votre connexion internet pour les services cloud');
        } elseif (!$ocrService->isOcrAvailable()) {
            $this->info('- Services cloud disponibles (solution simple)');
            $this->info('- Pour de meilleures performances, installez Tesseract');
        } else {
            $this->info('- Tesseract installé : qualité optimale ✨');
        }

        $this->newLine();
        $this->info('🎯 Pour tester avec votre image :');
        $this->line('php artisan ocr:test "chemin/vers/votre/image.png"');
    }
}
