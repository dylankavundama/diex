<?php
/**
 * Page d'accueil de l'administration
 * Redirige vers le tableau de bord
 */
require_once '../config/config.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/auth/login.php');
    exit();
}

// Vérifier si l'utilisateur est admin
if (!hasRole(ROLE_ADMIN)) {
    // Si c'est un vendeur, rediriger vers son dashboard
    if (hasRole(ROLE_VENDEUR)) {
        header('Location: ' . SITE_URL . '/vendeur/dashboard.php');
        exit();
    }
    // Sinon, rediriger vers l'accueil
    header('Location: ' . SITE_URL . '/index.php');
    exit();
}

// Rediriger vers le dashboard admin
header('Location: ' . SITE_URL . '/admin/dashboard.php');
exit();
?>

