<?php
$page_title = "Gestion des Commandes";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_ADMIN);

$conn = getDBConnection();
$message = '';
$message_type = '';

// Traitement des actions
if (isset($_POST['action']) && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action'];
    
    if ($action === 'update_status') {
        $new_status = sanitize($_POST['status']);
        $stmt = $conn->prepare("UPDATE orders SET statut = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        if ($stmt->execute()) {
            $message = 'Statut de la commande mis à jour.';
            $message_type = 'success';
        }
        $stmt->close();
    }
}

// Filtres
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$query = "SELECT o.*, u.nom, u.prenom, u.email, u.telephone 
          FROM orders o 
          JOIN users u ON o.client_id = u.id";
          
if (!empty($status_filter)) {
    $query .= " WHERE o.statut = '$status_filter'";
}

$query .= " ORDER BY o.created_at DESC";

$orders = $conn->query($query);

require_once 'includes/admin_header.php';
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
        <h2><i class="fas fa-shopping-cart"></i> Liste des Commandes</h2>
        <a href="create_order.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Créer une commande
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
                            <td><?php echo htmlspecialchars($order['numero_commande']); ?></td>
                            <td><?php echo htmlspecialchars($order['nom'] . ' ' . $order['prenom']); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($order['email']); ?></div>
                                <small><?php echo htmlspecialchars($order['telephone']); ?></small>
                            </td>
                            <td><?php echo formatPrice($order['total']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="action" value="update_status">
                                    <select name="status" onchange="this.form.submit()" style="padding: 0.5rem; border-radius: 5px;">
                                        <option value="en_attente" <?php echo $order['statut'] === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                                        <option value="confirmee" <?php echo $order['statut'] === 'confirmee' ? 'selected' : ''; ?>>Confirmée</option>
                                        <option value="en_preparation" <?php echo $order['statut'] === 'en_preparation' ? 'selected' : ''; ?>>En préparation</option>
                                        <option value="expediee" <?php echo $order['statut'] === 'expediee' ? 'selected' : ''; ?>>Expédiée</option>
                                        <option value="livree" <?php echo $order['statut'] === 'livree' ? 'selected' : ''; ?>>Livrée</option>
                                        <option value="annulee" <?php echo $order['statut'] === 'annulee' ? 'selected' : ''; ?>>Annulée</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Détails</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                Aucune commande enregistrée pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/admin_footer.php';
?>

