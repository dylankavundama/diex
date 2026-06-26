<?php
/**
 * Header commun pour toutes les pages vendeur
 * Inclut la sidebar et le header
 */
if (!isset($page_title)) {
    $page_title = "Espace Vendeur";
}

// Déterminer la page active pour la sidebar
$current_page = basename($_SERVER['PHP_SELF']);
$active_pages = [
    'dashboard.php' => 'dashboard.php',
    'products.php' => 'products.php',
    'product_add.php' => 'products.php',
    'product_edit.php' => 'products.php',
    'create_sale.php' => 'create_sale.php',
    'orders.php' => 'orders.php',
    'reports.php' => 'reports.php',
    'cash.php' => 'cash.php'
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
            background: #27ae60;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .admin-sidebar-header {
            padding: 1.5rem;
            background: #1e8449;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: white;
        }
        
        .admin-sidebar-header p {
            margin: 0.5rem 0 0 0;
            color: #d5f4e6;
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
            background: #229954;
            border-left-color: #f39c12;
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
            background: #27ae60;
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
            border-left: 4px solid #27ae60;
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
            background: rgba(39, 174, 96, 0.1);
            color: #27ae60;
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
            <!-- Sidebar Header -->
            <div class="admin-sidebar-header">
                <div class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="brand-text">
                        <h2><?php echo SITE_NAME; ?></h2>
                        <span>Vendeur</span>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="admin-menu">
                <!-- Main Nav -->
                <a href="dashboard.php" class="menu-item <?php echo $active_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <div class="menu-item-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="menu-item-text">Tableau de bord</span>
                </a>
                
                <!-- Gestion Group -->
                <div class="menu-group">
                    <div class="menu-group-title">
                        <i class="fas fa-layer-group"></i>
                        <span>Ventes & Stocks</span>
                    </div>
                    
                    <a href="create_sale.php" class="menu-item <?php echo $active_page === 'create_sale.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <span class="menu-item-text">Effectuer une vente</span>
                    </a>

                    <a href="products.php" class="menu-item <?php echo $active_page === 'products.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <span class="menu-item-text">Mes Produits</span>
                    </a>
                    
                    <a href="orders.php" class="menu-item <?php echo $active_page === 'orders.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <span class="menu-item-text">Mes Ventes</span>
                    </a>
                </div>
                
                <!-- Performance Group -->
                <div class="menu-group">
                    <div class="menu-group-title">
                        <i class="fas fa-wallet"></i>
                        <span>Performance</span>
                    </div>
                    
                    <a href="reports.php" class="menu-item <?php echo $active_page === 'reports.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <span class="menu-item-text">Rapports</span>
                    </a>
                    
                    <a href="cash.php" class="menu-item <?php echo $active_page === 'cash.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <span class="menu-item-text">Ma Caisse</span>
                    </a>
                </div>
                
                <!-- Bottom Actions -->
                <div class="menu-bottom">
                    <a href="<?php echo SITE_URL; ?>/index.php" class="menu-item">
                        <div class="menu-item-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        <span class="menu-item-text">Voir le site</span>
                    </a>
                    
                    <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="menu-item menu-logout">
                        <div class="menu-item-icon">
                            <i class="fas fa-power-off"></i>
                        </div>
                        <span class="menu-item-text">Déconnexion</span>
                    </a>
                </div>
            </nav>
            
            <style>
                /* Modern Sidebar Styles - Vendeur Edition (Green Theme) */
                .admin-sidebar {
                    background: linear-gradient(180deg, #11998e 0%, #38ef7d 100%);
                    display: flex;
                    flex-direction: column;
                }
                
                /* Sidebar Header */
                .admin-sidebar-header {
                    padding: 1.5rem 1.2rem;
                    background: rgba(0, 0, 0, 0.1);
                    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                }
                
                .sidebar-brand {
                    display: flex;
                    align-items: center;
                    gap: 0.8rem;
                }
                
                .brand-icon {
                    width: 40px;
                    height: 40px;
                    background: white;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    color: #11998e;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                    flex-shrink: 0;
                }
                
                .brand-text {
                    display: flex;
                    flex-direction: column;
                }
                
                .brand-text h2 {
                    margin: 0;
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: white;
                    line-height: 1.2;
                }
                
                .brand-text span {
                    font-size: 0.7rem;
                    color: rgba(255, 255, 255, 0.8);
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                
                /* Menu */
                .admin-menu {
                    padding: 1rem 0;
                    overflow-y: auto;
                    height: calc(100vh - 100px);
                    display: flex;
                    flex-direction: column;
                }
                
                /* Menu Groups */
                .menu-group {
                    margin: 0.5rem 0;
                    display: flex;
                    flex-direction: column;
                }
                
                .menu-group-title {
                    padding: 0.8rem 1.2rem;
                    font-size: 0.7rem;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    color: rgba(255, 255, 255, 0.6);
                    display: flex;
                    align-items: center;
                    gap: 0.6rem;
                    margin-top: 0.5rem;
                }
                
                .menu-group-title i {
                    font-size: 0.85rem;
                }
                
                /* Menu Items */
                .menu-item {
                    display: flex;
                    align-items: center;
                    padding: 0.75rem 1.2rem;
                    color: rgba(255, 255, 255, 0.9);
                    text-decoration: none;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    position: relative;
                    margin: 0.2rem 0.8rem;
                    border-radius: 10px;
                    width: auto;
                }
                
                .menu-item:hover {
                    background: rgba(255, 255, 255, 0.15);
                    color: white;
                    transform: translateX(4px);
                }
                
                .menu-item.active {
                    background: white;
                    color: #11998e;
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                }
                
                .menu-item.active::before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 3px;
                    height: 60%;
                    background: #f39c12;
                    border-radius: 0 3px 3px 0;
                }
                
                .menu-item-icon {
                    width: 36px;
                    height: 36px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1rem;
                    background: rgba(255, 255, 255, 0.1);
                    border-radius: 8px;
                    transition: all 0.3s;
                    flex-shrink: 0;
                }
                
                .menu-item:hover .menu-item-icon {
                    background: rgba(255, 255, 255, 0.2);
                    transform: scale(1.1);
                }
                
                .menu-item.active .menu-item-icon {
                    background: #11998e;
                    color: white;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                }
                
                .menu-item-text {
                    margin-left: 0.8rem;
                    font-size: 0.9rem;
                    font-weight: 500;
                    flex: 1;
                    white-space: nowrap;
                }
                
                /* Bottom Menu */
                .menu-bottom {
                    margin-top: auto;
                    padding-top: 1rem;
                    border-top: 1px solid rgba(255, 255, 255, 0.1);
                    display: flex;
                    flex-direction: column;
                }
                
                /* Logout */
                .menu-logout {
                    color: #ffcccc !important;
                }
                
                .menu-logout:hover {
                    background: rgba(255, 255, 255, 0.1) !important;
                    color: white !important;
                }
                
                .menu-logout .menu-item-icon {
                    background: rgba(255, 255, 255, 0.1);
                }
            </style>
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
                        <div style="font-size: 0.85rem; color: #7f8c8d;">Vendeur</div>
                    </div>
                    <div class="admin-user-avatar">
                        <?php echo strtoupper(substr($_SESSION['user_nom'], 0, 1)); ?>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="admin-main-content">

