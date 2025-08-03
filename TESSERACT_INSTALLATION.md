# Guide d'installation Tesseract OCR pour Windows

## Étapes d'installation

### 1. Télécharger Tesseract
- Allez sur : https://github.com/UB-Mannheim/tesseract/wiki
- Téléchargez la version Windows 64-bit la plus récente
- Fichier recommandé : `tesseract-ocr-w64-setup-5.3.x.exe`

### 2. Installation
- Exécutez le fichier téléchargé en tant qu'administrateur
- **IMPORTANT** : Pendant l'installation, cochez "Additional language data (French)"
- Installez dans le répertoire par défaut : `C:\Program Files\Tesseract-OCR`

### 3. Configuration du PATH
- Ouvrez les "Variables d'environnement système"
- Ajoutez `C:\Program Files\Tesseract-OCR` au PATH système
- OU créez une variable TESSDATA_PREFIX pointant vers `C:\Program Files\Tesseract-OCR\tessdata`

### 4. Vérification
Ouvrez une nouvelle invite de commande et tapez :
```cmd
tesseract --version
```

### 5. Redémarrage
- Redémarrez votre serveur web PHP
- Redémarrez le serveur Laravel

## Test après installation
Une fois installé, uploadez à nouveau un document. Le système devrait extraire automatiquement le texte de l'image au lieu d'afficher "ANALYSE BASIQUE DE L'IMAGE".

## Langues supportées
Le système est configuré pour reconnaître :
- Français (fra)
- Anglais (eng)

## Dépannage
Si Tesseract ne fonctionne toujours pas après installation :
1. Vérifiez que `tesseract.exe` est dans le PATH
2. Redémarrez complètement votre serveur
3. Vérifiez les logs Laravel pour les erreurs spécifiques
