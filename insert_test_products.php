<?php
/**
 * Script PHP pour insérer 18 produits de test
 * Exécutez ce fichier dans votre navigateur
 */
require_once 'config/database.php';
// Ne pas charger config.php pour éviter les problèmes de session

$conn = getDBConnection();
$errors = [];
$success = [];

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Option pour supprimer les produits existants avant insertion
$force_insert = isset($_GET['force']) && $_GET['force'] === '1';

if ($force_insert) {
    // Supprimer les produits de test existants
    $delete_query = "DELETE FROM products WHERE nom IN (
        'T-shirt Enfant Coton Bio',
        'Robe Fille Fleurie',
        'Pantalon Garçon Jeans',
        'Chemise Homme Élégante',
        'Robe Femme Élégante',
        'Jeans Femme Taille Haute',
        'Service de Table 12 Personnes',
        'Verres à Vin Cristal',
        'Couverts Inox 24 Pièces',
        'Mixeur Blender 500W',
        'Bouilloire Électrique 1.7L',
        'Grille-Pain 4 Fentes',
        'Set Ustensiles de Cuisine',
        'Planche à Découper Bambou',
        'Table Basse Moderne',
        'Chaise Design Scandinave',
        'Étagère Murale 5 Niveaux',
        'Vase Décoratif Céramique',
        'Tableau Décoratif Moderne',
        'Miroir Décoratif Murale',
        'Lampe de Bureau LED',
        'Lustre Moderne 5 Bras'
    )";
    $conn->query($delete_query);
    $success[] = "Produits de test existants supprimés.";
}

// Vérifier que les catégories existent
$categories_check = $conn->query("SELECT id, nom FROM categories ORDER BY id");
$categories = [];
while ($row = $categories_check->fetch_assoc()) {
    $categories[$row['id']] = $row['nom'];
}

if (empty($categories)) {
    $errors[] = "Aucune catégorie trouvée. Veuillez d'abord exécuter le script schema.sql";
}

