<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MistralAIService;

class TestShortAnalysis extends Command
{
    protected $signature = 'test:short-analysis';
    protected $description = 'Test l\'analyse courte avec Mistral AI';

    public function handle()
    {
        $this->info('🧪 Test d\'analyse courte avec Mistral AI');
        $this->newLine();

        $mistralService = app(MistralAIService::class);

        // Texte de test basé sur votre image
        $testText = "COMMUNE DE OUAGADOUGOU
AGENCE MUNICIPALE DES GRANDS TRAVAUX
DIRECTION GENERALE
DIRECTION DE L'ADMINISTRATION, DES FINANCES ET DE LA COMPTABILITE

FICHE D'ACCOMPAGNEMENT DU COURRIER ARRIVEE

Nature: Demande de stage
Numéro Arr: 0416/05/2025
Date arr: 05/05/2025
Provenance: BAZIE Sabrina Nelle Yiriyé

Objet: Demande de stage en communication pour développement formulée par BAZIE Nelle Yiriyé

Imputation du Directeur
- M. SAWADOGO Adama
- Mme HANRO/SEYE Aïcha D.
- M. ROUAMBA Idrissa
- M. KOBANKA François
- BALKOUMA Ibrahim Khalil
- Mme SERI/KOUMBEM W. Armelle
- Secrétariat

Date du jour: 05/05/2025";

        // Test de classification
        $this->line('📋 Classification du document...');
        $documentType = $mistralService->classifyDocument($testText);
        $this->line("Type détecté: {$documentType}");
        $this->newLine();

        // Test d'extraction d'informations
        $this->line('🔍 Extraction d\'informations clés...');
        $keyInfo = $mistralService->extractKeyInfo($testText);
        foreach ($keyInfo as $type => $values) {
            if (!empty($values)) {
                $this->line("{$type}: " . implode(', ', $values));
            }
        }
        $this->newLine();

        // Test d'analyse IA
        $this->line('🤖 Analyse IA en cours...');
        $result = $mistralService->analyzeDocument($testText, $documentType);

        if ($result['success']) {
            $this->line('✅ Analyse réussie!');
            $this->newLine();
            $this->line('📝 Résumé court:');
            $this->line($result['summary']);
        } else {
            $this->error('❌ Échec de l\'analyse: ' . ($result['error'] ?? 'Erreur inconnue'));
            $this->newLine();
            $this->line('📝 Résumé de secours:');
            $this->line($result['summary']);
        }

        return Command::SUCCESS;
    }
}
