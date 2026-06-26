<?php
/**
 * API REST - Cash Movements
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

$query = "SELECT p.*, u.nom as client_nom, u.prenom as client_prenom 
          FROM payments p 
          JOIN users u ON p.client_id = u.id 
          ORDER BY p.created_at DESC LIMIT 50";

$result = $conn->query($query);
$movements = [];
while ($row = $result->fetch_assoc()) {
    $movements[] = $row;
}

echo json_encode(['success' => true, 'data' => $movements]);

$conn->close();
