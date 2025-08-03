<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MistralAIService;

class TestMistralAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mistral:test {--text= : Text to analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Mistral AI integration with sample text';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Test de l\'intégration Mistral AI');
        $this->info('═══════════════════════════════════════');

        $mistralService = app(MistralAIService::class);

        // Use provided text or default sample
        $sampleText = $this->option('text') ??
            "FACTURE N° 2024-001\n" .
            "Date: 15/07/2024\n" .
            "Société: BURKINA TECH SARL\n" .
            "Adresse: Ouagadougou, Burkina Faso\n" .
            "Client: Ministère de l'Économie Numérique\n" .
            "Description: Services de numérisation de documents\n" .
            "Quantité: 1000 pages\n" .
            "Prix unitaire: 200 FCFA\n" .
            "Total HT: 200,000 FCFA\n" .
            "TVA (18%): 36,000 FCFA\n" .
            "Total TTC: 236,000 FCFA\n" .
            "Modalités de paiement: 30 jours\n" .
            "Référence: PROJ-2024-BF-001";

        $this->info('📄 Texte à analyser:');
        $this->line($sampleText);
        $this->newLine();

        // Test document classification
        $this->info('🔍 Classification du document...');
        $documentType = $mistralService->classifyDocument($sampleText);
        $this->line("Type détecté: {$documentType}");
        $this->newLine();

        // Test key information extraction
        $this->info('📊 Extraction d\'informations clés...');
        $keyInfo = $mistralService->extractKeyInfo($sampleText);
        foreach ($keyInfo as $type => $values) {
            if (!empty($values)) {
                $this->line("- {$type}: " . implode(', ', $values));
            }
        }
        $this->newLine();

        // Test AI analysis
        $this->info('🧠 Analyse IA avec Mistral...');
        $this->warn('Note: Assurez-vous d\'avoir configuré votre clé API Mistral dans .env');

        if (!config('services.mistral.api_key') || config('services.mistral.api_key') === 'your_mistral_api_key_here') {
            $this->error('❌ Clé API Mistral non configurée!');
            $this->info('💡 Pour configurer:');
            $this->line('1. Obtenez une clé API gratuite sur https://console.mistral.ai/');
            $this->line('2. Ajoutez MISTRAL_API_KEY=votre_clé dans votre fichier .env');
            $this->line('3. Relancez cette commande');
            return Command::FAILURE;
        }

        try {
            $result = $mistralService->analyzeDocument($sampleText, $documentType);

            if ($result['success']) {
                $this->info('✅ Analyse réussie!');
                $this->newLine();
                $this->info('📝 Résumé généré:');
                $this->line($result['summary']);

                if (isset($result['usage'])) {
                    $this->newLine();
                    $this->info('📈 Utilisation API:');
                    $this->line('Tokens utilisés: ' . ($result['usage']['total_tokens'] ?? 'N/A'));
                }
            } else {
                $this->warn('⚠️  Analyse IA échouée, utilisation du résumé de secours:');
                $this->line($result['summary']);
                if (isset($result['error'])) {
                    $this->error('Erreur: ' . $result['error']);
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du test: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('✨ Test terminé!');

        return Command::SUCCESS;
    }
}
