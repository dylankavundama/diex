<?php
/**
 * API REST - Dashboard Statistics
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/config.php';
require_once '../config/database.php';

// Vérifier l'authentification
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit();
}

$role = $_GET['role'] ?? $_SESSION['user_role'];
$conn = getDBConnection();
$stats = [];

if ($role === 'admin') {
    // Statistiques admin
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COALESCE(SUM(stock), 0) as total FROM products WHERE statut = 'actif'");
    $stats['total_stock'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COALESCE(SUM(stock * prix_vente), 0) as total FROM products WHERE statut = 'actif'");
    $stats['total_stock_value'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock <= stock_minimum AND statut = 'actif'");
    $stats['low_stock'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE statut = 'en_attente'");
    $stats['pending_orders'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'client'");
    $stats['total_clients'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'vendeur'");
    $stats['total_vendeurs'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT SUM(total) as total FROM orders WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND statut != 'annulee'");
    $stats['monthly_revenue'] = $result->fetch_assoc()['total'] ?? 0;
    
    $result = $conn->query("SELECT 
        (SELECT COALESCE(SUM(montant), 0) FROM payments WHERE type_paiement = 'entree' AND statut = 'valide') as total_income,
        (SELECT COALESCE(SUM(montant), 0) FROM payments WHERE type_paiement = 'sortie' AND statut = 'valide') as total_expenses");
    $cash_data = $result->fetch_assoc();
    $stats['cash_balance'] = ($cash_data['total_income'] ?? 0) - ($cash_data['total_expenses'] ?? 0);
    
} else if ($role === 'vendeur') {
    // Statistiques vendeur
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    $stats['total_products'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock > 0 AND statut = 'actif'");
    $stats['products_in_stock'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM products WHERE stock <= stock_minimum AND statut = 'actif'");
    $stats['low_stock'] = $result->fetch_assoc()['total'];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE statut != 'annulee'");
    $stats['total_orders'] = $result->fetch_assoc()['total'];
}

echo json_encode([
    'success' => true,
    'data' => $stats
]);

$conn->close();
?>

