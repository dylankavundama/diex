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
            <form method="GET" action="" class="filter-form" id="shopFilterForm">
                <div class="filter-group">
                    <label>Recherche</label>
                    <input type="text" name="search" id="shopSearch" class="form-control" placeholder="Rechercher un produit..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="filter-group">
                    <label>Catégorie</label>
                    <select name="categorie" id="shopCategory" class="form-control">
                        <option value="">Toutes les catégories</option>
                        <?php 
                        // Reset categories result pointer
                        $categories_result->data_seek(0);
                        while ($cat = $categories_result->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $categorie_id == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['nom']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Prix minimum</label>
                    <input type="number" name="min_price" id="minPrice" class="form-control" placeholder="0" value="<?php echo $min_price > 0 ? $min_price : ''; ?>" min="0">
                </div>
                
                <div class="filter-group">
                    <label>Prix maximum</label>
                    <input type="number" name="max_price" id="maxPrice" class="form-control" placeholder="Max" value="<?php echo $max_price > 0 ? $max_price : ''; ?>" min="0">
                </div>
                
                <a href="shop.php" class="btn btn-secondary">Réinitialiser</a>
            </form>
        </div>
        
        <div id="noResults" style="display: none; text-align: center; padding: 5rem; grid-column: 1 / -1; width: 100%;">
            <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p style="font-size: 1.2rem; color: #666;">Aucun produit ne correspond à vos critères.</p>
        </div>

        <div class="products-grid" id="shopProductsGrid">
            <?php if ($products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="product-card" 
                         data-name="<?php echo htmlspecialchars(strtolower($product['nom'])); ?>" 
                         data-category="<?php echo $product['categorie_id']; ?>" 
                         data-price="<?php echo $product['prix_vente']; ?>">
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
                    <p>Aucun produit disponible pour le moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shopSearch = document.getElementById('shopSearch');
    const shopCategory = document.getElementById('shopCategory');
    const minPriceInput = document.getElementById('minPrice');
    const maxPriceInput = document.getElementById('maxPrice');
    const productCards = document.querySelectorAll('.product-card');
    const noResults = document.getElementById('noResults');
    const productsGrid = document.getElementById('shopProductsGrid');

    function filterProducts() {
        const searchQuery = shopSearch.value.toLowerCase().trim();
        const selectedCat = shopCategory.value;
        const minPrice = parseFloat(minPriceInput.value) || 0;
        const maxPrice = parseFloat(maxPriceInput.value) || Infinity;

        let visibleCount = 0;

        productCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const catId = card.getAttribute('data-category');
            const price = parseFloat(card.getAttribute('data-price'));

            const matchesSearch = name.includes(searchQuery);
            const matchesCat = selectedCat === '' || catId === selectedCat;
            const matchesPrice = price >= minPrice && price <= maxPrice;

            if (matchesSearch && matchesCat && matchesPrice) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0 && productCards.length > 0) {
            noResults.style.display = 'block';
            productsGrid.style.display = 'none';
        } else {
            noResults.style.display = 'none';
            productsGrid.style.display = 'grid';
        }
    }

    [shopSearch, shopCategory, minPriceInput, maxPriceInput].forEach(el => {
        if (el) {
            el.addEventListener('input', filterProducts);
            if (el.tagName === 'SELECT') {
                el.addEventListener('change', filterProducts);
            }
        }
    });

    // Initial check (in case filters were set via URL)
    filterProducts();
});
</script>

<style>
.shop-filters {
    background: white;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    margin-bottom: 3rem;
    border: 1px solid #eee;
}

.filter-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    align-items: end;
}

.filter-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-control {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1.5px solid #eee;
    border-radius: 8px;
    font-family: inherit;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}
</style>

<?php
$stmt->close();
$conn->close();
require_once 'includes/footer.php';
?>

