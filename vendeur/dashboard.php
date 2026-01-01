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

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card-modern">
        <div class="stat-icon">
            <i class="fas fa-box"></i>
        </div>
        <h3>Produits en Stock</h3>
        <div class="stat-value"><?php echo $stats['products_in_stock']; ?></div>
        <div class="stat-change">
            <i class="fas fa-info-circle" style="color: #3498db;"></i> Total: <?php echo $stats['total_products']; ?> produits | 
            <i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i> <?php echo $stats['low_stock']; ?> en stock faible
        </div>
    </div>
    
    <div class="stat-card-modern">
        <div class="stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h3>Ventes réalisées</h3>
        <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
        <div class="stat-change">Total commandes</div>
    </div>
</div>

<!-- Content Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Recent Orders -->
    <div class="content-card">
        <div class="content-card-header">
            <h2><i class="fas fa-shopping-cart"></i> Commandes récentes</h2>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>N° Commande</th>
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
                                <td><strong><?php echo htmlspecialchars($order['numero_commande']); ?></strong></td>
                                <td><?php echo htmlspecialchars($order['nom'] . ' ' . $order['prenom']); ?></td>
                                <td><strong><?php echo formatPrice($order['total']); ?></strong></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo $order['statut'] == 'livree' ? 'success' : 
                                            ($order['statut'] == 'en_attente' ? 'warning' : '');
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                Aucune commande récente
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Low Stock Products -->
    <div class="content-card">
        <div class="content-card-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Stock faible</h2>
            <a href="products.php" style="color: #27ae60; text-decoration: none; font-size: 0.9rem;">Voir tout →</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($low_stock_products->num_rows > 0): ?>
                        <?php while ($product = $low_stock_products->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($product['nom']); ?></strong></td>
                                <td>
                                    <span style="color: #e74c3c; font-weight: bold;">
                                        <?php echo $product['stock']; ?> / <?php echo $product['stock_minimum']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-warning" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                Tous les stocks sont suffisants
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="content-card">
    <div class="content-card-header">
        <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="create_sale.php" class="btn btn-primary" style="text-align: center; padding: 1rem;">
            <i class="fas fa-cash-register"></i><br>Effectuer une vente
        </a>
        <a href="product_add.php" class="btn btn-success" style="text-align: center; padding: 1rem;">
            <i class="fas fa-plus"></i><br>Ajouter un produit
        </a>
        <a href="products.php" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
            <i class="fas fa-box"></i><br>Tous les produits
        </a>
        <a href="reports.php" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
            <i class="fas fa-chart-bar"></i><br>Voir les rapports
        </a>
        <a href="cash.php" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
            <i class="fas fa-wallet"></i><br>Gérer la caisse
        </a>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/vendeur_footer.php';
?>
