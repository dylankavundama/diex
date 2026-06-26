<?php
$page_title = "Tableau de bord Admin";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_ADMIN);

$conn = getDBConnection();

// Statistiques générales
$stats = [];

// Nombre total de produits
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$stats['total_products'] = $result->fetch_assoc()['total'];

// Somme totale des produits en stock (nombre d'unités)
$result = $conn->query("SELECT COALESCE(SUM(stock), 0) as total FROM products WHERE statut = 'actif'");
$stats['total_stock'] = $result->fetch_assoc()['total'];

// Valeur totale en dollars des produits en stock
$result = $conn->query("SELECT COALESCE(SUM(stock * prix_vente), 0) as total FROM products WHERE statut = 'actif'");
$stats['total_stock_value'] = $result->fetch_assoc()['total'];

// Nombre de produits en stock faible
$result = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock <= stock_minimum AND statut = 'actif'");
$stats['low_stock'] = $result->fetch_assoc()['total'];

// Nombre total de commandes
$result = $conn->query("SELECT COUNT(*) as total FROM orders");
$stats['total_orders'] = $result->fetch_assoc()['total'];

// Commandes en attente
$result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE statut = 'en_attente'");
$stats['pending_orders'] = $result->fetch_assoc()['total'];

// Nombre total de clients
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'client'");
$stats['total_clients'] = $result->fetch_assoc()['total'];

// Nombre de vendeurs
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'vendeur'");
$stats['total_vendeurs'] = $result->fetch_assoc()['total'];

// Chiffre d'affaires du mois
$result = $conn->query("SELECT SUM(total) as total FROM orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND statut != 'annulee'");
$stats['monthly_revenue'] = $result->fetch_assoc()['total'] ?? 0;

