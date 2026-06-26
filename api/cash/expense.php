<?php
/**
 * API REST - Record Expense
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../auth_check.php';

verifyApiAuth();

$input = json_decode(file_get_contents('php://input'), true);
$conn = getDBConnection();

$montant = (float)($input['montant'] ?? 0);
$description = sanitize($input['description'] ?? '');
$mode = sanitize($input['mode_paiement'] ?? 'espece');
$client_id = $_SESSION['user_id']; // L'admin ou le vendeur qui enregistre la dépense

if ($montant <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Le montant doit être supérieur à zéro']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO payments (client_id, montant, type_paiement, mode_paiement, description, statut) VALUES (?, ?, 'sortie', ?, ?, 'valide')");
$stmt->bind_param("idss", $client_id, $montant, $mode, $description);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Dépense enregistrée avec succès']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement : ' . $conn->error]);
}

$stmt->close();
$conn->close();
