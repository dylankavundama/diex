<?php
$page_title = "Accueil";
require_once 'includes/header.php';
require_once 'config/database.php';

$conn = getDBConnection();

// Récupérer tous les produits actifs
$featured_query = "SELECT p.*, c.nom as categorie_nom, u.nom as vendeur_nom 
                   FROM products p 
                   LEFT JOIN categories c ON p.categorie_id = c.id 
                   LEFT JOIN users u ON p.vendeur_id = u.id 
                   WHERE p.statut = 'actif' AND p.stock > 0 
                   ORDER BY p.created_at DESC";
$featured_result = $conn->query($featured_query);

// Récupérer les catégories principales
$categories_query = "SELECT * FROM categories WHERE parent_id IS NULL";
$categories_result = $conn->query($categories_query);
?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Bienvenue sur <?php echo SITE_NAME; ?></h1>
            <p>Découvrez notre large sélection de vêtements, articles ménagers et décoration intérieure</p>
            <a href="shop.php" class="btn btn-primary">Découvrir la boutique</a>
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="container">
        <h2 class="section-title">Nos Catégories</h2>
        <div class="categories-grid">
            <?php while ($categorie = $categories_result->fetch_assoc()): ?>
                <div class="category-card">
                    <a href="shop.php?categorie=<?php echo $categorie['id']; ?>">
                        <div class="category-icon">
                            <i class="fas fa-<?php 
                                echo $categorie['id'] == 1 ? 'tshirt' : ($categorie['id'] == 2 ? 'home' : 'couch'); 
                            ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($categorie['nom']); ?></h3>
                        <p><?php echo htmlspecialchars($categorie['description']); ?></p>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="featured-products">
    <div class="container">
        <h2 class="section-title">Tous nos Produits</h2>
        <?php if ($featured_result->num_rows > 0): ?>
            <div class="products-grid">
                <?php while ($product = $featured_result->fetch_assoc()): ?>
                    <div class="product-card">
                        <a href="product.php?id=<?php echo $product['id']; ?>">
                            <div class="product-image">
                                <?php if ($product['image_principale']): ?>
                                    <img src="<?php echo UPLOAD_URL . $product['image_principale']; ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>">
                                <?php else: ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" alt="Image non disponible">
                                <?php endif; ?>
                                <?php if ($product['stock'] <= $product['stock_minimum']): ?>
                                    <span class="badge badge-warning">Stock faible</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['nom']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['categorie_nom']); ?></p>
                                <p class="product-price"><?php echo formatPriceDual($product['prix_vente']); ?></p>
                                <?php if ($product['vendeur_nom']): ?>
                                    <p class="product-seller"><small>Vendeur: <?php echo htmlspecialchars($product['vendeur_nom']); ?></small></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 3rem;">
                <p style="font-size: 1.2rem; color: #666;">Aucun produit disponible pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
$conn->close();
require_once 'includes/footer.php';
?>
