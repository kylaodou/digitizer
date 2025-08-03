<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MistralAIService;
use Illuminate\Support\Facades\Http;

class TestMistralConnection extends Command
{
    protected $signature = 'mistral:connection {--debug}';
    protected $description = 'Test la connexion avec Mistral AI et diagnostique les problèmes';

    public function handle()
    {
        $this->info('🔍 Diagnostic de connexion Mistral AI');
        $this->info('=====================================');

        // 1. Vérifier la configuration
        $this->checkConfiguration();

        // 2. Tester la connectivité réseau
        $this->testNetworkConnectivity();

        // 3. Tester avec différentes options SSL
        $this->testSSLOptions();

        // 4. Test final avec le service
        $this->testMistralService();

        return Command::SUCCESS;
    }

    private function checkConfiguration()
    {
        $this->info('📋 1. Vérification de la configuration...');

        $apiKey = config('services.mistral.api_key');
        $baseUrl = config('services.mistral.base_url');

        if (!$apiKey || $apiKey === 'your_mistral_api_key_here') {
            $this->error('❌ Clé API Mistral manquante ou invalide');
            return;
        }

        $this->line("✅ Clé API: " . substr($apiKey, 0, 8) . '...');
        $this->line("✅ URL de base: {$baseUrl}");
        $this->newLine();
    }

    private function testNetworkConnectivity()
    {
        $this->info('🌐 2. Test de connectivité réseau...');

        $testUrls = [
            'google.com' => 'https://www.google.com',
            'mistral.ai' => 'https://mistral.ai',
        ];

        foreach ($testUrls as $name => $url) {
            try {
                $response = Http::timeout(5)->get($url);
                if ($response->successful()) {
                    $this->line("✅ {$name}: Accessible");
                } else {
                    $this->warn("⚠️  {$name}: Erreur HTTP " . $response->status());
                }
            } catch (\Exception $e) {
                $this->error("❌ {$name}: " . $e->getMessage());
            }
        }
        $this->newLine();
    }

    private function testSSLOptions()
    {
        $this->info('🔒 3. Test des options SSL...');

        $apiKey = config('services.mistral.api_key');
        $url = 'https://api.mistral.ai/v1/models';

        // Test 1: Avec vérification SSL complète
        $this->line('Test 1: SSL complet...');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey
            ])->timeout(10)->get($url);

            $this->line("✅ SSL complet: " . $response->status());
        } catch (\Exception $e) {
            $this->error("❌ SSL complet: " . $e->getMessage());
        }

        // Test 2: Sans vérification SSL
        $this->line('Test 2: SSL désactivé...');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey
            ])->withOptions([
                'verify' => false
            ])->timeout(10)->get($url);

            $this->line("✅ SSL désactivé: " . $response->status());
        } catch (\Exception $e) {
            $this->error("❌ SSL désactivé: " . $e->getMessage());
        }

        // Test 3: Avec certificat personnalisé
        $this->line('Test 3: Certificat personnalisé...');
        $certPath = $this->getCertificatePath();
        if ($certPath) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey
                ])->withOptions([
                    'verify' => $certPath
                ])->timeout(10)->get($url);

                $this->line("✅ Certificat personnalisé: " . $response->status());
            } catch (\Exception $e) {
                $this->error("❌ Certificat personnalisé: " . $e->getMessage());
            }
        } else {
            $this->warn('⚠️  Aucun certificat personnalisé trouvé');
        }

        $this->newLine();
    }

    private function testMistralService()
    {
        $this->info('🧠 4. Test du service Mistral...');

        try {
            $mistralService = app(MistralAIService::class);

            $sampleText = "Test de connexion Mistral AI depuis le Burkina Faso.";

            $result = $mistralService->analyzeDocument($sampleText, 'test');

            if ($result['success']) {
                $this->line("✅ Service Mistral: Fonctionnel!");
                $this->line("📝 Résumé: " . substr($result['summary'], 0, 100) . '...');
            } else {
                $this->warn("⚠️  Service Mistral: " . ($result['error'] ?? 'Erreur inconnue'));
                $this->line("📝 Résumé de secours généré");
            }

        } catch (\Exception $e) {
            $this->error("❌ Service Mistral: " . $e->getMessage());
        }
    }

    private function getCertificatePath(): ?string
    {
        $possiblePaths = [
            // Chemin standard Windows
            dirname(php_ini_loaded_file()) . '/cacert.pem',
            // Chemins XAMPP
            'C:/xampp/apache/bin/curl-ca-bundle.crt',
            'C:/xampp/php/extras/ssl/cacert.pem',
            // Chemins Laravel Valet
            storage_path('ssl/cacert.pem'),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $this->line("📄 Certificat trouvé: {$path}");
                return $path;
            }
        }

        return null;
    }
}
