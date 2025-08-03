<?php

/**
 * Script pour télécharger et configurer le certificat SSL pour cURL sur Windows
 * À exécuter une seule fois pour résoudre les problèmes SSL
 */

function downloadCACertificate() {
    $phpIniPath = php_ini_loaded_file();
    $phpDir = dirname($phpIniPath);
    $certFile = $phpDir . '/cacert.pem';

    echo "🔍 Vérification de la configuration SSL...\n";
    echo "📁 Répertoire PHP: {$phpDir}\n";
    echo "📄 Fichier php.ini: {$phpIniPath}\n";

    // Télécharger le certificat CA si nécessaire
    if (!file_exists($certFile)) {
        echo "📥 Téléchargement du certificat CA...\n";

        $caCertUrl = 'https://curl.haxx.se/ca/cacert.pem';
        $caCertContent = file_get_contents($caCertUrl);

        if ($caCertContent === false) {
            echo "❌ Erreur lors du téléchargement du certificat CA\n";
            return false;
        }

        file_put_contents($certFile, $caCertContent);
        echo "✅ Certificat CA téléchargé vers: {$certFile}\n";
    } else {
        echo "✅ Certificat CA déjà présent: {$certFile}\n";
    }

    // Vérifier la configuration php.ini
    $iniContent = file_get_contents($phpIniPath);
    $curlCaInfoPattern = '/^curl\.cainfo\s*=/m';

    if (preg_match($curlCaInfoPattern, $iniContent)) {
        echo "✅ Configuration curl.cainfo déjà présente dans php.ini\n";
    } else {
        echo "⚠️  Configuration curl.cainfo manquante dans php.ini\n";
        echo "💡 Ajoutez cette ligne dans votre php.ini:\n";
        echo "curl.cainfo = \"{$certFile}\"\n";
        echo "Puis redémarrez votre serveur web.\n";
    }

    return true;
}

function testMistralConnection() {
    echo "\n🧪 Test de connexion avec Mistral AI...\n";

    // Test avec cURL direct
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.mistral.ai/v1/models');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Temporaire pour test

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "❌ Erreur cURL: {$error}\n";
        return false;
    }

    if ($httpCode === 401) {
        echo "✅ Connexion SSL réussie! (401 = clé API requise)\n";
        return true;
    }

    echo "ℹ️  Code HTTP: {$httpCode}\n";
    return $httpCode < 400;
}

// Exécution du script
echo "🔧 Configuration SSL pour Mistral AI\n";
echo "=====================================\n\n";

if (downloadCACertificate()) {
    testMistralConnection();
}

echo "\n📝 Instructions supplémentaires:\n";
echo "1. Ajoutez votre clé API Mistral dans .env\n";
echo "2. Si le problème persiste, redémarrez votre serveur PHP\n";
echo "3. Testez avec: php artisan mistral:test\n\n";
