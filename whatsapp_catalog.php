<?php
/**
 * Script pour générer un catalogue de produits au format WhatsApp
 * Les clients peuvent utiliser ce lien pour recevoir le catalogue complet
 */
require_once 'config/config.php';
require_once 'config/database.php';

$conn = getDBConnection();

// Récupérer toutes les catégories avec leurs produits
$categories = $conn->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY nom");

$catalog_message = "📦 *CATALOGUE DIEXO E-COMMERCE*\n\n";
$catalog_message .= "Bienvenue dans notre boutique en ligne !\n\n";
$catalog_message .= "Voici notre catalogue de produits :\n\n";

while ($category = $categories->fetch_assoc()) {
    $catalog_message .= "━━━━━━━━━━━━━━━━━━━━\n";
    $catalog_message .= "🛍️ *" . strtoupper($category['nom']) . "*\n";
    $catalog_message .= "━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Récupérer les produits de cette catégorie
    $products = $conn->query("SELECT * FROM products 
                              WHERE categorie_id = {$category['id']} 
                              AND statut = 'actif' 
                              AND stock > 0 
                              ORDER BY nom 
                              LIMIT 20");
    
    if ($products->num_rows > 0) {
        while ($product = $products->fetch_assoc()) {
            $catalog_message .= "• *{$product['nom']}*\n";
            $catalog_message .= "  Prix: " . formatPrice($product['prix_vente']) . "\n";
            if ($product['stock'] <= $product['stock_minimum']) {
                $catalog_message .= "  ⚠️ Stock limité\n";
            }
            $catalog_message .= "  Lien: " . SITE_URL . "/product.php?id={$product['id']}\n\n";
        }
    } else {
        $catalog_message .= "Aucun produit disponible pour le moment.\n\n";
    }
}

$catalog_message .= "━━━━━━━━━━━━━━━━━━━━\n";
$catalog_message .= "💬 Pour commander, visitez notre site ou contactez-nous directement !\n";
$catalog_message .= "🌐 Site web: " . SITE_URL . "\n";
$catalog_message .= "📱 WhatsApp: " . WHATSAPP_NUMBER . "\n";

$conn->close();

// Si accès via GET, rediriger vers WhatsApp
if (isset($_GET['send'])) {
    $whatsapp_url = "https://wa.me/" . WHATSAPP_NUMBER . "?text=" . urlencode($catalog_message);
    header('Location: ' . $whatsapp_url);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue WhatsApp - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 4rem auto;">
        <div class="card">
            <h1>Catalogue WhatsApp</h1>
            <p>Cliquez sur le bouton ci-dessous pour recevoir notre catalogue complet via WhatsApp :</p>
            
            <a href="?send=1" class="whatsapp-order-btn" style="display: block; text-align: center; margin: 2rem 0;">
                <i class="fab fa-whatsapp"></i> Recevoir le catalogue via WhatsApp
            </a>
            
            <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 5px; margin-top: 2rem;">
                <h3>Aperçu du catalogue :</h3>
                <pre style="white-space: pre-wrap; font-family: Arial, sans-serif; font-size: 0.9rem; line-height: 1.6;"><?php echo htmlspecialchars($catalog_message); ?></pre>
            </div>
            
            <div style="margin-top: 2rem; text-align: center;">
                <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-secondary">Retour à l'accueil</a>
            </div>
        </div>
    </div>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>

