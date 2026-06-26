<?php
$page_title = "Gestion des Produits";
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
requireRole(ROLE_ADMIN);

$conn = getDBConnection();
$message = '';
$message_type = '';

// Traitement des actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        
        // Vérifier s'il y a des commandes liées à ce produit
        $check_orders = $conn->query("SELECT id FROM order_items WHERE product_id = $id LIMIT 1");
        
        if ($check_orders->num_rows > 0) {
            // S'il y a des commandes, on fait un soft-delete
            $stmt = $conn->prepare("UPDATE products SET statut = 'inactif' WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'Le produit a été archivé car il possède des commandes liées.';
                $message_type = 'success';
            }
        } else {
            // S'il n'y a pas de commandes, on peut faire un vrai delete
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = 'Produit supprimé définitivement avec succès.';
                $message_type = 'success';
            }
        }
        if (isset($stmt)) $stmt->close();
    }
}

// Récupérer tous les produits (sauf les inactifs si on veut les cacher)
$products = $conn->query("SELECT p.*, c.nom as categorie_nom, u.nom as vendeur_nom 
                          FROM products p 
                          LEFT JOIN categories c ON p.categorie_id = c.id 
                          LEFT JOIN users u ON p.vendeur_id = u.id 
                          WHERE p.statut != 'inactif'
                          ORDER BY p.created_at DESC");

require_once 'includes/admin_header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 1.5rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="page-actions">
    <a href="product_add.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Ajouter un produit
    </a>
</div>

<div class="content-card">
    <div class="content-card-header">
        <h2><i class="fas fa-box"></i> Liste des Produits</h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix d'achat</th>
                        <th>Prix de vente</th>
                        <th>Stock</th>
                        <th>Vendeur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products->num_rows > 0): ?>
                        <?php while ($product = $products->fetch_assoc()): ?>
                        <tr>
                            <td data-label="ID"><?php echo $product['id']; ?></td>
                            <td data-label="Image">
                                <?php if ($product['image_principale']): ?>
                                    <img src="<?php echo UPLOAD_URL . $product['image_principale']; ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <span style="color: #999;">Pas d'image</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Nom"><?php echo htmlspecialchars($product['nom']); ?></td>
                            <td data-label="Catégorie"><?php echo htmlspecialchars($product['categorie_nom']); ?></td>
                            <td data-label="Prix d'achat"><?php echo formatPrice($product['prix_achat']); ?></td>
                            <td data-label="Prix de vente"><?php echo formatPrice($product['prix_vente']); ?></td>
                            <td data-label="Stock">
                                <span style="color: <?php echo $product['stock'] <= $product['stock_minimum'] ? 'var(--accent-color)' : 'var(--success-color)'; ?>; font-weight: bold;">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td data-label="Vendeur"><?php echo $product['vendeur_nom'] ? htmlspecialchars($product['vendeur_nom']) : 'Admin'; ?></td>
                            <td data-label="Statut">
                                <span class="badge badge-<?php 
                                    echo $product['statut'] == 'actif' ? 'success' : 
                                        ($product['statut'] == 'rupture' ? 'warning' : '');
                                ?>">
                                    <?php echo ucfirst($product['statut']); ?>
                                </span>
                            </td>
                            <td data-label="Actions">
                                <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="?action=delete&id=<?php echo $product['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                Aucun produit enregistré pour le moment.
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

