# Diexo Mobile - Application Flutter

Application mobile Flutter pour les administrateurs et vendeurs de Diexo.

## Structure de l'application

```
lib/
├── main.dart                 # Point d'entrée de l'application
├── models/                   # Modèles de données
│   ├── user.dart
│   ├── product.dart
│   └── order.dart
├── providers/                # Gestion d'état (Provider)
│   ├── auth_provider.dart
│   ├── product_provider.dart
│   └── order_provider.dart
├── services/                 # Services API
│   └── api_service.dart
├── screens/                  # Écrans de l'application
│   ├── auth/
│   │   └── login_screen.dart
│   ├── admin/
│   │   ├── admin_home_screen.dart
│   │   ├── admin_dashboard_screen.dart
│   │   ├── admin_products_screen.dart
│   │   ├── admin_orders_screen.dart
│   │   ├── admin_financial_screen.dart
│   │   ├── admin_reports_screen.dart
│   │   └── admin_cash_screen.dart
│   └── vendeur/
│       ├── vendeur_home_screen.dart
│       ├── vendeur_dashboard_screen.dart
│       ├── vendeur_products_screen.dart
│       ├── vendeur_sales_screen.dart
│       ├── vendeur_reports_screen.dart
│       └── vendeur_cash_screen.dart
└── utils/
    └── constants.dart        # Constantes de l'application
```

## Configuration

### 1. Modifier l'URL de base

Éditez `lib/utils/constants.dart` et modifiez `baseUrl` :

```dart
static const String baseUrl = 'http://votre-ip:port/diexo';
```

Pour tester sur un appareil physique, remplacez `localhost` par l'IP de votre machine.

### 2. Installation des dépendances

```bash
cd mobile_app
flutter pub get
```

### 3. Exécution

```bash
# Android
flutter run

# iOS
flutter run -d ios

# Web (pour tester)
flutter run -d chrome
```

## API REST

L'application communique avec le backend PHP via l'API REST située dans le dossier `api/`.

### Endpoints disponibles

- `POST /api/auth/login.php` - Authentification
- `GET /api/dashboard.php?role=admin|vendeur` - Statistiques du tableau de bord
- `GET /api/products.php` - Liste des produits
- `GET /api/products.php?id=X` - Détails d'un produit
- `GET /api/orders.php` - Liste des commandes
- `GET /api/orders.php?id=X` - Détails d'une commande

## Fonctionnalités

### Admin
- ✅ Tableau de bord avec statistiques
- ✅ Liste des produits
- ✅ Liste des commandes
- ⏳ Gestion financière (à implémenter)
- ⏳ Rapports (à implémenter)
- ⏳ Gestion de caisse (à implémenter)

### Vendeur
- ✅ Tableau de bord avec statistiques
- ✅ Liste des produits
- ⏳ Création de ventes (à implémenter)
- ⏳ Rapports (à implémenter)
- ⏳ Gestion de caisse (à implémenter)

## Développement

### Ajouter un nouvel écran

1. Créer le fichier dans `lib/screens/admin/` ou `lib/screens/vendeur/`
2. Ajouter la route dans `admin_home_screen.dart` ou `vendeur_home_screen.dart`
3. Implémenter la logique métier

### Ajouter un nouvel endpoint API

1. Créer le fichier PHP dans `api/`
2. Ajouter la méthode dans `lib/services/api_service.dart`
3. Utiliser dans les providers ou écrans

## Notes

- L'authentification utilise les sessions PHP
- Les images des produits sont chargées depuis `uploads/products/`
- Le taux de change USD/CDF est défini dans `constants.dart` (2200 CDF = 1 USD)

