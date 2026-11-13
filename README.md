# Digitizer — Numérisation de documents et factures

Application web qui numérise des documents papier : on importe un scan ou une photo, le texte est
extrait par OCR puis analysé pour en tirer les informations utiles (type de document, montants,
lignes de facture). Les factures extraites peuvent ensuite être gérées, payées et exportées en PDF.

## Fonctionnalités

- **Compte utilisateur** : inscription, connexion, vérification de l'adresse e-mail, profil,
  paramètres.
- **Documents** : import, liste, détail, téléchargement, suppression.
- **OCR** : extraction du texte d'un document (Tesseract en local, ou service OCR en ligne selon
  la configuration), relance du traitement.
- **Analyse intelligente** : le texte extrait est envoyé à l'API Mistral pour identifier le type
  de document et structurer son contenu.
- **Factures** : création à partir d'un document analysé, lignes de facture, génération PDF,
  enregistrement du paiement, statistiques.
- **Journal d'audit** des actions.
- **API JSON** d'un document (`/documents/{id}/api`).

## Stack technique

- PHP 8.2+, Laravel 12
- SQLite (par défaut) ou MySQL
- Tesseract OCR
- API Mistral AI (modèle `mistral-small-latest`)
- Tailwind CSS 3 (forms, typography), Vite

## Prérequis

- PHP 8.2+, Composer 2
- Node.js 20+
- Tesseract OCR installé sur la machine (sous Windows : `install-tesseract.bat`, voir
  `TESSERACT_INSTALLATION.md`)
- Une clé d'API Mistral (voir `MISTRAL_SETUP.md`)

## Installation

```bash
git clone https://github.com/kylaodou/digitizer.git
cd digitizer

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Ajoutez dans `.env` la clé Mistral et, si besoin, le chemin de Tesseract :

```env
MISTRAL_API_KEY=votre_cle
```

Créez la base et les tables :

```bash
php artisan migrate
php artisan storage:link
```

## Lancement

```bash
npm run dev
php artisan serve
```

L'application est disponible sur http://localhost:8000.

## Documentation complémentaire

| Fichier | Contenu |
|---|---|
| `GUIDE-OCR-COMPLET.md` | Fonctionnement de la chaîne OCR |
| `TESSERACT_INSTALLATION.md`, `INSTALL_TESSERACT_EXPRESS.md` | Installation de Tesseract |
| `MISTRAL_SETUP.md` | Configuration de l'API Mistral |

## Auteur

[@kylaodou](https://github.com/kylaodou)
