# Configuration Mistral AI pour Document Digitizer

## 🚀 Obtenir une clé API Mistral gratuite

### Étape 1: Créer un compte Mistral
1. Allez sur [https://console.mistral.ai/](https://console.mistral.ai/)
2. Cliquez sur "Sign up" pour créer un compte
3. Vérifiez votre email et connectez-vous

### Étape 2: Obtenir la clé API
1. Une fois connecté, allez dans la section "API Keys"
2. Cliquez sur "Create new key"
3. Donnez un nom à votre clé (ex: "Document-Digitizer")
4. Copiez la clé générée

### Étape 3: Configuration dans Laravel
1. Ouvrez votre fichier `.env`
2. Remplacez `your_mistral_api_key_here` par votre vraie clé :
```env
MISTRAL_API_KEY=votre_clé_mistral_ici
```

### Étape 4: Test de l'intégration
Exécutez la commande de test :
```bash
php artisan mistral:test
```

## 🎯 Fonctionnalités disponibles

### Classification automatique
Le système peut identifier automatiquement le type de document :
- 📄 Factures
- 📋 Contrats 
- 📊 Rapports
- 📮 Lettres
- 🎓 Certificats
- 📝 Procès-verbaux

### Extraction d'informations
- 📅 Dates (multiples formats)
- 💰 Montants (FCFA, EUR, USD)
- 📧 Adresses email
- 📞 Numéros de téléphone (format Burkina Faso)

### Résumé IA intelligent
- Résumé structuré selon le type de document
- Informations clés extraites
- Recommandations et actions à suivre
- Adapté au contexte burkinabè

## 🔧 Utilisation dans l'interface

1. **Upload d'un document** : Le système lance automatiquement l'OCR
2. **Analyse IA** : Cliquez sur "Analyser avec Mistral IA" 
3. **Résultats** : Obtenez un résumé structuré et des informations clés

## 💡 Conseils d'optimisation

### Pour les factures
- Assurez-vous que les montants sont bien visibles
- Les dates doivent être claires
- Les informations de facturation complètes

### Pour les contrats
- Texte bien structuré avec parties clairement identifiées
- Dates de début/fin visibles
- Signatures et cachets nets

### Pour les rapports
- Titres et sections bien délimitées
- Conclusions et recommandations distinctes
- Tableaux et chiffres lisibles

## 🛡️ Sécurité et confidentialité

- ✅ Les documents sont traités localement pour l'OCR
- ✅ Seul le texte extrait est envoyé à Mistral (pas les images)
- ✅ Aucune donnée n'est stockée chez Mistral
- ✅ Chiffrement des communications
- ✅ Respect des normes burkinabè de confidentialité

## 📊 Limites du plan gratuit Mistral

- **Modèle** : mistral-small-latest
- **Limite** : Environ 1000 requêtes/mois
- **Tokens** : ~1000 tokens par analyse
- **Idéal pour** : Tests et petits volumes

## 🚀 Évolutions futures

- [ ] Support de modèles plus avancés
- [ ] Analyse d'images directe
- [ ] Classification de documents manuscrits
- [ ] Integration avec l'API OCR locale
- [ ] Résumés multilingues (français, mooré, dioula)

## 🆘 Dépannage

### Erreur "Clé API non configurée"
```bash
# Vérifiez votre configuration
php artisan config:cache
php artisan mistral:test
```

### Erreur de connexion
- Vérifiez votre connexion internet
- Assurez-vous que la clé API est valide
- Vérifiez les logs Laravel : `storage/logs/laravel.log`

### Timeout ou erreurs de limite
- Le plan gratuit a des limites de débit
- Attendez quelques minutes entre les requêtes
- Considérez l'upgrade vers un plan payant pour plus de volume

## 📞 Support

Pour toute question technique :
- 📧 Email : support@document-digitizer.bf
- 📱 WhatsApp : +226 XX XX XX XX
- 🌐 Documentation : [https://docs.document-digitizer.bf](https://docs.document-digitizer.bf)