// Produits à insérer
$products = [
    // Vêtements Enfants (catégorie 4)
    [
        'nom' => 'T-shirt Enfant Coton Bio',
        'description' => 'T-shirt 100% coton bio pour enfant, confortable et doux. Disponible en plusieurs couleurs et tailles (4-12 ans).',
        'prix_achat' => 2500,
        'prix_vente' => 4500,
        'stock' => 25,
        'stock_minimum' => 5,
        'categorie_id' => 4,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Robe Fille Fleurie',
        'description' => 'Belle robe fleurie pour petite fille, idéale pour les occasions spéciales. Tissu léger et respirant.',
        'prix_achat' => 3500,
        'prix_vente' => 6500,
        'stock' => 15,
        'stock_minimum' => 3,
        'categorie_id' => 4,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Pantalon Garçon Jeans',
        'description' => 'Pantalon jean classique pour garçon, résistant et confortable. Taille ajustable.',
        'prix_achat' => 4000,
        'prix_vente' => 7500,
        'stock' => 20,
        'stock_minimum' => 5,
        'categorie_id' => 4,
        'statut' => 'actif'
    ],
    // Vêtements Adultes (catégorie 5)
    [
        'nom' => 'Chemise Homme Élégante',
        'description' => 'Chemise homme en coton de qualité supérieure, parfaite pour le bureau ou les occasions formelles. Plusieurs couleurs disponibles.',
        'prix_achat' => 8000,
        'prix_vente' => 15000,
        'stock' => 30,
        'stock_minimum' => 5,
        'categorie_id' => 5,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Robe Femme Élégante',
        'description' => 'Robe élégante pour femme, design moderne et confortable. Idéale pour soirées et événements.',
        'prix_achat' => 12000,
        'prix_vente' => 22000,
        'stock' => 18,
        'stock_minimum' => 4,
        'categorie_id' => 5,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Jeans Femme Taille Haute',
        'description' => 'Jeans femme taille haute, coupe moderne et confortable. Plusieurs tailles disponibles.',
        'prix_achat' => 9000,
        'prix_vente' => 18000,
        'stock' => 22,
        'stock_minimum' => 5,
        'categorie_id' => 5,
        'statut' => 'actif'
    ],
    // Vaisselle (catégorie 7)
    [
        'nom' => 'Service de Table 12 Personnes',
        'description' => 'Service de table complet pour 12 personnes en porcelaine de qualité. Inclut assiettes, bols, tasses et soucoupes.',
        'prix_achat' => 25000,
        'prix_vente' => 45000,
        'stock' => 8,
        'stock_minimum' => 2,
        'categorie_id' => 7,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Verres à Vin Cristal',
        'description' => 'Set de 6 verres à vin en cristal, élégants et durables. Parfaits pour recevoir.',
        'prix_achat' => 8000,
        'prix_vente' => 15000,
        'stock' => 15,
        'stock_minimum' => 3,
        'categorie_id' => 7,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Couverts Inox 24 Pièces',
        'description' => 'Service de couverts en inox 18/10, 24 pièces. Design moderne et résistant aux taches.',
        'prix_achat' => 15000,
        'prix_vente' => 28000,
        'stock' => 12,
        'stock_minimum' => 3,
        'categorie_id' => 7,
        'statut' => 'actif'
    ],
    // Électroménagers (catégorie 8)
    [
        'nom' => 'Mixeur Blender 500W',
        'description' => 'Mixeur blender puissant 500W, idéal pour smoothies, soupes et préparations. Bol en verre 1.5L.',
        'prix_achat' => 18000,
        'prix_vente' => 32000,
        'stock' => 10,
        'stock_minimum' => 2,
        'categorie_id' => 8,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Bouilloire Électrique 1.7L',
        'description' => 'Bouilloire électrique rapide, capacité 1.7L. Arrêt automatique et indicateur de niveau.',
        'prix_achat' => 12000,
        'prix_vente' => 22000,
        'stock' => 15,
        'stock_minimum' => 3,
        'categorie_id' => 8,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Grille-Pain 4 Fentes',
        'description' => 'Grille-pain 4 fentes avec fonction décongélation. Contrôle de brunissement réglable.',
        'prix_achat' => 10000,
        'prix_vente' => 18000,
        'stock' => 12,
        'stock_minimum' => 3,
        'categorie_id' => 8,
        'statut' => 'actif'
    ],
    // Accessoires Cuisine (catégorie 9)
    [
        'nom' => 'Set Ustensiles de Cuisine',
        'description' => 'Set complet d\'ustensiles de cuisine en silicone et inox. 12 pièces essentielles pour votre cuisine.',
        'prix_achat' => 6000,
        'prix_vente' => 12000,
        'stock' => 20,
        'stock_minimum' => 5,
        'categorie_id' => 9,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Planche à Découper Bambou',
        'description' => 'Planche à découper en bambou écologique, antibactérienne et durable. 3 tailles disponibles.',
        'prix_achat' => 3000,
        'prix_vente' => 5500,
        'stock' => 25,
        'stock_minimum' => 5,
        'categorie_id' => 9,
        'statut' => 'actif'
    ],
    // Meubles (catégorie 10)
    [
        'nom' => 'Table Basse Moderne',
        'description' => 'Table basse moderne en bois massif avec étagère inférieure. Design épuré et élégant. Dimensions: 120x60x45cm.',
        'prix_achat' => 45000,
        'prix_vente' => 85000,
        'stock' => 5,
        'stock_minimum' => 1,
        'categorie_id' => 10,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Chaise Design Scandinave',
        'description' => 'Chaise design style scandinave, confortable et élégante. Bois naturel et assise rembourrée.',
        'prix_achat' => 25000,
        'prix_vente' => 48000,
        'stock' => 8,
        'stock_minimum' => 2,
        'categorie_id' => 10,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Étagère Murale 5 Niveaux',
        'description' => 'Étagère murale 5 niveaux en métal et bois, parfaite pour ranger et décorer. Facile à monter.',
        'prix_achat' => 18000,
        'prix_vente' => 35000,
        'stock' => 10,
        'stock_minimum' => 2,
        'categorie_id' => 10,
        'statut' => 'actif'
    ],
    // Objets Déco (catégorie 11)
    [
        'nom' => 'Vase Décoratif Céramique',
        'description' => 'Vase décoratif en céramique, design moderne et coloré. Parfait pour fleurs ou décoration seule.',
        'prix_achat' => 5000,
        'prix_vente' => 9500,
        'stock' => 18,
        'stock_minimum' => 4,
        'categorie_id' => 11,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Tableau Décoratif Moderne',
        'description' => 'Tableau décoratif moderne, impression haute qualité sur toile. Plusieurs motifs disponibles.',
        'prix_achat' => 8000,
        'prix_vente' => 15000,
        'stock' => 15,
        'stock_minimum' => 3,
        'categorie_id' => 11,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Miroir Décoratif Murale',
        'description' => 'Miroir décoratif mural avec cadre en bois, design élégant. Dimensions: 60x80cm.',
        'prix_achat' => 12000,
        'prix_vente' => 22000,
        'stock' => 8,
        'stock_minimum' => 2,
        'categorie_id' => 11,
        'statut' => 'actif'
    ],
    // Luminaires (catégorie 12)
    [
        'nom' => 'Lampe de Bureau LED',
        'description' => 'Lampe de bureau LED moderne, réglable en hauteur et intensité. Éclairage doux et confortable.',
        'prix_achat' => 15000,
        'prix_vente' => 28000,
        'stock' => 12,
        'stock_minimum' => 3,
        'categorie_id' => 12,
        'statut' => 'actif'
    ],
    [
        'nom' => 'Lustre Moderne 5 Bras',
        'description' => 'Lustre moderne 5 bras, design contemporain. Parfait pour salon ou salle à manger.',
        'prix_achat' => 35000,
        'prix_vente' => 65000,
        'stock' => 6,
        'stock_minimum' => 1,
        'categorie_id' => 12,
        'statut' => 'actif'
    ]
];

