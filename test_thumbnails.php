<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Document;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Test de génération de thumbnails pour tous les documents\n";
echo "======================================================\n\n";

$controller = new DocumentController();
$documents = Document::all();

if ($documents->count() == 0) {
    echo "Aucun document trouvé dans la base de données.\n";
    exit;
}

foreach ($documents as $document) {
    echo "Document: {$document->original_filename}\n";
    echo "Type: {$document->mime_type}\n";
    echo "Chemin: {$document->file_path}\n";

    // Vérifier si le fichier existe
    $filePath = storage_path('app/public/' . $document->file_path);
    if (!file_exists($filePath)) {
        echo "❌ Fichier non trouvé: $filePath\n\n";
        continue;
    }

    try {
        $thumbnailPath = $controller->generateThumbnail($document);

        if ($thumbnailPath) {
            echo "✅ Thumbnail généré: $thumbnailPath\n";

            // Mettre à jour le document
            $document->thumbnail_path = $thumbnailPath;
            $document->save();
            echo "✅ Document mis à jour avec le chemin du thumbnail\n";
        } else {
            echo "⚠️  Aucun thumbnail généré\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }

    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "Test terminé !\n";
