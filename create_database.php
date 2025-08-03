<?php

try {
    // Connexion MySQL sans spécifier de base de données
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Créer la base de données
    $sql = "CREATE DATABASE IF NOT EXISTS document_digitizer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);

    echo "Base de données 'document_digitizer' créée avec succès!\n";

} catch (PDOException $e) {
    echo "Erreur lors de la création de la base de données: " . $e->getMessage() . "\n";

    // Essayer avec un mot de passe vide
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=3306", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "CREATE DATABASE IF NOT EXISTS document_digitizer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $pdo->exec($sql);

        echo "Base de données 'document_digitizer' créée avec succès (sans mot de passe)!\n";
    } catch (PDOException $e2) {
        echo "Erreur: " . $e2->getMessage() . "\n";
        echo "Veuillez créer manuellement la base de données 'document_digitizer' dans votre serveur MySQL.\n";
    }
}
