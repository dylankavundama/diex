<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="navbar">
        <nav>
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
            
                <li><a href="tel:+22507000000">Appeler Nous</a></li>
            </ul>

            <div class="logo">
                <a href="<?php echo SITE_URL; ?>/index.php" style="text-decoration:none; color:black;">
                   Dieu Agit
                </a>
            </div>

            <ul class="menu right">
                <li>
                    <a href="<?php echo SITE_URL; ?>/shop.php" title="Rechercher" style="color: black;"><i class="fas fa-search"></i></a>
                </li>
                <li>
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo SITE_URL; ?>/profile.php" title="Mon Compte" style="color: black;"><i class="fas fa-user"></i></a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/auth/login.php" title="Connexion" style="color: black;"><i class="far fa-user"></i></a>
                    <?php endif; ?>
                </li>
         
                <li class="mobile-toggle" style="display:none;">
                    <i class="fas fa-bars" id="navToggle" style="color: black;"></i>
                </li>
            </ul>
        </nav>
    </header>

