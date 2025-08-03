<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\PerfectOCRService;

echo "=== TEST DU NOUVEAU SYSTÈME OCR ===\n\n";

try {
    // Créer une instance du service
    $perfectOCR = new PerfectOCRService();
    echo "✅ Service PerfectOCRService créé avec succès\n\n";

    // Créer un fichier image de test
    $testImagePath = storage_path('app/temp/test_ocr.txt');

    // Créer le dossier temp s'il n'existe pas
    $tempDir = dirname($testImagePath);
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // Créer un fichier de test (simule une image)
    file_put_contents($testImagePath, "Ceci est un test");
    echo "📄 Fichier de test créé : $testImagePath\n\n";

    // Tester l'extraction
    echo "🔍 Test d'extraction en cours...\n";
    $result = $perfectOCR->extractText($testImagePath);

    echo "✅ RÉSULTAT DE L'EXTRACTION :\n";
    echo str_repeat("=", 60) . "\n";
    echo $result . "\n";
    echo str_repeat("=", 60) . "\n\n";

    // Nettoyer
    unlink($testImagePath);
    echo "✅ Test terminé avec succès !\n";

} catch (Exception $e) {
    echo "❌ Erreur durant le test : " . $e->getMessage() . "\n";
}
