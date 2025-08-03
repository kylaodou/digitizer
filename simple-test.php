<?php

echo "🔍 TEST API OCR SIMPLE\n";
echo str_repeat('=', 30) . "\n";

// Test basique de connectivité
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.ocr.space/parse/image');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Exemple_facture.png/300px-Exemple_facture.png',
    'language' => 'fre'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
// Configuration SSL pour Windows
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$result = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Status HTTP: $status\n";

if ($error) {
    echo "❌ Erreur cURL: $error\n";
} else {
    echo "✅ Connexion réussie !\n";

    if ($result) {
        $data = json_decode($result, true);
        if ($data && isset($data['ParsedResults'][0]['ParsedText'])) {
            $text = $data['ParsedResults'][0]['ParsedText'];
            echo "🎯 TEXTE DÉTECTÉ: " . substr($text, 0, 100) . "...\n";
        } else {
            echo "❌ Pas de texte détecté\n";
            echo "Réponse: " . substr($result, 0, 200) . "...\n";
        }
    } else {
        echo "❌ Pas de réponse\n";
    }
}

echo "\n🔧 DIAGNOSTIC :\n";
echo "- Connexion internet : " . (empty($error) ? "✅ OK" : "❌ Problème") . "\n";
echo "- API OCR.space : " . ($status == 200 ? "✅ OK" : "❌ Problème") . "\n";

echo "\n✅ Test terminé !\n";
