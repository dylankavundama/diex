<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="navbar">
        <nav>
            <div class="mobile-toggle" style="display:none;">
                <i class="fas fa-bars" id="navToggle" style="color: black;"></i>
            </div>

            <ul class="menu left" id="navMenu">
                <li><a href="<?php echo SITE_URL; ?>/index.php">Accueil</a></li>
                <li class="dropdown">
                    <a href="#">Catégorie <i class="fas fa-chevron-down" style="font-size: 0.8em;"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo SITE_URL; ?>/shop.php?categorie=1">Vêtements</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php?categorie=2">Maison</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php?categorie=3">Accessoires</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php">Voir tout</a></li>
                    </ul>
                </li>
            </ul>

            <div class="logo">
                <a href="<?php echo SITE_URL; ?>/index.php" style="text-decoration:none; color:black;">
                    BOUTIQUE DIEU AGIT
                    <div style="font-size: 0.5em; font-weight: 400; letter-spacing: 2px; margin-top: 2px; opacity: 0.7;">Chez Jodesie</div>
                </a>
            </div>

            <ul class="menu right" id="navMenuRight">
                <li>
                    <a href="<?php echo $current_page === 'index.php' ? '#instantSearch' : SITE_URL . '/shop.php'; ?>" 
                       onclick="<?php echo $current_page === 'index.php' ? 'focusSearch(event)' : ''; ?>" 
                       title="Rechercher" style="color: black;">
                        <i class="fas fa-search"></i>
                    </a>
                </li>
                <li>
                    <a href="<?php echo WHATSAPP_API_URL; ?>?phone=<?php echo WHATSAPP_NUMBER; ?>" title="Contactez-nous sur WhatsApp" style="color: black;"><i class="fas fa-phone"></i></a>
                </li>
                <li>
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/profile.php" title="Mon Compte" style="color: black;"><i class="fas fa-user"></i></a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth/login.php" title="Connexion" style="color: black;"><i class="far fa-user"></i></a>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    </header>

    <script>
    function focusSearch(e) {
        const searchInput = document.getElementById('instantSearch');
        if (searchInput) {
            e.preventDefault();
            searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => searchInput.focus(), 500);
        }
    }
    </script>

