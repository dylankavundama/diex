<?php
/**
 * API REST - Financial Reports
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
require_once 'auth_check.php';

verifyApiAuth();

$conn = getDBConnection();
$period = $_GET['period'] ?? 'monthly';

$stats = [];

if ($period === 'daily') {
    $query = "SELECT DATE(created_at) as label, SUM(total) as value 
              FROM orders 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
              AND statut != 'annulee'
              GROUP BY DATE(created_at) 
              ORDER BY label ASC";
} else {
    $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as label, SUM(total) as value 
              FROM orders 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) 
              AND statut != 'annulee'
              GROUP BY label 
              ORDER BY label ASC";
}

$result = $conn->query($query);
$history = [];
while ($row = $result->fetch_assoc()) {
    $history[] = $row;
}

// Recent sales for context
$recent_query = "SELECT o.numero_commande, o.total, o.created_at, u.nom, u.prenom 
                 FROM orders o 
                 JOIN users u ON o.client_id = u.id 
                 ORDER BY o.created_at DESC LIMIT 5";
$recent_res = $conn->query($recent_query);
$recent = [];
while ($row = $recent_res->fetch_assoc()) {
    $recent[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => [
        'history' => $history,
        'recent_sales' => $recent,
        'total_revenue' => array_sum(array_column($history, 'value'))
    ]
]);

$conn->close();
