<?php
/**
 * Script PHP pour insérer 4 produits dans chaque catégorie principale
 * Total: 12 produits (4 x 3 catégories)
 * Exécutez ce fichier dans votre navigateur
 */
require_once 'config/database.php';

// Définir SITE_URL si non défini
if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost/diexo');
}

$conn = getDBConnection();
$errors = [];
$success = [];

// Vérifier que les catégories existent
$categories_check = $conn->query("SELECT id, nom FROM categories WHERE parent_id IS NULL ORDER BY id");
$categories = [];
while ($row = $categories_check->fetch_assoc()) {
    $categories[$row['id']] = $row['nom'];
}

if (empty($categories)) {
    $errors[] = "Aucune catégorie principale trouvée. Veuillez d'abord exécuter le script schema.sql";
}

// Produits à insérer par catégorie
$products_by_category = [
    1 => [ // Vêtements
        [
            'nom' => 'T-shirt Homme Classique',
            'description' => 'T-shirt 100% coton pour homme, coupe régulière, confortable au quotidien. Disponible en plusieurs couleurs et tailles.',
            'prix_achat' => 8.00,
            'prix_vente' => 15.00,
            'stock' => 30,
            'stock_minimum' => 5,
            'categorie_id' => 1,
            'statut' => 'actif',
            'image_main' => 'product_23_main.jpg',
            'images' => ['product_23_1.jpg', 'product_23_2.jpg', 'product_23_3.jpg', 'product_23_4.jpg', 'product_23_5.jpg', 'product_23_6.jpg']
        ],
        [
            'nom' => 'Robe Été Femme',
            'description' => 'Robe légère et fluide pour l\'été, idéale pour les journées chaudes. Tissu respirant et coupe élégante.',
            'prix_achat' => 12.00,
            'prix_vente' => 25.00,
            'stock' => 20,
            'stock_minimum' => 4,
            'categorie_id' => 1,
            'statut' => 'actif',
            'image_main' => 'product_24_main.jpg',
            'images' => ['product_24_1.jpg', 'product_24_2.jpg', 'product_24_3.jpg', 'product_24_4.jpg', 'product_24_5.jpg', 'product_24_6.jpg']
        ],
        [
            'nom' => 'Pantalon Chino Homme',
            'description' => 'Pantalon chino élégant pour homme, parfait pour le bureau ou les occasions décontractées. Tissu résistant.',
            'prix_achat' => 15.00,
            'prix_vente' => 30.00,
            'stock' => 25,
            'stock_minimum' => 5,
            'categorie_id' => 1,
            'statut' => 'actif',
            'image_main' => 'product_25_main.jpg',
            'images' => ['product_25_1.jpg', 'product_25_2.jpg', 'product_25_3.jpg', 'product_25_4.jpg', 'product_25_5.jpg', 'product_25_6.jpg']
        ],
        [
            'nom' => 'Jupe Midi Femme',
            'description' => 'Jupe midi moderne pour femme, coupe A élégante. Disponible en plusieurs motifs et couleurs.',
            'prix_achat' => 10.00,
            'prix_vente' => 22.00,
            'stock' => 18,
            'stock_minimum' => 4,
            'categorie_id' => 1,
            'statut' => 'actif',
            'image_main' => 'product_26_main.jpg',
            'images' => ['product_26_1.jpg', 'product_26_2.jpg', 'product_26_3.jpg', 'product_26_4.jpg', 'product_26_5.jpg', 'product_26_6.jpg']
        ]
    ],
    2 => [ // Articles Ménagers
        [
            'nom' => 'Casserole Anti-Adhésive 24cm',
            'description' => 'Casserole en aluminium avec revêtement anti-adhésif, poignées ergonomiques. Parfaite pour la cuisine quotidienne.',
            'prix_achat' => 18.00,
            'prix_vente' => 35.00,
            'stock' => 15,
            'stock_minimum' => 3,
            'categorie_id' => 2,
            'statut' => 'actif',
            'image_main' => 'product_27_main.jpg',
            'images' => ['product_27_1.jpg', 'product_27_2.jpg', 'product_27_3.jpg', 'product_27_4.jpg', 'product_27_5.jpg', 'product_27_6.jpg']
        ],
        [
            'nom' => 'Set de Bols en Céramique 6 Pièces',
            'description' => 'Set de 6 bols en céramique de qualité, design moderne. Idéal pour le petit-déjeuner ou les salades.',
            'prix_achat' => 12.00,
            'prix_vente' => 24.00,
            'stock' => 20,
            'stock_minimum' => 4,
            'categorie_id' => 2,
            'statut' => 'actif',
            'image_main' => 'product_28_main.jpg',
            'images' => ['product_28_1.jpg', 'product_28_2.jpg', 'product_28_3.jpg', 'product_28_4.jpg', 'product_28_5.jpg', 'product_28_6.jpg']
        ],
        [
            'nom' => 'Machine à Café Expresso',
            'description' => 'Machine à café expresso automatique, préparation rapide et facile. Réservoir d\'eau amovible 1.5L.',
            'prix_achat' => 45.00,
            'prix_vente' => 85.00,
            'stock' => 8,
            'stock_minimum' => 2,
            'categorie_id' => 2,
            'statut' => 'actif',
            'image_main' => 'product_29_main.jpg',
            'images' => ['product_29_1.jpg', 'product_29_2.jpg', 'product_29_3.jpg', 'product_29_4.jpg', 'product_29_5.jpg', 'product_29_6.jpg']
        ],
        [
            'nom' => 'Set de Casseroles 3 Pièces',
            'description' => 'Set complet de 3 casseroles avec couvercles en verre, différentes tailles. Compatible tous feux.',
            'prix_achat' => 35.00,
            'prix_vente' => 65.00,
            'stock' => 12,
            'stock_minimum' => 3,
            'categorie_id' => 2,
            'statut' => 'actif',
            'image_main' => 'product_30_main.jpg',
            'images' => ['product_30_1.jpg', 'product_30_2.jpg', 'product_30_3.jpg', 'product_30_4.jpg', 'product_30_5.jpg', 'product_30_6.jpg']
        ]
    ],
    3 => [ // Décoration Intérieure
        [
            'nom' => 'Canapé 3 Places Moderne',
            'description' => 'Canapé 3 places design moderne, tissu résistant et confortable. Parfait pour le salon. Dimensions: 200x90x85cm.',
            'prix_achat' => 180.00,
            'prix_vente' => 350.00,
            'stock' => 5,
            'stock_minimum' => 1,
            'categorie_id' => 3,
            'statut' => 'actif',
            'image_main' => 'product_31_main.jpg',
            'images' => ['product_31_1.jpg', 'product_31_2.jpg', 'product_31_3.jpg', 'product_31_4.jpg', 'product_31_5.jpg', 'product_31_6.jpg']
        ],
        [
            'nom' => 'Tapis Décoratif 200x300cm',
            'description' => 'Tapis décoratif en laine, design contemporain. Doux et confortable sous les pieds. Plusieurs motifs disponibles.',
            'prix_achat' => 45.00,
            'prix_vente' => 90.00,
            'stock' => 10,
            'stock_minimum' => 2,
            'categorie_id' => 3,
            'statut' => 'actif',
            'image_main' => 'product_32_main.jpg',
            'images' => ['product_32_1.jpg', 'product_32_2.jpg', 'product_32_3.jpg', 'product_32_4.jpg', 'product_32_5.jpg', 'product_32_6.jpg']
        ],
        [
            'nom' => 'Plante Décorative Artificielle',
            'description' => 'Plante artificielle haute qualité, aspect naturel. Parfaite pour décorer sans entretien. Pot inclus.',
            'prix_achat' => 15.00,
            'prix_vente' => 30.00,
            'stock' => 25,
            'stock_minimum' => 5,
            'categorie_id' => 3,
            'statut' => 'actif',
            'image_main' => 'product_33_main.jpg',
            'images' => ['product_33_1.jpg', 'product_33_2.jpg', 'product_33_3.jpg', 'product_33_4.jpg', 'product_33_5.jpg', 'product_33_6.jpg']
        ],
        [
            'nom' => 'Horloge Murale Design',
            'description' => 'Horloge murale design moderne, grand cadran 40cm. Silencieuse et élégante. Plusieurs styles disponibles.',
            'prix_achat' => 12.00,
            'prix_vente' => 25.00,
            'stock' => 20,
            'stock_minimum' => 4,
            'categorie_id' => 3,
            'statut' => 'actif',
            'image_main' => 'product_34_main.jpg',
            'images' => ['product_34_1.jpg', 'product_34_2.jpg', 'product_34_3.jpg', 'product_34_4.jpg', 'product_34_5.jpg', 'product_34_6.jpg']
        ]
    ]
];

