<?php
$page_title = "Facture";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_VENDEUR);

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
$items_query = "SELECT oi.*, p.nom as product_nom 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

require_once 'includes/vendeur_header.php';
?>

<div class="page-actions">
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print"></i> Imprimer
    </button>
    <a href="orders.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="content-card" style="max-width: 800px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 2px solid #ddd;">
        <h1 style="color: #27ae60; margin: 0;"><?php echo SITE_NAME; ?></h1>
        <p style="margin: 0.5rem 0;">Facture</p>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h3 style="margin-top: 0;">Informations Client</h3>
            <p><strong>Nom:</strong> <?php echo htmlspecialchars($order['nom'] . ' ' . $order['prenom']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
            <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($order['telephone']); ?></p>
            <?php if ($order['adresse_livraison']): ?>
                <p><strong>Adresse:</strong> <?php echo nl2br(htmlspecialchars($order['adresse_livraison'])); ?></p>
            <?php endif; ?>
        </div>
        
        <div>
            <h3 style="margin-top: 0;">Informations Facture</h3>
            <p><strong>N° Commande:</strong> <?php echo htmlspecialchars($order['numero_commande']); ?></p>
            <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
            <p><strong>Mode de paiement:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['mode_paiement'])); ?></p>
            <p><strong>Statut:</strong> 
                <span class="badge badge-<?php 
                    echo $order['statut'] == 'livree' ? 'success' : 
                        ($order['statut'] == 'en_attente' ? 'warning' : '');
                ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                </span>
            </p>
        </div>
    </div>
    
    <div style="margin-bottom: 2rem;">
        <h3>Articles</h3>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Réinitialiser le pointeur pour recalculer
                $items_result->data_seek(0);
                $total_benefice = 0;
                $sous_total = 0;
                while ($item = $items_result->fetch_assoc()): 
                    $total_benefice += $item['benefice'];
                    $sous_total += $item['prix_total'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_nom']); ?></td>
                        <td><?php echo $item['quantite']; ?></td>
                        <td><?php echo formatPrice($item['prix_unitaire']); ?></td>
                        <td><?php echo formatPrice($item['prix_total']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">Sous-total:</td>
                    <td><?php echo formatPrice($sous_total); ?></td>
                </tr>
                <?php 
                // Extraire la réduction des notes si elle existe
                $reduction = 0;
                if (preg_match('/Réduction appliquée:\s*\$?([\d,]+\.?\d*)/', $order['notes'], $matches)) {
                    $reduction = (float)str_replace(',', '', $matches[1]);
                }
                if ($reduction > 0): 
                ?>
                <tr style="color: #27ae60;">
                    <td colspan="3" style="text-align: right;">Réduction:</td>
                    <td style="color: #27ae60;">-<?php echo formatPrice($reduction); ?></td>
                </tr>
                <?php endif; ?>
                <tr style="font-weight: bold; background: #f8f9fa;">
                    <td colspan="3" style="text-align: right;">Total:</td>
                    <td style="font-size: 1.2rem; color: #27ae60;"><?php echo formatPrice($order['total']); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <?php 
    // Afficher les notes sans la ligne de réduction (déjà affichée dans le tableau)
    $notes_display = $order['notes'];
    if (preg_match('/Réduction appliquée:.*$/m', $notes_display)) {
        $notes_display = preg_replace('/\n?\n?Réduction appliquée:.*$/m', '', $notes_display);
        $notes_display = trim($notes_display);
    }
    if ($notes_display): 
    ?>
        <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
            <strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($notes_display)); ?>
        </div>
    <?php endif; ?>
    
    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #ddd; text-align: center; color: #7f8c8d;">
        <p>Merci pour votre achat !</p>
    </div>
</div>

<style>
@media print {
    .page-actions, .admin-sidebar, .admin-header {
        display: none !important;
    }
    .admin-content {
        margin-left: 0 !important;
    }
    .content-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>

<?php
$stmt->close();
$items_stmt->close();
$conn->close();
require_once 'includes/vendeur_footer.php';
?>

