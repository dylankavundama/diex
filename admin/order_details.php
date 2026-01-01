<?php
$page_title = "Détails de la Commande";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_ADMIN);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: orders.php');
    exit();
}

$order_id = (int)$_GET['id'];
$conn = getDBConnection();

// Récupérer les détails de la commande
$query = "SELECT o.*, u.nom, u.prenom, u.email, u.telephone 
          FROM orders o 
          JOIN users u ON o.client_id = u.id 
          WHERE o.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: orders.php');
    exit();
}

$order = $result->fetch_assoc();

// Récupérer les articles de la commande
$items_query = "SELECT oi.*, p.nom as product_nom, p.image_principale 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

require_once 'includes/admin_header.php';
?>

<div class="page-actions">
    <a href="orders.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour aux commandes
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
    <div class="content-card">
        <div class="content-card-header">
            <h2><i class="fas fa-user"></i> Informations Client</h2>
        </div>
            <p><strong>Nom:</strong> <?php echo htmlspecialchars($order['nom'] . ' ' . $order['prenom']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($order['telephone']); ?></p>
            <p><strong>Adresse de livraison:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($order['adresse_livraison'] ?? 'Non spécifiée')); ?></p>
            <p><strong>Téléphone de livraison:</strong> <?php echo htmlspecialchars($order['telephone_livraison'] ?? 'Non spécifié'); ?></p>
        </div>
        
        <div class="content-card">
            <div class="content-card-header">
                <h2><i class="fas fa-shopping-cart"></i> Informations Commande</h2>
            </div>
            <p><strong>Statut:</strong> 
                <span class="badge badge-<?php 
                    echo $order['statut'] == 'livree' ? 'success' : 
                        ($order['statut'] == 'en_attente' ? 'warning' : '');
                ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                </span>
            </p>
            <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            <p><strong>Mode de paiement:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['mode_paiement'])); ?></p>
            <p><strong>Total:</strong> <span style="font-size: 1.5rem; font-weight: bold; color: var(--accent-color);"><?php echo formatPrice($order['total']); ?></span></p>
            <?php if ($order['notes']): ?>
                <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="content-card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h2>Articles de la commande</h2>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                        <th>Bénéfice</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_benefice = 0;
                    while ($item = $items_result->fetch_assoc()): 
                        $total_benefice += $item['benefice'];
                    ?>
                        <tr>
                            <td>
                                <?php if ($item['image_principale']): ?>
                                    <img src="<?php echo UPLOAD_URL . $item['image_principale']; ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['product_nom']); ?></td>
                            <td><?php echo $item['quantite']; ?></td>
                            <td><?php echo formatPrice($item['prix_unitaire']); ?></td>
                            <td><?php echo formatPrice($item['prix_total']); ?></td>
                            <td style="color: var(--success-color); font-weight: bold;"><?php echo formatPrice($item['benefice']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; background: var(--light-color);">
                        <td colspan="5" style="text-align: right;">Total bénéfice:</td>
                        <td style="color: var(--success-color);"><?php echo formatPrice($total_benefice); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php
$stmt->close();
$items_stmt->close();
$conn->close();
require_once 'includes/admin_footer.php';
?>

