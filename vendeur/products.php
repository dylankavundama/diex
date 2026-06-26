<?php
$page_title = "Tous les Produits";
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
requireRole(ROLE_VENDEUR);

$conn = getDBConnection();
$vendeur_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Traitement des actions
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Vérifier que le produit appartient au vendeur
    $check = $conn->query("SELECT id FROM products WHERE id = $id AND vendeur_id = $vendeur_id");
    if ($check->num_rows > 0) {
        // Vérifier s'il y a des commandes liées
        $check_orders = $conn->query("SELECT id FROM order_items WHERE product_id = $id LIMIT 1");
        
        if ($check_orders->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE products SET statut = 'inactif' WHERE id = ? AND vendeur_id = ?");
            $stmt->bind_param("ii", $id, $vendeur_id);
            if ($stmt->execute()) {
                $message = 'Produit archivé avec succès.';
                $message_type = 'success';
            }
        } else {
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND vendeur_id = ?");
            $stmt->bind_param("ii", $id, $vendeur_id);
            if ($stmt->execute()) {
                $message = 'Produit supprimé avec succès.';
                $message_type = 'success';
            }
        }
        if (isset($stmt)) $stmt->close();
    }
}

// Récupérer TOUS les produits actifs (admin et vendeur ont accès à tous)
$products = $conn->query("SELECT p.*, c.nom as categorie_nom, u.nom as vendeur_nom 
                          FROM products p 
                          LEFT JOIN categories c ON p.categorie_id = c.id 
                          LEFT JOIN users u ON p.vendeur_id = u.id 
                          WHERE p.statut != 'inactif'
                          ORDER BY p.created_at DESC");

require_once 'includes/vendeur_header.php';
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
        <h2><i class="fas fa-box"></i> Tous les Produits</h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-modern">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Prix de vente</th>
                        <th>Stock</th>
                        <th>Vendeur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products->num_rows > 0): ?>
                        <?php while ($product = $products->fetch_assoc()): ?>
                        <tr>
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
                            <td data-label="Prix de vente"><?php echo formatPrice($product['prix_vente']); ?></td>
                            <td data-label="Stock">
                                <span style="color: <?php echo $product['stock'] <= $product['stock_minimum'] ? 'var(--accent-color)' : 'var(--success-color)'; ?>; font-weight: bold;">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td data-label="Vendeur"><?php echo $product['vendeur_nom'] ? htmlspecialchars($product['vendeur_nom']) : 'Admin'; ?></td>
                            <td data-label="Actions">
                                <?php if ($product['vendeur_id'] == $vendeur_id): ?>
                                    <a href="product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                    <a href="?action=delete&id=<?php echo $product['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</a>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Lecture seule</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                Aucun produit disponible pour le moment.
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
require_once 'includes/vendeur_footer.php';
?>

