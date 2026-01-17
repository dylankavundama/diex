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


?>

<section class="hero">
    <!-- Slider Background Layer -->
    <!-- Static Background Image (Handled via CSS .hero) -->

    <!-- Static Overlay Content (Direct Children of .hero for CSS compatibility) -->
    <div class="hero-content">
        <h1>EFFORTLESS STYLE,<br>TIMELESS ELEGANCE.</h1>
        <p>Discover our latest collection of modern minimal fashion.</p>
        <a href="shop.php"><button>Shop Now →</button></a>
    </div>

    <div class="product-cards">
        <div class="card">
            <img src="<?php echo SITE_URL; ?>/assets/images/header/a.avif" alt="Denim Jacket">
            <div class="card-info">
                <span>Denim Jacket</span>
                <span>$54</span>
            </div>
        </div>

        <div class="card">
            <img src="<?php echo SITE_URL; ?>/assets/images/header/a.jpg" alt="Jacket">
            <div class="card-info">
                <span>Jacket</span>
                <span>$84</span>
            </div>
        </div>
    </div>
</section>



<section class="featured-products">
    <div class="container">
        <h2 class="section-title">Tous nos produits</h2>
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
