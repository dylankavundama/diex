<?php
$page_title = "Mes Ventes";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_VENDEUR);

$conn = getDBConnection();
$message = '';
$message_type = '';

// Filtres
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$query = "SELECT DISTINCT o.*, u.nom, u.prenom, u.email, u.telephone 
          FROM orders o 
          JOIN order_items oi ON o.id = oi.order_id 
          JOIN products p ON oi.product_id = p.id 
          JOIN users u ON o.client_id = u.id 
          WHERE 1=1";
          
if (!empty($status_filter)) {
    $query .= " AND o.statut = '$status_filter'";
}

$query .= " ORDER BY o.created_at DESC";

$orders = $conn->query($query);

require_once 'includes/vendeur_header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 1.5rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="content-card" style="margin-bottom: 1.5rem;">
    <div class="content-card-header">
        <h2><i class="fas fa-filter"></i> Filtres</h2>
    </div>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="?status=" class="btn btn-<?php echo empty($status_filter) ? 'primary' : 'secondary'; ?>">Toutes</a>
        <a href="?status=en_attente" class="btn btn-<?php echo $status_filter === 'en_attente' ? 'primary' : 'secondary'; ?>">En attente</a>
        <a href="?status=confirmee" class="btn btn-<?php echo $status_filter === 'confirmee' ? 'primary' : 'secondary'; ?>">Confirmées</a>
        <a href="?status=livree" class="btn btn-<?php echo $status_filter === 'livree' ? 'primary' : 'secondary'; ?>">Livrées</a>
        <a href="?status=annulee" class="btn btn-<?php echo $status_filter === 'annulee' ? 'primary' : 'secondary'; ?>">Annulées</a>
    </div>
</div>

<div class="content-card">
    <div class="content-card-header">
        <h2><i class="fas fa-shopping-cart"></i> Mes Ventes</h2>
        <a href="create_sale.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Nouvelle vente
        </a>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Client</th>
                    <th>Contact</th>
                    <th>Total</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders->num_rows > 0): ?>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($order['numero_commande']); ?></strong></td>
                            <td><?php echo htmlspecialchars($order['nom'] . ' ' . $order['prenom']); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($order['email']); ?></div>
                                <small><?php echo htmlspecialchars($order['telephone']); ?></small>
                            </td>
                            <td><strong><?php echo formatPrice($order['total']); ?></strong></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $order['statut'] == 'livree' ? 'success' : 
                                        ($order['statut'] == 'en_attente' ? 'warning' : '');
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="invoice.php?id=<?php echo $order['id']; ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                    <i class="fas fa-file-invoice"></i> Facture
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                            Aucune vente enregistrée
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/vendeur_footer.php';
?>

