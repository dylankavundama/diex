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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: #f5f7fa;
        }
        
        .admin-sidebar {
            width: 260px;
            background: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .admin-sidebar-header {
            padding: 1.5rem;
            background: #1a252f;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: white;
        }
        
        .admin-sidebar-header p {
            margin: 0.5rem 0 0 0;
            color: #bdc3c7;
            font-size: 0.9rem;
        }
        
        .admin-menu {
            padding: 1rem 0;
        }
        
        .admin-menu-item {
            display: block;
            padding: 1rem 1.5rem;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .admin-menu-item:hover,
        .admin-menu-item.active {
            background: #34495e;
            border-left-color: #3498db;
            color: white;
        }
        
        .admin-menu-item i {
            width: 20px;
            margin-right: 0.75rem;
        }
        
        .admin-content {
            flex: 1;
            margin-left: 260px;
            padding: 0;
        }
        
        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .admin-header h1 {
            margin: 0;
            font-size: 1.75rem;
            color: #2c3e50;
        }
        
        .admin-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .admin-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .admin-main-content {
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card-modern {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border-left: 4px solid #3498db;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card-modern.success {
            border-left-color: #27ae60;
        }
        
        .stat-card-modern.warning {
            border-left-color: #f39c12;
        }
        
        .stat-card-modern.danger {
            border-left-color: #e74c3c;
        }
        
        .stat-card-modern .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            background: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }
        
        .stat-card-modern.success .stat-icon {
            background: rgba(39, 174, 96, 0.1);
            color: #27ae60;
        }
        
        .stat-card-modern.warning .stat-icon {
            background: rgba(243, 156, 18, 0.1);
            color: #f39c12;
        }
        
        .stat-card-modern.danger .stat-icon {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
        
        .stat-card-modern h3 {
            margin: 0 0 0.5rem 0;
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .stat-card-modern .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }
        
        .stat-card-modern .stat-change {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #95a5a6;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .content-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ecf0f1;
        }
        
        .content-card-header h2 {
            margin: 0;
            font-size: 1.25rem;
            color: #2c3e50;
        }
        
        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table-modern thead {
            background: #f8f9fa;
        }
        
        .table-modern th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        
        .table-modern td {
            padding: 1rem;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .table-modern tbody tr:hover {
            background: #f8f9fa;
        }
        
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: #2c3e50;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.active {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <h2><i class="fas fa-shopping-bag"></i> <?php echo SITE_NAME; ?></h2>
                <p>Administration</p>
            </div>
            
            <nav class="admin-menu">
                <a href="dashboard.php" class="admin-menu-item active">
                    <i class="fas fa-home"></i> Tableau de bord
                </a>
                <a href="products.php" class="admin-menu-item">
                    <i class="fas fa-box"></i> Produits
                </a>
                <a href="orders.php" class="admin-menu-item">
                    <i class="fas fa-shopping-cart"></i> Commandes
                </a>
                <a href="users.php" class="admin-menu-item">
                    <i class="fas fa-users"></i> Utilisateurs
                </a>
                <a href="financial.php" class="admin-menu-item">
                    <i class="fas fa-dollar-sign"></i> Finances
                </a>
                <a href="reports.php" class="admin-menu-item">
                    <i class="fas fa-chart-bar"></i> Rapports
                </a>
                <a href="<?php echo SITE_URL; ?>/index.php" class="admin-menu-item">
                    <i class="fas fa-store"></i> Voir le site
                </a>
                <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="admin-menu-item">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <div class="admin-content">
            <!-- Header -->
            <header class="admin-header">
                <div>
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1>Tableau de bord</h1>
                </div>
                <div class="admin-user-info">
                    <div style="text-align: right; margin-right: 1rem;">
                        <div style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($_SESSION['user_nom'] . ' ' . $_SESSION['user_prenom']); ?></div>
                        <div style="font-size: 0.85rem; color: #7f8c8d;">Administrateur</div>
                    </div>
                    <div class="admin-user-avatar">
                        <?php echo strtoupper(substr($_SESSION['user_nom'], 0, 1)); ?>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="admin-main-content">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card-modern">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <h3>Produits</h3>
                        <div class="stat-value"><?php echo $stats['total_products']; ?></div>
                        <div class="stat-change">
                            <i class="fas fa-cubes" style="color: #3498db;"></i> Total stock: <?php echo number_format($stats['total_stock'], 0, ',', ' '); ?> unités | 
                            <i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i> <?php echo $stats['low_stock']; ?> en stock faible
                        </div>
                    </div>
                    
                    <div class="stat-card-modern success">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h3>Chiffre d'affaires</h3>
                        <div class="stat-value"><?php echo formatPrice($stats['monthly_revenue']); ?></div>
                        <div class="stat-change">Ce mois</div>
                    </div>
                    
                    <div class="stat-card-modern success">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Bénéfice</h3>
                        <div class="stat-value"><?php echo formatPrice($stats['monthly_profit']); ?></div>
                        <div class="stat-change">Ce mois</div>
                    </div>
                    
                    <div class="stat-card-modern">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3>Commandes</h3>
                        <div class="stat-value"><?php echo $stats['total_orders']; ?></div>
                        <div class="stat-change">
                            <i class="fas fa-clock" style="color: #f39c12;"></i> <?php echo $stats['pending_orders']; ?> en attente
                        </div>
                    </div>
                    
                    <div class="stat-card-modern">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Clients</h3>
                        <div class="stat-value"><?php echo $stats['total_clients']; ?></div>
                        <div class="stat-change">Total clients</div>
                    </div>
                    
                    <div class="stat-card-modern warning">
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <h3>Dettes</h3>
                        <div class="stat-value"><?php echo formatPrice($stats['total_debts']); ?></div>
                        <div class="stat-change">À recouvrer</div>
                    </div>
                    
                    <div class="stat-card-modern">
                        <div class="stat-icon">
                            <i class="fas fa-cubes"></i>
                        </div>
                        <h3>Total Stock</h3>
                        <div class="stat-value"><?php echo formatPrice($stats['total_stock_value']); ?></div>
                        <div class="stat-change">Valeur du stock</div>
                    </div>
                    
                    <div class="stat-card-modern <?php echo $stats['cash_balance'] >= 0 ? 'success' : 'danger'; ?>">
                        <div class="stat-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h3>Caisse</h3>
                        <div class="stat-value"><?php echo formatPrice($stats['cash_balance']); ?></div>
                        <div class="stat-change">Solde disponible</div>
                    </div>
                </div>
                
                <!-- Content Grid -->
                <div class="content-grid">
                    <!-- Recent Orders -->
                    <div class="content-card">
                        <div class="content-card-header">
                            <h2><i class="fas fa-shopping-cart"></i> Commandes récentes</h2>
                            <a href="orders.php" style="color: #3498db; text-decoration: none; font-size: 0.9rem;">Voir tout →</a>
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
                                        <th>Action</th>
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
                                                <td>
                                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 2rem; color: #7f8c8d;">
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
                            <a href="products.php" style="color: #3498db; text-decoration: none; font-size: 0.9rem;">Voir tout →</a>
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
                        <a href="product_add.php" class="btn btn-primary" style="text-align: center; padding: 1rem;">
                            <i class="fas fa-plus"></i><br>Ajouter un produit
                        </a>
                        <a href="create_order.php" class="btn btn-success" style="text-align: center; padding: 1rem;">
                            <i class="fas fa-shopping-cart"></i><br>Créer une commande
                        </a>
                        <a href="users.php" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                            <i class="fas fa-user-plus"></i><br>Ajouter un utilisateur
                        </a>
                        <a href="reports.php" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                            <i class="fas fa-file-alt"></i><br>Générer un rapport
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        function toggleSidebar() {
            document.getElementById('adminSidebar').classList.toggle('active');
        }
        
        // Fermer la sidebar en cliquant à l'extérieur sur mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('adminSidebar');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target) && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
