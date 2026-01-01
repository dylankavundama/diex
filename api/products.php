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

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit();
}

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
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non implémentée']);
}

$conn->close();
?>

