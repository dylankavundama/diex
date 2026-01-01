<?php
/**
 * Header commun pour toutes les pages admin
 * Inclut la sidebar et le header
 */
if (!isset($page_title)) {
    $page_title = "Administration";
}

// Déterminer la page active pour la sidebar
$current_page = basename($_SERVER['PHP_SELF']);
$active_pages = [
    'dashboard.php' => 'dashboard.php',
    'products.php' => 'products.php',
    'product_add.php' => 'products.php',
    'product_edit.php' => 'products.php',
    'orders.php' => 'orders.php',
    'order_details.php' => 'orders.php',
    'create_order.php' => 'orders.php',
    'users.php' => 'users.php',
    'financial.php' => 'financial.php',
    'reports.php' => 'reports.php'
];
$active_page = $active_pages[$current_page] ?? '';
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
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
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
            margin-right: 1rem;
        }
        
        .page-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
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
            
            .admin-main-content {
                padding: 1rem;
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
                <a href="dashboard.php" class="admin-menu-item <?php echo $active_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Tableau de bord
                </a>
                <a href="products.php" class="admin-menu-item <?php echo $active_page === 'products.php' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Produits
                </a>
                <a href="orders.php" class="admin-menu-item <?php echo $active_page === 'orders.php' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Commandes
                </a>
                <a href="users.php" class="admin-menu-item <?php echo $active_page === 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Utilisateurs
                </a>
                <a href="financial.php" class="admin-menu-item <?php echo $active_page === 'financial.php' ? 'active' : ''; ?>">
                    <i class="fas fa-dollar-sign"></i> Finances
                </a>
                <a href="reports.php" class="admin-menu-item <?php echo $active_page === 'reports.php' ? 'active' : ''; ?>">
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
                <div style="display: flex; align-items: center;">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1><?php echo $page_title; ?></h1>
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

