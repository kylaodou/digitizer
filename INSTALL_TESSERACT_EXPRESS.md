# Installation Tesseract OCR sur Windows - Guide Express

## 🚀 Installation en 5 minutes

### Étape 1 : Télécharger
1. Allez sur : https://github.com/UB-Mannheim/tesseract/wiki
2. Téléchargez : **tesseract-ocr-w64-setup-5.3.3.20231005.exe** (64-bit)

### Étape 2 : Installer
1. **Exécutez en tant qu'administrateur**
2. **IMPORTANT** : Cochez "Additional language data (French)" pendant l'installation
3. Installez dans : `C:\Program Files\Tesseract-OCR`

### Étape 3 : Configurer le PATH
**Méthode rapide :**
1. Appuyez sur `Windows + R`
2. Tapez : `sysdm.cpl` et Entrée
3. Onglet "Avancé" → "Variables d'environnement"
4. Dans "Variables système", sélectionnez "Path" → "Modifier"
5. Cliquez "Nouveau" et ajoutez : `C:\Program Files\Tesseract-OCR`
6. OK partout

### Étape 4 : Vérifier
1. Ouvrez une **nouvelle** invite de commande
2. Tapez : `tesseract --version`
3. Si ça marche → ✅ Installation réussie !

### Étape 5 : Redémarrer le serveur
```bash
# Arrêtez votre serveur Laravel (Ctrl+C)
# Puis relancez :
php artisan serve
```

**Résultat : Extraction de texte RÉELLE et fidèle ! 🎯**