$inserted = 0;
$skipped = 0;
$images_inserted = 0;

foreach ($products_by_category as $category_id => $products) {
    if (!isset($categories[$category_id])) {
        $errors[] = "Catégorie ID $category_id n'existe pas";
        continue;
    }
    
    foreach ($products as $product) {
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
        
        // Insérer le produit
        $stmt = $conn->prepare("INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddiiiss", 
            $product['nom'],
            $product['description'],
            $product['prix_achat'],
            $product['prix_vente'],
            $product['stock'],
            $product['stock_minimum'],
            $product['categorie_id'],
            $product['image_main'],
            $product['statut']
        );
        
        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            $inserted++;
            $success[] = "✓ Produit ajouté: {$product['nom']} (Catégorie: {$categories[$category_id]})";
            
            // Insérer les images supplémentaires
            if (isset($product['images']) && is_array($product['images'])) {
                foreach ($product['images'] as $index => $image_name) {
                    $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path, ordre) VALUES (?, ?, ?)");
                    $ordre = $index + 1;
                    $img_stmt->bind_param("isi", $product_id, $image_name, $ordre);
                    if ($img_stmt->execute()) {
                        $images_inserted++;
                    }
                    $img_stmt->close();
                }
            }
        } else {
            $errors[] = "✗ Erreur pour {$product['nom']}: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Statistiques par catégorie
$stats_query = $conn->query("SELECT c.nom, COUNT(p.id) as count 
                             FROM categories c 
                             LEFT JOIN products p ON c.id = p.categorie_id 
                             WHERE c.parent_id IS NULL 
                             GROUP BY c.id, c.nom 
                             ORDER BY c.id");

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertion Produits par Catégorie</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
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
            padding: 8px;
            border-radius: 5px;
            margin: 5px 0;
            font-size: 0.9rem;
        }
        .error {
            color: #e74c3c;
            background: #f8d7da;
            padding: 8px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            margin-right: 10px;
        }
        .btn:hover {
            background: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Insertion de 4 Produits par Catégorie</h1>
        
        <div class="stats">
            <div class="stat-box">
                <h3><?php echo $inserted; ?></h3>
                <p>Produits insérés</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $images_inserted; ?></h3>
                <p>Images ajoutées</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $skipped; ?></h3>
                <p>Produits existants</p>
            </div>
        </div>
        
        <?php if (!empty($success)): ?>
            <h2>✅ Succès (<?php echo count($success); ?>):</h2>
            <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                <?php foreach ($success as $msg): ?>
                    <div class="success"><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <h2>❌ Erreurs (<?php echo count($errors); ?>):</h2>
            <?php foreach ($errors as $msg): ?>
                <div class="error"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if ($stats_query->num_rows > 0): ?>
            <div class="info">
                <h3>Répartition par catégorie:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Nombre de produits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($stat = $stats_query->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stat['nom']); ?></td>
                                <td><strong><?php echo $stat['count']; ?></strong></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if ($inserted > 0): ?>
            <div class="info">
                <h3>✅ Insertion terminée !</h3>
                <p><strong><?php echo $inserted; ?></strong> nouveaux produits ont été ajoutés.</p>
                <p><strong><?php echo $images_inserted; ?></strong> images supplémentaires ont été associées.</p>
                <p>Chaque catégorie principale a maintenant au moins 4 produits.</p>
                <p><strong>Note:</strong> Les noms d'images sont enregistrés. Exécutez <code>generate_product_images.php</code> pour générer les images placeholder.</p>
            </div>
            
            <a href="<?php echo SITE_URL; ?>/shop.php" class="btn">Voir la boutique</a>
            <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn">Gérer les produits</a>
            <a href="<?php echo SITE_URL; ?>/generate_product_images.php" class="btn">Générer les images</a>
        <?php endif; ?>
    </div>
</body>
</html>

