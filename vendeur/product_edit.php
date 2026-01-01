<?php
$page_title = "Modifier un Produit";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_VENDEUR);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = (int)$_GET['id'];
$vendeur_id = $_SESSION['user_id'];
$conn = getDBConnection();
$message = '';
$message_type = '';

// Vérifier que le produit appartient au vendeur
$product_query = $conn->prepare("SELECT * FROM products WHERE id = ? AND vendeur_id = ?");
$product_query->bind_param("ii", $product_id, $vendeur_id);
$product_query->execute();
$product_result = $product_query->get_result();

if ($product_result->num_rows === 0) {
    header('Location: products.php');
    exit();
}

$product = $product_result->fetch_assoc();

// Récupérer les catégories
$categories = $conn->query("SELECT * FROM categories ORDER BY nom");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $prix_achat = (float)$_POST['prix_achat'] ?? 0;
    $prix_vente = (float)$_POST['prix_vente'] ?? 0;
    $stock = (int)$_POST['stock'] ?? 0;
    $stock_minimum = (int)$_POST['stock_minimum'] ?? 5;
    $categorie_id = (int)$_POST['categorie_id'] ?? 0;
    
    if (empty($nom) || $prix_achat <= 0 || $prix_vente <= 0 || $categorie_id <= 0) {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $message_type = 'danger';
    } else {
        $image_name = $product['image_principale'];
        
        // Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/products/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_ext, $allowed_ext)) {
                if ($image_name && file_exists($upload_dir . $image_name)) {
                    unlink($upload_dir . $image_name);
                }
                
                $image_name = uniqid() . '.' . $file_ext;
                move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
            }
        }
        
        $stmt = $conn->prepare("UPDATE products SET nom = ?, description = ?, prix_achat = ?, prix_vente = ?, stock = ?, stock_minimum = ?, categorie_id = ?, image_principale = ? WHERE id = ? AND vendeur_id = ?");
        $stmt->bind_param("ssddiiissii", $nom, $description, $prix_achat, $prix_vente, $stock, $stock_minimum, $categorie_id, $image_name, $product_id, $vendeur_id);
        
        if ($stmt->execute()) {
            $message = 'Produit mis à jour avec succès.';
            $message_type = 'success';
            header('Location: products.php');
            exit();
        } else {
            $message = 'Erreur lors de la mise à jour.';
            $message_type = 'danger';
        }
        
        $stmt->close();
    }
}

require_once 'includes/vendeur_header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 1.5rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="content-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nom">Nom du produit *</label>
                <input type="text" id="nom" name="nom" class="form-control" required value="<?php echo htmlspecialchars($product['nom']); ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="prix_achat">Prix d'achat (USD) *</label>
                    <input type="number" id="prix_achat" name="prix_achat" class="form-control" step="0.01" min="0" required value="<?php echo $product['prix_achat']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="prix_vente">Prix de vente (USD) *</label>
                    <input type="number" id="prix_vente" name="prix_vente" class="form-control" step="0.01" min="0" required value="<?php echo $product['prix_vente']; ?>">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" class="form-control" min="0" required value="<?php echo $product['stock']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="stock_minimum">Stock minimum</label>
                    <input type="number" id="stock_minimum" name="stock_minimum" class="form-control" min="0" value="<?php echo $product['stock_minimum']; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="categorie_id">Catégorie *</label>
                <select id="categorie_id" name="categorie_id" class="form-control" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $product['categorie_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['nom']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Image actuelle</label>
                <?php if ($product['image_principale']): ?>
                    <div style="margin-bottom: 1rem;">
                        <img src="<?php echo UPLOAD_URL . $product['image_principale']; ?>" style="max-width: 200px; border-radius: 5px;">
                    </div>
                <?php endif; ?>
                <label for="image">Nouvelle image (laisser vide pour conserver l'actuelle)</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="products.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<?php
$product_query->close();
$conn->close();
require_once 'includes/vendeur_footer.php';
?>

