<?php
/**
 * API REST - Orders
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
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
        // Récupérer une commande spécifique
        $stmt = $conn->prepare("SELECT o.*, u.nom as client_nom, u.prenom as client_prenom 
                                FROM orders o 
                                JOIN users u ON o.client_id = u.id 
                                WHERE o.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();
            
            // Récupérer les items
            $items_result = $conn->query("SELECT oi.*, p.nom as product_nom 
                                         FROM order_items oi 
                                         JOIN products p ON oi.product_id = p.id 
                                         WHERE oi.order_id = $id");
            $items = [];
            while ($item = $items_result->fetch_assoc()) {
                $items[] = $item;
            }
            $order['items'] = $items;
            
            echo json_encode(['success' => true, 'data' => $order]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Commande non trouvée']);
        }
    } else {
        // Récupérer toutes les commandes
        $result = $conn->query("SELECT o.*, u.nom as client_nom, u.prenom as client_prenom 
                               FROM orders o 
                               JOIN users u ON o.client_id = u.id 
                               ORDER BY o.created_at DESC");
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $orders]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non implémentée']);
}

$conn->close();
?>

