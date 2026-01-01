<?php
$page_title = "Boutique";
require_once 'includes/header.php';
require_once 'config/database.php';

$conn = getDBConnection();

// Filtres
$categorie_id = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;

// Construction de la requête
$query = "SELECT p.*, c.nom as categorie_nom, u.nom as vendeur_nom 
          FROM products p 
          LEFT JOIN categories c ON p.categorie_id = c.id 
          LEFT JOIN users u ON p.vendeur_id = u.id 
          WHERE p.statut = 'actif' AND p.stock > 0";

$params = [];
$types = "";

if ($categorie_id > 0) {
    $query .= " AND (p.categorie_id = ? OR c.parent_id = ?)";
    $params[] = $categorie_id;
    $params[] = $categorie_id;
    $types .= "ii";
}

if (!empty($search)) {
    $query .= " AND (p.nom LIKE ? OR p.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if ($min_price > 0) {
    $query .= " AND p.prix_vente >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price > 0) {
    $query .= " AND p.prix_vente <= ?";
    $params[] = $max_price;
    $types .= "d";
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products_result = $stmt->get_result();

// Récupérer les catégories pour le filtre
$categories_query = "SELECT * FROM categories ORDER BY nom";
$categories_result = $conn->query($categories_query);
?>

<section class="shop-section">
    <div class="container">
        <h1 class="section-title">Notre Boutique</h1>
        
        <div class="shop-filters">
            <form method="GET" action="" class="filter-form">
                <div class="filter-group">
                    <label>Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un produit..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="filter-group">
                    <label>Catégorie</label>
                    <select name="categorie" class="form-control">
                        <option value="">Toutes les catégories</option>
                        <?php while ($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $categorie_id == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nom']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Prix minimum</label>
                    <input type="number" name="min_price" class="form-control" placeholder="0" value="<?php echo $min_price; ?>" min="0">
                </div>
                
                <div class="filter-group">
                    <label>Prix maximum</label>
                    <input type="number" name="max_price" class="form-control" placeholder="100000" value="<?php echo $max_price; ?>" min="0">
                </div>
                
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="shop.php" class="btn btn-secondary">Réinitialiser</a>
            </form>
        </div>
        
        <div class="products-grid">
            <?php if ($products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
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
                                <p class="product-price"><?php echo formatPrice($product['prix_vente']); ?></p>
                                <?php if ($product['vendeur_nom']): ?>
                                    <p class="product-seller"><small>Vendeur: <?php echo htmlspecialchars($product['vendeur_nom']); ?></small></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center" style="grid-column: 1 / -1; padding: 3rem;">
                    <p>Aucun produit trouvé avec ces critères.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.shop-filters {
    background: var(--white);
    padding: 2rem;
    border-radius: 10px;
    box-shadow: var(--shadow);
    margin-bottom: 3rem;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    align-items: end;
}

.filter-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--primary-color);
}
</style>

<?php
$stmt->close();
$conn->close();
require_once 'includes/footer.php';
?>

