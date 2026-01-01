<?php
/**
 * API REST - Authentification
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/config.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

$login = sanitize($input['login'] ?? '');
$password = $input['password'] ?? '';

if (empty($login) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs']);
    exit();
}

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id, nom, prenom, email, telephone, password, role, statut FROM users WHERE email = ? OR nom = ?");
$stmt->bind_param("ss", $login, $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    if ($user['statut'] === 'inactif') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Compte désactivé']);
        exit();
    }
    
    if (password_verify($password, $user['password'])) {
        // Démarrer la session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        // Retourner les données utilisateur (sans le mot de passe)
        unset($user['password']);
        
        // Générer un token simple (vous pouvez utiliser JWT pour plus de sécurité)
        $token = bin2hex(random_bytes(32));
        $_SESSION['api_token'] = $token;
        
        echo json_encode([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
    }
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Identifiants invalides']);
}

$stmt->close();
$conn->close();
?>

