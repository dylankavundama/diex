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
require_once 'auth_check.php';

// Vérifier l'authentification
verifyApiAuth();

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
} else if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $client_id = (int)($input['client_id'] ?? 0);
    $items = $input['items'] ?? [];
    $total = (float)($input['total'] ?? 0);
    $adresse = sanitize($input['adresse_livraison'] ?? '');
    $telephone = sanitize($input['telephone_livraison'] ?? '');
    
    if (!$client_id || empty($items)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Client ou articles manquants']);
        exit();
    }
    
    $numero_commande = 'CMD-' . time() . '-' . rand(100, 999);
    
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO orders (client_id, numero_commande, total, adresse_livraison, telephone_livraison, statut) VALUES (?, ?, ?, ?, ?, 'en_attente')");
        $stmt->bind_param("isdss", $client_id, $numero_commande, $total, $adresse, $telephone);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantite, prix_unitaire, prix_total) VALUES (?, ?, ?, ?, ?)");
        foreach ($items as $item) {
            $pid = (int)$item['product_id'];
            $qty = (int)$item['quantite'];
            $price = (float)$item['prix_unitaire'];
            $ptotal = $qty * $price;
            $stmt_item->bind_param("iiidd", $order_id, $pid, $qty, $price, $ptotal);
            $stmt_item->execute();
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Commande créée', 'id' => $order_id, 'numero' => $numero_commande]);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
    }
} else if ($method === 'PUT') {
    $id = (int)($_GET['id'] ?? 0);
    $input = json_decode(file_get_contents('php://input'), true);
    $statut = sanitize($input['statut'] ?? '');
    
    if (!$id || !$statut) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID ou statut manquant']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE orders SET statut = ? WHERE id = ?");
    $stmt->bind_param("si", $statut, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Statut mis à jour']);
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

