<?php
/**
 * Helper to check API authentication
 */
session_start();

function verifyApiAuth() {
    // 1. Check Session
    if (isset($_SESSION['user_id'])) {
        return true;
    }

    // 2. Check Bearer Token (for Mobile App)
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, role, nom, prenom FROM users WHERE api_token = ? AND statut = 'actif'");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $stmt->close();
            return true;
        }
        $stmt->close();
    }

    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit();
}
