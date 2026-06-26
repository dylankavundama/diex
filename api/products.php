<?php
/**
 * API REST - Products
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/config.php';
require_once '../config/database.php';
require_once 'auth_check.php';

// Vérifier l'authentification
verifyApiAuth();

$conn = getDBConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        // Récupérer un produit spécifique
        $stmt = $conn->prepare("SELECT p.*, c.nom as categorie_nom, u.nom as vendeur_nom 
                                FROM products p 
                                LEFT JOIN categories c ON p.categorie_id = c.id 
                                LEFT JOIN users u ON p.vendeur_id = u.id 
                                WHERE p.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $product]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Produit non trouvé']);
        }
    } else {
        // Récupérer tous les produits
        $result = $conn->query("SELECT p.*, c.nom as categorie_nom, u.nom as vendeur_nom 
                               FROM products p 
                               LEFT JOIN categories c ON p.categorie_id = c.id 
                               LEFT JOIN users u ON p.vendeur_id = u.id 
                               ORDER BY p.created_at DESC");
        
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $products]);
    }
} else if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $nom = sanitize($input['nom'] ?? '');
    $description = sanitize($input['description'] ?? '');
    $prix_achat = (float)($input['prix_achat'] ?? 0);
    $prix_vente = (float)($input['prix_vente'] ?? 0);
    $stock = (int)($input['stock'] ?? 0);
    $categorie_id = (int)($input['categorie_id'] ?? 1);
    $vendeur_id = $input['vendeur_id'] ?? $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO products (nom, description, prix_achat, prix_vente, stock, categorie_id, vendeur_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddiii", $nom, $description, $prix_achat, $prix_vente, $stock, $categorie_id, $vendeur_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Produit créé', 'id' => $conn->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $conn->error]);
    }
    $stmt->close();
} else if ($method === 'PUT') {
    $id = (int)($_GET['id'] ?? 0);
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
        exit();
    }
    
    $update_fields = [];
    $params = [];
    $types = "";
    
    $fields = ['nom', 'description', 'prix_achat', 'prix_vente', 'stock', 'statut'];
    foreach ($fields as $field) {
        if (isset($input[$field])) {
            $update_fields[] = "$field = ?";
            $params[] = $input[$field];
            $types .= is_numeric($input[$field]) ? (is_float($input[$field]) ? "d" : "i") : "s";
        }
    }
    
    if (empty($update_fields)) {
        echo json_encode(['success' => true, 'message' => 'Rien à mettre à jour']);
        exit();
    }
    
    $sql = "UPDATE products SET " . implode(", ", $update_fields) . " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Produit mis à jour']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $conn->error]);
    }
    $stmt->close();
} else if ($method === 'DELETE') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
        exit();
    }
    
    // Instead of deleting, we set to 'inactif'
    $stmt = $conn->prepare("UPDATE products SET statut = 'inactif' WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Produit supprimé (désactivé)']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $conn->error]);
    }
    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}

$conn->close();
?>

