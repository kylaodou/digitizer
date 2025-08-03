<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MistralAIService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.mistral.api_key', env('MISTRAL_API_KEY'));
        $this->baseUrl = config('services.mistral.base_url', 'https://api.mistral.ai/v1');
    }

    /**
     * Analyze document text and generate summary using Mistral AI
     */
    public function analyzeDocument(string $text, string $documentType = 'document'): array
    {
        try {
            $prompt = $this->buildAnalysisPrompt($text, $documentType);

            // Configuration HTTP avec gestion SSL pour Windows
            $httpClient = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30);

            // Désactiver la vérification SSL sur Windows si nécessaire
            if (PHP_OS_FAMILY === 'Windows') {
                $httpClient = $httpClient->withOptions([
                    'verify' => false, // Désactive la vérification SSL temporairement
                ]);
            }

            $response = $httpClient->post($this->baseUrl . '/chat/completions', [
                'model' => 'mistral-small-latest', // Utilisation du modèle gratuit
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un assistant IA spécialisé dans l\'analyse de documents pour les entreprises et services publics du Burkina Faso. Tu dois fournir des analyses claires, précises et adaptées au contexte burkinabè.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 300 // Réduit pour des résumés plus courts
            ]);            if ($response->successful()) {
                $result = $response->json();

                // Nettoyer le texte en retirant le formatage Markdown
                $summary = $result['choices'][0]['message']['content'] ?? '';
                $cleanSummary = $this->cleanMarkdownFormatting($summary);

                return [
                    'success' => true,
                    'summary' => $cleanSummary,
                    'usage' => $result['usage'] ?? [],
                ];
            }

            Log::error('Mistral AI API error: ' . $response->body());
            return [
                'success' => false,
                'error' => 'Erreur de communication avec l\'API Mistral',
                'summary' => $this->getFallbackSummary($text)
            ];

        } catch (\Exception $e) {
            Log::error('Mistral AI service error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'summary' => $this->getFallbackSummary($text)
            ];
        }
    }

    /**
     * Build analysis prompt based on document type
     */
    private function buildAnalysisPrompt(string $text, string $documentType): string
    {
        $basePrompt = "Analyse ce document en français et fournis un résumé COURT et précis (maximum 4-5 lignes) comprenant :\n\n";

        switch (strtolower($documentType)) {
            case 'invoice':
            case 'facture':
                return $basePrompt .
                    "1. Type : Facture\n" .
                    "2. Montant total\n" .
                    "3. Fournisseur\n" .
                    "4. Date\n" .
                    "5. Objet principal\n\n" .
                    "IMPORTANT: Résumé très court (4-5 lignes maximum).\n\n" .
                    "Document :\n" . substr($text, 0, 1500);

            case 'contract':
            case 'contrat':
                return $basePrompt .
                    "1. Type : Contrat\n" .
                    "2. Parties\n" .
                    "3. Objet\n" .
                    "4. Durée\n" .
                    "5. Points importants\n\n" .
                    "IMPORTANT: Résumé très court (4-5 lignes maximum).\n\n" .
                    "Document :\n" . substr($text, 0, 1500);

            case 'lettre':
            case 'courrier':
                return $basePrompt .
                    "1. Type : Lettre/Courrier\n" .
                    "2. Expéditeur\n" .
                    "3. Destinataire\n" .
                    "4. Objet\n" .
                    "5. Demande principale\n\n" .
                    "IMPORTANT: Résumé très court (4-5 lignes maximum).\n\n" .
                    "Document :\n" . substr($text, 0, 1500);

            default:
                return $basePrompt .
                    "1. Type de document\n" .
                    "2. Objet principal\n" .
                    "3. Informations clés\n" .
                    "4. Action requise\n\n" .
                    "IMPORTANT: Résumé très court (4-5 lignes maximum).\n\n" .
                    "Document :\n" . substr($text, 0, 1500);
        }
    }

    /**
     * Clean Markdown formatting from text
     */
    private function cleanMarkdownFormatting(string $text): string
    {
        // Retirer les balises Markdown courantes
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text); // **bold** -> bold
        $text = preg_replace('/\*(.*?)\*/', '$1', $text); // *italic* -> italic
        $text = preg_replace('/#{1,6}\s/', '', $text); // ### Title -> Title
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text); // [text](link) -> text
        $text = preg_replace('/`([^`]+)`/', '$1', $text); // `code` -> code

        // Nettoyer les lignes vides multiples
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * Generate fallback summary when AI is unavailable
     */
    private function getFallbackSummary(string $text): string
    {
        $wordCount = str_word_count($text);
        $preview = substr($text, 0, 200) . '...';

        return "**Résumé automatique généré localement**\n\n" .
               "📄 **Aperçu** : {$preview}\n\n" .
               "📊 **Statistiques** :\n" .
               "- Nombre de mots : {$wordCount}\n" .
               "- Caractères : " . strlen($text) . "\n\n" .
               "⚠️ **Note** : Résumé IA Mistral temporairement indisponible. Résumé basique généré.";
    }

    /**
     * Extract key information from text using simple regex patterns
     */
    public function extractKeyInfo(string $text): array
    {
        $info = [];

        // Extract dates (various formats)
        if (preg_match_all('/\b(\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]\d{2,4})\b/', $text, $matches)) {
            $info['dates'] = array_unique($matches[1]);
        }

        // Extract amounts (FCFA, EUR, USD)
        if (preg_match_all('/\b(\d{1,3}(?:\s?\d{3})*(?:[,\.]\d{2})?)\s?(FCFA|€|EUR|\$|USD)\b/i', $text, $matches)) {
            $info['amounts'] = array_unique($matches[0]);
        }

        // Extract emails
        if (preg_match_all('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $text, $matches)) {
            $info['emails'] = array_unique($matches[0]);
        }

        // Extract phone numbers (Burkina Faso format)
        if (preg_match_all('/\b(?:\+226\s?)?(?:\d{2}\s?\d{2}\s?\d{2}\s?\d{2})\b/', $text, $matches)) {
            $info['phones'] = array_unique($matches[0]);
        }

        return $info;
    }

    /**
     * Classify document type based on content
     */
    public function classifyDocument(string $text): string
    {
        $text = strtolower($text);

        $patterns = [
            'fiche-administrative' => ['fiche', 'accompagnement', 'courrier', 'commune', 'ouagadougou', 'direction', 'imputation'],
            'demande-stage' => ['demande', 'stage', 'formation', 'stagiaire', 'étudiant'],
            'courrier-officiel' => ['république', 'burkina', 'faso', 'ministère', 'secrétariat', 'général'],
            'facture' => ['facture', 'invoice', 'montant', 'total', 'tva', 'prix', 'fcfa'],
            'contrat' => ['contrat', 'contract', 'accord', 'convention', 'signataire', 'article'],
            'rapport' => ['rapport', 'report', 'analyse', 'conclusion', 'recommandation', 'activité'],
            'lettre' => ['monsieur', 'madame', 'objet', 'courrier', 'correspondance', 'honneur'],
            'certificat' => ['certificat', 'attestation', 'diplôme', 'certification'],
            'procès-verbal' => ['procès-verbal', 'réunion', 'assemblée', 'délibération']
        ];

        foreach ($patterns as $type => $keywords) {
            $matches = 0;
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    $matches++;
                }
            }
            if ($matches >= 2) {
                return $type;
            }
        }

        return 'document';
    }
}
