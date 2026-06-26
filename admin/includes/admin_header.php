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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <!-- Sidebar Header -->
            <div class="admin-sidebar-header">
                <div class="sidebar-brand">
                    <div class="brand-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="brand-text">
                        <h2><?php echo SITE_NAME; ?></h2>
                        <span>Panel Admin</span>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="admin-menu">
                <!-- Dashboard -->
                <a href="dashboard.php" class="menu-item <?php echo $active_page === 'dashboard.php' ? 'active' : ''; ?>">
                    <div class="menu-item-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="menu-item-text">Dashboard</span>
                </a>
                
                <!-- Gestion Group -->
                <div class="menu-group">
                    <div class="menu-group-title">
                        <i class="fas fa-layer-group"></i>
                        <span>Gestion</span>
                    </div>
                    
                    <a href="products.php" class="menu-item <?php echo $active_page === 'products.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <span class="menu-item-text">Produits</span>
                    </a>
                    
                    <a href="orders.php" class="menu-item <?php echo $active_page === 'orders.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <span class="menu-item-text">Commandes</span>
                        <?php 
                        // Afficher le nombre de commandes en attente si > 0
                        $conn = getDBConnection();
                        $result = $conn->query("SELECT COUNT(*) as count FROM orders WHERE statut = 'en_attente'");
                        $pending = $result->fetch_assoc()['count'];
                        if ($pending > 0): 
                        ?>
                            <span class="menu-badge"><?php echo $pending; ?></span>
                        <?php endif; ?>
                    </a>
                    
                    <a href="users.php" class="menu-item <?php echo $active_page === 'users.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="menu-item-text">Utilisateurs</span>
                    </a>
                </div>
                
                <!-- Finances Group -->
                <div class="menu-group">
                    <div class="menu-group-title">
                        <i class="fas fa-wallet"></i>
                        <span>Finances</span>
                    </div>
                    
                    <a href="financial.php" class="menu-item <?php echo $active_page === 'financial.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <span class="menu-item-text">Trésorerie</span>
                    </a>
                    
                    <a href="reports.php" class="menu-item <?php echo $active_page === 'reports.php' ? 'active' : ''; ?>">
                        <div class="menu-item-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <span class="menu-item-text">Rapports</span>
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
                /* Modern Sidebar Styles */
                .admin-sidebar {
                    background: linear-gradient(180deg, #1a1f3a 0%, #2d3561 100%);
                    display: flex;
                    flex-direction: column;
                }
                
                /* Sidebar Header */
                .admin-sidebar-header {
                    padding: 1.5rem 1.2rem;
                    background: rgba(0, 0, 0, 0.2);
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
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    color: white;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
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
                    color: rgba(255, 255, 255, 0.6);
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
                    color: rgba(255, 255, 255, 0.4);
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
                    color: rgba(255, 255, 255, 0.7);
                    text-decoration: none;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    position: relative;
                    margin: 0.2rem 0.8rem;
                    border-radius: 10px;
                    width: auto;
                }
                
                .menu-item:hover {
                    background: rgba(255, 255, 255, 0.08);
                    color: white;
                    transform: translateX(4px);
                }
                
                .menu-item.active {
                    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
                    color: white;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
                }
                
                .menu-item.active::before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 3px;
                    height: 60%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-radius: 0 3px 3px 0;
                }
                
                .menu-item-icon {
                    width: 36px;
                    height: 36px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1rem;
                    background: rgba(255, 255, 255, 0.05);
                    border-radius: 8px;
                    transition: all 0.3s;
                    flex-shrink: 0;
                }
                
                .menu-item:hover .menu-item-icon {
                    background: rgba(255, 255, 255, 0.1);
                    transform: scale(1.1);
                }
                
                .menu-item.active .menu-item-icon {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
                }
                
                .menu-item-text {
                    margin-left: 0.8rem;
                    font-size: 0.9rem;
                    font-weight: 500;
                    flex: 1;
                    white-space: nowrap;
                }
                
                /* Badge for notifications */
                .menu-badge {
                    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                    color: white;
                    font-size: 0.7rem;
                    font-weight: 700;
                    padding: 0.2rem 0.5rem;
                    border-radius: 10px;
                    min-width: 20px;
                    text-align: center;
                    flex-shrink: 0;
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
                    color: #ff6b6b !important;
                }
                
                .menu-logout:hover {
                    background: rgba(255, 107, 107, 0.1) !important;
                }
                
                .menu-logout .menu-item-icon {
                    background: rgba(255, 107, 107, 0.1);
                }
                
                .menu-logout:hover .menu-item-icon {
                    background: rgba(255, 107, 107, 0.2);
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
                        <div style="font-size: 0.85rem; color: #7f8c8d;">Administrateur</div>
                    </div>
                    <div class="admin-user-avatar">
                        <?php echo strtoupper(substr($_SESSION['user_nom'], 0, 1)); ?>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="admin-main-content">

