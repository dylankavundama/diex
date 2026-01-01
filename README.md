# Diexo E-commerce - Site de vente en ligne

Site e-commerce complet pour la vente de vêtements, articles ménagers et décoration intérieure.

## Fonctionnalités

### Interface Publique
- Catalogue de produits avec recherche et filtres
- Détails produits avec images multiples
- Commande via WhatsApp
- Inscription et connexion clients

### Panneau Administrateur
- Dashboard avec statistiques complètes
- Gestion des produits (ajout, modification, suppression)
- Gestion des commandes et statuts
- Gestion des utilisateurs (clients et vendeurs)
- Gestion financière (dettes, paiements, bénéfices)
- Rapports journaliers, mensuels et annuels
- Statistiques de visite

### Panneau Vendeur
- Dashboard personnel
- Gestion de ses propres produits
- Suivi des commandes
- Statistiques de ventes et bénéfices

## Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx) ou XAMPP/WAMP
- Extension PHP mysqli activée

### Étapes d'installation

1. **Télécharger les fichiers**
   - Placez tous les fichiers dans le dossier `htdocs` de XAMPP (ou équivalent)

2. **Créer la base de données**
   ```sql
   CREATE DATABASE diexo_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Importer le schéma**
   - Ouvrez phpMyAdmin
   - Sélectionnez la base de données `diexo_ecommerce`
   - Allez dans l'onglet "Importer"
   - Sélectionnez le fichier `database/schema.sql`
   - Cliquez sur "Exécuter"

4. **Configurer la connexion**
   - Éditez le fichier `config/database.php`
   - Modifiez les constantes si nécessaire :
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'diexo_ecommerce');
     ```

5. **Configurer l'URL du site**
   - Éditez le fichier `config/config.php`
   - Modifiez `SITE_URL` selon votre configuration :
     ```php
     define('SITE_URL', 'http://localhost/diexo');
     ```

6. **Configurer WhatsApp**
   - Éditez le fichier `config/config.php`
   - Modifiez `WHATSAPP_NUMBER` avec votre numéro :
     ```php
     define('WHATSAPP_NUMBER', '221XXXXXXXX'); // Format international sans +
     ```

7. **Créer le dossier uploads**
   - Créez le dossier `uploads/products/` à la racine du projet
   - Assurez-vous que les permissions d'écriture sont activées

8. **Compte administrateur par défaut**
   - Email: `admin@diexo.com`
   - Mot de passe: `admin123`
   - ⚠️ **Changez ce mot de passe après la première connexion !**

## Structure des dossiers

```
diexo/
├── admin/              # Panneau administrateur
├── assets/             # CSS, JS, images
│   ├── css/
│   └── js/
├── auth/               # Authentification
├── config/             # Configuration
├── database/           # Schéma SQL
├── includes/           # Fichiers inclus (header, footer)
├── uploads/            # Images uploadées
├── vendeur/            # Panneau vendeur
├── index.php           # Page d'accueil
├── shop.php            # Boutique
├── product.php         # Détails produit
└── profile.php         # Profil utilisateur
```

## Utilisation

### Créer un compte vendeur
1. Connectez-vous en tant qu'administrateur
2. Allez dans "Gérer les utilisateurs"
3. Créez un nouvel utilisateur avec le rôle "vendeur"

### Ajouter un produit
1. Connectez-vous en tant qu'admin ou vendeur
2. Allez dans "Gérer les produits"
3. Cliquez sur "Ajouter un produit"
4. Remplissez les informations et uploadez une image

### Gérer les commandes
1. Les commandes arrivent via WhatsApp
2. Dans le panneau admin, allez dans "Gestion des commandes"
3. Mettez à jour le statut selon l'avancement

### Gérer les finances
1. Dans le panneau admin, allez dans "Finances"
2. Enregistrez les paiements des clients
3. Consultez les rapports pour voir les bénéfices

## Sécurité

- Les mots de passe sont hashés avec `password_hash()`
- Protection contre les injections SQL avec les requêtes préparées
- Sanitisation des données utilisateur
- Sessions sécurisées
- Vérification des rôles pour l'accès aux pages

## Personnalisation

### Modifier les catégories
- Éditez directement dans la base de données la table `categories`
- Ou ajoutez une interface d'administration pour les catégories

### Modifier le design
- Les styles sont dans `assets/css/style.css`
- Les variables CSS permettent de changer facilement les couleurs

### Intégration WhatsApp API
- Pour une intégration complète avec l'API WhatsApp Business, vous devrez :
  1. Créer un compte WhatsApp Business API
  2. Obtenir les credentials
  3. Modifier le code dans `product.php` pour utiliser l'API

## Support

Pour toute question ou problème, consultez la documentation PHP et MySQL.

## Licence

Ce projet est fourni tel quel pour usage personnel ou commercial.

