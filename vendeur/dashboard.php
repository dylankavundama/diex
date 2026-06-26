<?php
$page_title = "Tableau de bord Vendeur";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_VENDEUR);

$conn = getDBConnection();
$vendeur_id = $_SESSION['user_id'];

// Statistiques du vendeur
$stats = [];

// Nombre total de produits (le vendeur a accès à tous les produits)
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$stats['total_products'] = $result->fetch_assoc()['total'];

// Nombre de produits disponibles en stock (stock > 0) - tous les produits
$result = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock > 0 AND statut = 'actif'");
$stats['products_in_stock'] = $result->fetch_assoc()['total'];

// Produits en stock faible - tous les produits
$result = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock <= stock_minimum AND statut = 'actif'");
$stats['low_stock'] = $result->fetch_assoc()['total'];

// Nombre total de ventes réalisées (toutes les commandes, car le vendeur a accès à tous les produits)
$result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE statut != 'annulee'");
$stats['total_orders'] = $result->fetch_assoc()['total'];

// Pas de calcul de chiffre d'affaires ni de bénéfice pour le vendeur

// Commandes récentes (toutes les commandes, car le vendeur a accès à tous les produits)
$recent_orders = $conn->query("SELECT DISTINCT o.*, u.nom, u.prenom 
                                FROM orders o 
                                JOIN users u ON o.client_id = u.id 
                                ORDER BY o.created_at DESC 
                                LIMIT 10");

// Produits en stock faible (tous les produits)
$low_stock_products = $conn->query("SELECT * FROM products WHERE stock <= stock_minimum AND statut = 'actif' ORDER BY stock ASC LIMIT 10");

require_once 'includes/vendeur_header.php';
?>

<?php
// Inclure les styles premium du dashboard (partagés avec l'admin)
require_once '../admin/dashboard_premium_styles.php';
?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card-premium green">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Produits Actifs</h3>
                <div class="stat-value"><?php echo $stats['products_in_stock']; ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-change">
            <span>En stock et actifs</span>
        </div>
    </div>
    
    <div class="stat-card-premium purple">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Ventes Réalisées</h3>
                <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="stat-change">
            <span>Total des commandes</span>
        </div>
    </div>

    <div class="stat-card-premium red">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Stock Faible</h3>
                <div class="stat-value"><?php echo $stats['low_stock']; ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
        <div class="stat-change">
            <span>Nécessite attention</span>
        </div>
    </div>
</div>

<style>
    /* The grid-container is already handled in dashboard_premium_styles.php if we use content-grid-premium */
    /* But since it's a separate class here, let's just make it consistent */
    .grid-container {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 1024px) {
        .grid-container {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
</style>

<!-- Content Grid -->
<div class="grid-container">
    <!-- Recent Orders -->
    <div class="content-card-premium">
        <div class="card-header-premium">
            <h2><i class="fas fa-receipt"></i> Ventes récentes</h2>
            <a href="orders.php" class="view-all">Voir tout <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>Commande</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_orders->num_rows > 0): ?>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                             <tr>
                                <td data-label="Commande"><span class="order-id">#<?php echo htmlspecialchars($order['numero_commande']); ?></span></td>
                                <td data-label="Client"><?php echo htmlspecialchars($order['nom'] . ' ' . $order['prenom']); ?></td>
                                <td data-label="Total"><span class="price-premium"><?php echo formatPrice($order['total']); ?></span></td>
                                <td data-label="Statut">
                                    <span class="badge-premium badge-<?php 
                                        echo $order['statut'] == 'livree' ? 'success' : 
                                            ($order['statut'] == 'en_attente' ? 'warning' : 'info');
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                                    </span>
                                </td>
                                <td data-label="Date"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Aucune vente récente</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Low Stock Products -->
    <div class="content-card-premium">
        <div class="card-header-premium header-warning">
            <h2><i class="fas fa-exclamation-circle"></i> Alertes Stock</h2>
        </div>
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($low_stock_products->num_rows > 0): ?>
                        <?php while ($product = $low_stock_products->fetch_assoc()): ?>
                             <tr>
                                <td data-label="Produit"><strong><?php echo htmlspecialchars($product['nom']); ?></strong></td>
                                <td data-label="Stock">
                                    <span class="text-danger font-weight-bold">
                                        <?php echo $product['stock']; ?> / <?php echo $product['stock_minimum']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="empty-state">
                                <i class="fas fa-check-circle"></i>
                                <p>Tout est en stock</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="content-card-premium">
    <div class="card-header-premium">
        <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
    </div>
    <div class="quick-actions-grid">
        <a href="create_sale.php" class="quick-action-btn primary">
            <div class="action-icon"><i class="fas fa-cash-register"></i></div>
            <span>Effectuer une vente</span>
        </a>
        <a href="product_add.php" class="quick-action-btn success">
            <div class="action-icon"><i class="fas fa-plus-circle"></i></div>
            <span>Ajouter un produit</span>
        </a>
        <a href="products.php" class="quick-action-btn warning">
            <div class="action-icon"><i class="fas fa-boxes"></i></div>
            <span>Mes produits</span>
        </a>
        <a href="reports.php" class="quick-action-btn blue">
            <div class="action-icon"><i class="fas fa-chart-line"></i></div>
            <span>Voir les rapports</span>
        </a>
        <a href="cash.php" class="quick-action-btn orange">
            <div class="action-icon"><i class="fas fa-wallet"></i></div>
            <span>Gérer ma caisse</span>
        </a>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/vendeur_footer.php';
?>
