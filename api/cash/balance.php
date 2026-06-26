<?php
/**
 * API REST - Cash Balance
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../auth_check.php';

verifyApiAuth();

$conn = getDBConnection();

$result = $conn->query("SELECT 
    (SELECT COALESCE(SUM(montant), 0) FROM payments WHERE type_paiement = 'entree' AND statut = 'valide') as total_income,
    (SELECT COALESCE(SUM(montant), 0) FROM payments WHERE type_paiement = 'sortie' AND statut = 'valide') as total_expenses");

$data = $result->fetch_assoc();
$balance = ($data['total_income'] ?? 0) - ($data['total_expenses'] ?? 0);

echo json_encode([
    'success' => true, 
    'data' => [
        'balance' => (float)$balance,
        'income' => (float)$data['total_income'],
        'expenses' => (float)$data['total_expenses']
    ]
]);

$conn->close();
