<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <a href="<?php echo SITE_URL; ?>/index.php">
                    <i class="fas fa-shopping-bag"></i> <?php echo SITE_NAME; ?>
                </a>
            </div>
            <div class="nav-menu" id="navMenu">
                <a href="<?php echo SITE_URL; ?>/index.php">Accueil</a>
                <a href="<?php echo SITE_URL; ?>/shop.php">Boutique</a>
                <a href="<?php echo SITE_URL; ?>/whatsapp_catalog.php">Catalogue WhatsApp</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle">Catégories <i class="fas fa-chevron-down"></i></a>
                    <div class="dropdown-menu">
                        <a href="<?php echo SITE_URL; ?>/shop.php?categorie=1">Vêtements</a>
                        <a href="<?php echo SITE_URL; ?>/shop.php?categorie=2">Articles Ménagers</a>
                        <a href="<?php echo SITE_URL; ?>/shop.php?categorie=3">Décoration</a>
                    </div>
                </div>
                <?php if (isLoggedIn()): ?>
                    <?php if (hasRole(ROLE_ADMIN)): ?>
                        <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">Admin</a>
                    <?php elseif (hasRole(ROLE_VENDEUR)): ?>
                        <a href="<?php echo SITE_URL; ?>/vendeur/dashboard.php">Vendeur</a>
                    <?php endif; ?>
                    <a href="<?php echo SITE_URL; ?>/profile.php">Mon Compte</a>
                    <a href="<?php echo SITE_URL; ?>/auth/logout.php">Déconnexion</a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/auth/login.php">Connexion</a>
                    <a href="<?php echo SITE_URL; ?>/auth/register.php">Inscription</a>
                <?php endif; ?>
            </div>
            <div class="nav-toggle" id="navToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

