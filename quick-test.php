<?php

echo "Test API OCR rapide...\n";

$postData = http_build_query([
    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Exemple_facture.png/300px-Exemple_facture.png',
    'language' => 'fre'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => $postData,
        'timeout' => 10
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

echo "Envoi de la requête...\n";
$result = file_get_contents('https://api.ocr.space/parse/image', false, $context);

if ($result) {
    echo "✅ Réponse reçue !\n";
    $data = json_decode($result, true);
    if ($data && isset($data['ParsedResults'][0]['ParsedText'])) {
        echo "🎯 TEXTE DÉTECTÉ: " . substr($data['ParsedResults'][0]['ParsedText'], 0, 100) . "...\n";
        echo "✅ L'API OCR fonctionne parfaitement !\n";
    } else {
        echo "❌ Pas de texte dans la réponse\n";
        echo "Réponse: " . substr($result, 0, 300) . "\n";
    }
} else {
    echo "❌ Pas de réponse\n";
    $error = error_get_last();
    if ($error) {
        echo "Erreur: " . $error['message'] . "\n";
    }
}

echo "Test terminé.\n";
