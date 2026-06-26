<?php
/**
 * API REST - Clients
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $search = $_GET['search'] ?? '';
    
    $query = "SELECT id, nom, prenom, email, telephone, created_at FROM users WHERE role = 'client'";
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $query .= " AND (nom LIKE '%$search%' OR prenom LIKE '%$search%' OR telephone LIKE '%$search%' OR email LIKE '%$search%')";
    }
    $query .= " ORDER BY nom ASC, prenom ASC";
    
    $result = $conn->query($query);
    $clients = [];
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $clients]);
} else if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nom = sanitize($input['nom'] ?? '');
    $prenom = sanitize($input['prenom'] ?? '');
    $email = sanitize($input['email'] ?? '');
    $telephone = sanitize($input['telephone'] ?? '');
    
    if (empty($nom) || empty($prenom)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Le nom et le prénom sont obligatoires']);
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO users (nom, prenom, email, telephone, role, password) VALUES (?, ?, ?, ?, 'client', 'no_password')");
    $stmt->bind_param("ssss", $nom, $prenom, $email, $telephone);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Client ajouté avec succès', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du client : ' . $conn->error]);
    }
    $stmt->close();
}

$conn->close();