// Insérer les produits
$inserted = 0;
$skipped = 0;

foreach ($products as $product) {
    // Si force_insert est activé, on insère quand même (les anciens ont été supprimés)
    if (!$force_insert) {
        // Vérifier si le produit existe déjà
        $check = $conn->prepare("SELECT id FROM products WHERE nom = ?");
        $check->bind_param("s", $product['nom']);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            $skipped++;
            $check->close();
            continue;
        }
        $check->close();
    }
    
    // Vérifier que la catégorie existe
    if (!isset($categories[$product['categorie_id']])) {
        $errors[] = "Catégorie ID {$product['categorie_id']} n'existe pas pour le produit: {$product['nom']}";
        continue;
    }
    
    // Insérer le produit
    $stmt = $conn->prepare("INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddiiis", 
        $product['nom'],
        $product['description'],
        $product['prix_achat'],
        $product['prix_vente'],
        $product['stock'],
        $product['stock_minimum'],
        $product['categorie_id'],
        $product['statut']
    );
    
    if ($stmt->execute()) {
        $inserted++;
        $success[] = "✓ Produit ajouté: {$product['nom']}";
    } else {
        $errors[] = "✗ Erreur pour {$product['nom']}: " . $stmt->error;
    }
    
    $stmt->close();
}

// Statistiques
$total_query = $conn->query("SELECT COUNT(*) as total FROM products");
$total = $total_query->fetch_assoc()['total'];

// Définir SITE_URL si non défini
if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost/diexo');
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertion Produits de Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
        }
        .success {
            color: #27ae60;
            background: #d4edda;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
            font-size: 0.9rem;
        }
        .error {
            color: #e74c3c;
            background: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        .info {
            background: #d1ecf1;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 20px 0;
        }
        .stat-box {
            background: #3498db;
            color: white;
            padding: 1.5rem;
            border-radius: 5px;
            text-align: center;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 2rem;
        }
        .stat-box p {
            margin: 0.5rem 0 0 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Insertion de Produits de Test</h1>
        
        <div class="stats">
            <div class="stat-box">
                <h3><?php echo $inserted; ?></h3>
                <p>Produits insérés</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $skipped; ?></h3>
                <p>Produits existants</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $total; ?></h3>
                <p>Total produits</p>
            </div>
        </div>
        
        <?php if (!empty($success)): ?>
            <h2>Succès (<?php echo count($success); ?>):</h2>
            <div style="max-height: 300px; overflow-y: auto;">
                <?php foreach ($success as $msg): ?>
                    <div class="success"><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <h2>Erreurs (<?php echo count($errors); ?>):</h2>
            <?php foreach ($errors as $msg): ?>
                <div class="error"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if ($inserted > 0 || $skipped > 0): ?>
            <div class="info">
                <h3>✅ Insertion terminée !</h3>
                <p><strong><?php echo $inserted; ?></strong> nouveaux produits ont été ajoutés à la base de données.</p>
                <?php if ($skipped > 0): ?>
                    <p><strong><?php echo $skipped; ?></strong> produits existaient déjà et ont été ignorés.</p>
                    <p style="margin-top: 1rem;">
                        <a href="?force=1" style="color: #e74c3c; font-weight: bold;">
                            🔄 Cliquez ici pour supprimer les anciens produits et réinsérer tous les produits
                        </a>
                    </p>
                <?php endif; ?>
                <p>Vous pouvez maintenant voir les produits dans la boutique.</p>
            </div>
            
            <a href="shop.php" class="btn">Voir la boutique</a>
            <a href="admin/products.php" class="btn">Gérer les produits</a>
        <?php else: ?>
            <div class="info">
                <p>Aucun produit n'a été inséré. Vérifiez les erreurs ci-dessus.</p>
                <p style="margin-top: 1rem;">
                    <a href="?force=1" style="color: #e74c3c; font-weight: bold;">
                        🔄 Forcer l'insertion (supprime les anciens produits de test)
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

