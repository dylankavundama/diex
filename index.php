<?php
$page_title = "Accueil";
require_once 'includes/header.php';
require_once 'config/database.php';

$conn = getDBConnection();

// Récupérer le terme de recherche si présent
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Récupérer tous les produits actifs de manière aléatoire ou filtrés par recherche
$featured_query = "SELECT p.*, c.nom as categorie_nom, u.nom as vendeur_nom 
                   FROM products p 
                   LEFT JOIN categories c ON p.categorie_id = c.id 
                   LEFT JOIN users u ON p.vendeur_id = u.id 
                   WHERE p.statut = 'actif' AND p.stock > 0";

if (!empty($search)) {
    $featured_query .= " AND (p.nom LIKE '%$search%' OR p.description LIKE '%$search%' OR c.nom LIKE '%$search%')";
}

$featured_query .= " ORDER BY RAND()";
$featured_result = $conn->query($featured_query);


?>

<style>
    .search-container {
        margin: 2rem 0;
        display: flex;
        justify-content: center;
        animation: fadeIn 0.8s ease;
    }

    .search-form {
        display: flex;
        width: 100%;
        max-width: 600px;
        position: relative;
    }

    .search-input {
        flex: 1;
        padding: 1rem 100px 1rem 3rem;
        border: 2px solid #eee;
        border-radius: 50px;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .search-input:focus {
        outline: none;
        border-color: #764ba2;
        box-shadow: 0 8px 25px rgba(118, 75, 162, 0.15);
    }

    .search-icon {
        position: absolute;
        left: 1.2rem;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
        font-size: 1.1rem;
    }

    .btn-search {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-search:hover {
        transform: translateY(-50%) scale(1.05);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<section class="hero">
    <div class="hero-content">
        <h1>STYLE SANS EFFORT,<br>LA BEAUTÉ DURABLE.</h1>
        <p>Découvrez notre dernière collection de mode minimal moderne pour tout le monde.</p>
        <a href="shop.php"><button>Shop Now →</button></a>
    </div>
</section>

<section class="featured-products">
    <div class="container">
        <div class="search-container">
            <form action="" method="GET" class="search-form" id="searchFilterForm">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" id="instantSearch" class="search-input" placeholder="Rechercher un produit, une catégorie..." value="" autocomplete="off">
                <button type="button" id="clearSearch" class="clear-search" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
                <button type="submit" class="btn-search">Rechercher</button>
            </form>
        </div>
        
        <h2 class="section-title" id="sectionTitle"><?php echo !empty($search) ? 'Résultats pour "' . htmlspecialchars($search) . '"' : 'Tous nos produits'; ?></h2>
        
        <div id="noResults" style="display: none; text-align: center; padding: 3rem;">
            <p style="font-size: 1.2rem; color: #666;">Aucun produit ne correspond à votre recherche.</p>
        </div>

        <?php if ($featured_result->num_rows > 0): ?>
            <div class="products-grid" id="productsGrid">
                <?php while ($product = $featured_result->fetch_assoc()): ?>
                    <div class="product-card" data-name="<?php echo htmlspecialchars(strtolower($product['nom'])); ?>" data-category="<?php echo htmlspecialchars(strtolower($product['categorie_nom'])); ?>">
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
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 3rem;">
                <p style="font-size: 1.2rem; color: #666;">Aucun produit disponible pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .clear-search {
        position: absolute;
        right: 120px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #95a5a6;
        cursor: pointer;
        padding: 5px;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        transition: color 0.3s;
        z-index: 10;
    }

    .clear-search:hover {
        color: #e74c3c;
    }

    @media (max-width: 600px) {
        .clear-search {
            right: 110px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('instantSearch');
    const clearBtn = document.getElementById('clearSearch');
    const productCards = document.querySelectorAll('.product-card');
    const noResults = document.getElementById('noResults');
    const sectionTitle = document.getElementById('sectionTitle');

    // Clean URL on load if search is present
    if (window.location.search.includes('search=')) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;

            // Toggle clear button
            if (query.length > 0) {
                clearBtn.style.display = 'flex';
            } else {
                clearBtn.style.display = 'none';
            }

            productCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const category = card.getAttribute('data-category');

                if (name.includes(query) || category.includes(query)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update title and no products message
            if (query === '') {
                sectionTitle.textContent = 'Tous nos produits';
            } else {
                sectionTitle.textContent = 'Résultats pour "' + this.value + '"';
            }

            if (visibleCount === 0 && productCards.length > 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        });

        // Clear button functionality
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
                
                // If the page was refreshed with a search param, we might want to clean the URL
                if (window.location.search.includes('search=')) {
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            });
        }
    }
});
</script>

<?php
$conn->close();
require_once 'includes/footer.php';
?>