// Bénéfice du mois
$result = $conn->query("SELECT SUM(benefice) as total FROM order_items oi 
                        JOIN orders o ON oi.order_id = o.id 
                        WHERE MONTH(o.created_at) = MONTH(CURRENT_DATE()) 
                        AND YEAR(o.created_at) = YEAR(CURRENT_DATE()) 
                        AND o.statut != 'annulee'");
$stats['monthly_profit'] = $result->fetch_assoc()['total'] ?? 0;

// Dettes totales
$result = $conn->query("SELECT SUM(montant_restant) as total FROM client_debts WHERE statut != 'paye'");
$stats['total_debts'] = $result->fetch_assoc()['total'] ?? 0;

// Solde de caisse total (toutes les entrées - toutes les sorties)
$result = $conn->query("SELECT 
    (SELECT COALESCE(SUM(montant), 0) FROM payments WHERE type_paiement = 'entree' AND statut = 'valide') as total_income,
    (SELECT COALESCE(SUM(montant), 0) FROM payments WHERE type_paiement = 'sortie' AND statut = 'valide') as total_expenses");
$cash_data = $result->fetch_assoc();
$stats['cash_balance'] = ($cash_data['total_income'] ?? 0) - ($cash_data['total_expenses'] ?? 0);

// Commandes récentes
$recent_orders = $conn->query("SELECT o.*, u.nom, u.prenom 
                                FROM orders o 
                                JOIN users u ON o.client_id = u.id 
                                ORDER BY o.created_at DESC 
                                LIMIT 10");

// Produits en stock faible
$low_stock_products = $conn->query("SELECT * FROM products WHERE stock <= stock_minimum AND statut = 'actif' ORDER BY stock ASC LIMIT 10");

// Statistiques des ventes des 7 derniers jours
$sales_last_7_days = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count, SUM(total) as revenue 
                                    FROM orders 
                                    WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) 
                                    AND statut != 'annulee'
                                    GROUP BY DATE(created_at) 
                                    ORDER BY date ASC");

require_once 'includes/admin_header.php';
?>

<?php include 'dashboard_premium_styles.php'; ?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card-premium purple">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Produits</h3>
                <div class="stat-value"><?php echo $stats['total_products']; ?></div>
</div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-box"></i>
            </div>
        </div>
        <div class="stat-change">
            <i class="fas fa-cubes"></i> <?php echo number_format($stats['total_stock'], 0, ',', ' '); ?> unités en stock
        </div>
    </div>
    
    <div class="stat-card-premium green">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Chiffre d'affaires</h3>
                <div class="stat-value"><?php echo formatPrice($stats['monthly_revenue']); ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="stat-change">
            <i class="fas fa-calendar-alt"></i> Ce mois
        </div>
    </div>
    
    <div class="stat-card-premium orange">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Bénéfice</h3>
                <div class="stat-value"><?php echo formatPrice($stats['monthly_profit']); ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-change">
            <i class="fas fa-trending-up"></i> Ce mois
        </div>
    </div>
    
    <div class="stat-card-premium blue">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Commandes</h3>
                <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="stat-change">
            <i class="fas fa-clock" style="color: #f39c12;"></i> <?php echo $stats['pending_orders']; ?> en attente
        </div>
    </div>
    
    <div class="stat-card-premium purple">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Clients</h3>
                <div class="stat-value"><?php echo $stats['total_clients']; ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-change">
            <i class="fas fa-user-tie"></i> <?php echo $stats['total_vendeurs']; ?> vendeurs
        </div>
    </div>
    
    <div class="stat-card-premium yellow">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Dettes</h3>
                <div class="stat-value"><?php echo formatPrice($stats['total_debts']); ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
        <div class="stat-change">
            <i class="fas fa-hand-holding-usd"></i> À recouvrer
        </div>
    </div>
    
    <div class="stat-card-premium blue">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Valeur Stock</h3>
                <div class="stat-value"><?php echo formatPrice($stats['total_stock_value']); ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-cubes"></i>
            </div>
        </div>
        <div class="stat-change">
            <i class="fas fa-warehouse"></i> Total inventaire
        </div>
    </div>
    
    <div class="stat-card-premium <?php echo $stats['cash_balance'] >= 0 ? 'green' : 'red'; ?>">
        <div class="stat-header">
            <div class="stat-info">
                <h3>Caisse</h3>
                <div class="stat-value"><?php echo formatPrice($stats['cash_balance']); ?></div>
            </div>
            <div class="stat-icon-wrapper">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
        <div class="stat-change">
            <i class="fas fa-money-bill-wave"></i> Solde disponible
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid-premium">
    <!-- Recent Orders -->
    <div class="content-card-premium">
        <div class="content-header">
            <h2><i class="fas fa-shopping-cart"></i> Commandes récentes</h2>
            <a href="orders.php">Voir tout →</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_orders->num_rows > 0): ?>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td data-label="N° Commande"><strong><?php echo htmlspecialchars($order['numero_commande']); ?></strong></td>
                                <td data-label="Client"><?php echo htmlspecialchars($order['nom'] . ' ' . $order['prenom']); ?></td>
                                <td data-label="Total"><strong><?php echo formatPrice($order['total']); ?></strong></td>
                                <td data-label="Statut">
                                    <span class="badge-premium <?php 
                                        echo $order['statut'] == 'livree' ? 'success' : 
                                            ($order['statut'] == 'en_attente' ? 'warning' : 'info');
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                                    </span>
                                </td>
                                <td data-label="Date"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td data-label="Action">
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-premium primary">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                                Aucune commande récente
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Low Stock Products -->
    <div class="content-card-premium">
        <div class="content-header">
            <h2><i class="fas fa-exclamation-triangle"></i> Stock faible</h2>
            <a href="products.php">Voir tout →</a>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-premium">
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
                                <td data-label="Produit"><strong><?php echo htmlspecialchars($product['nom']); ?></strong></td>
                                <td data-label="Stock">
                                    <span style="color: #e74c3c; font-weight: bold;">
                                        <?php echo $product['stock']; ?> / <?php echo $product['stock_minimum']; ?>
                                    </span>
                                </td>
                                <td data-label="Action">
                                    <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn-premium warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="fas fa-check-circle" style="font-size: 2rem; color: #27ae60; margin-bottom: 0.5rem; display: block;"></i>
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
<div class="content-card-premium">
    <div class="content-header">
        <h2><i class="fas fa-bolt"></i> Actions rapides</h2>
    </div>
    <div class="quick-actions-grid">
        <a href="product_add.php" class="btn-premium primary quick-action-btn">
            <i class="fas fa-plus"></i>
            Ajouter un produit
        </a>
        <a href="create_order.php" class="btn-premium success quick-action-btn">
            <i class="fas fa-shopping-cart"></i>
            Créer une commande
        </a>
        <a href="users.php" class="btn-premium warning quick-action-btn">
            <i class="fas fa-user-plus"></i>
            Ajouter un utilisateur
        </a>
        <a href="reports.php" class="btn-premium primary quick-action-btn">
            <i class="fas fa-file-alt"></i>
            Générer un rapport
        </a>
    </div>
</div>

<?php
require_once 'includes/admin_footer.php';
$conn->close();
?>
