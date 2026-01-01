<?php
$page_title = "Modifier un Produit";
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/image_functions.php';
requireRole(ROLE_ADMIN);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = (int)$_GET['id'];
$conn = getDBConnection();
$message = '';
$message_type = '';

// Récupérer le produit
$product_query = $conn->prepare("SELECT * FROM products WHERE id = ?");
$product_query->bind_param("i", $product_id);
$product_query->execute();
$product_result = $product_query->get_result();

if ($product_result->num_rows === 0) {
    header('Location: products.php');
    exit();
}

$product = $product_result->fetch_assoc();

// Récupérer les catégories
$categories = $conn->query("SELECT * FROM categories ORDER BY nom");
$vendeurs = $conn->query("SELECT * FROM users WHERE role = 'vendeur' AND statut = 'actif'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $prix_achat = (float)$_POST['prix_achat'] ?? 0;
    $prix_vente = (float)$_POST['prix_vente'] ?? 0;
    $stock = (int)$_POST['stock'] ?? 0;
    $stock_minimum = (int)$_POST['stock_minimum'] ?? 5;
    $categorie_id = (int)$_POST['categorie_id'] ?? 0;
    $vendeur_id = !empty($_POST['vendeur_id']) ? (int)$_POST['vendeur_id'] : NULL;
    $statut = sanitize($_POST['statut'] ?? 'actif');
    
    if (empty($nom) || $prix_achat <= 0 || $prix_vente <= 0 || $categorie_id <= 0) {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $message_type = 'danger';
    } else {
        $image_name = $product['image_principale'];
        
        $upload_dir = __DIR__ . '/../uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Gestion de l'image principale
        // Option 1: Upload depuis l'appareil
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $result = processFileUpload($_FILES['image'], $upload_dir);
            if ($result['success']) {
                // Supprimer l'ancienne image si elle existe
                if ($image_name && file_exists($upload_dir . $image_name)) {
                    unlink($upload_dir . $image_name);
                }
                $image_name = $result['filename'];
                resizeImage($upload_dir . $image_name, $upload_dir . $image_name, 1200, 1200);
            }
        }
        // Option 2: URL d'image
        elseif (!empty($_POST['image_url'])) {
            $result = downloadImageFromUrl($_POST['image_url'], $upload_dir);
            if ($result['success']) {
                // Supprimer l'ancienne image si elle existe
                if ($image_name && file_exists($upload_dir . $image_name)) {
                    unlink($upload_dir . $image_name);
                }
                $image_name = $result['filename'];
                resizeImage($upload_dir . $image_name, $upload_dir . $image_name, 1200, 1200);
            }
        }
        
        $stmt = $conn->prepare("UPDATE products SET nom = ?, description = ?, prix_achat = ?, prix_vente = ?, stock = ?, stock_minimum = ?, categorie_id = ?, vendeur_id = ?, image_principale = ?, statut = ? WHERE id = ?");
        $stmt->bind_param("ssddiiisssi", $nom, $description, $prix_achat, $prix_vente, $stock, $stock_minimum, $categorie_id, $vendeur_id, $image_name, $statut, $product_id);
        
        if ($stmt->execute()) {
            // Gestion des images supplémentaires (upload multiple)
            if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
                // Compter les images existantes
                $count_query = $conn->query("SELECT COUNT(*) as count FROM product_images WHERE product_id = $product_id");
                $count = $count_query->fetch_assoc()['count'];
                
                $uploaded_count = 0;
                for ($i = 0; $i < count($_FILES['additional_images']['name']); $i++) {
                    if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $_FILES['additional_images']['name'][$i],
                            'type' => $_FILES['additional_images']['type'][$i],
                            'tmp_name' => $_FILES['additional_images']['tmp_name'][$i],
                            'error' => $_FILES['additional_images']['error'][$i],
                            'size' => $_FILES['additional_images']['size'][$i]
                        ];
                        
                        $result = processFileUpload($file, $upload_dir);
                        if ($result['success']) {
                            $filename = $result['filename'];
                            resizeImage($upload_dir . $filename, $upload_dir . $filename, 1200, 1200);
                            
                            $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path, ordre) VALUES (?, ?, ?)");
                            $ordre = $count + $uploaded_count + 1;
                            $img_stmt->bind_param("isi", $product_id, $filename, $ordre);
                            $img_stmt->execute();
                            $img_stmt->close();
                            $uploaded_count++;
                        }
                    }
                }
            }
            
            // Gestion des images supplémentaires via URLs
            if (!empty($_POST['additional_image_urls'])) {
                $count_query = $conn->query("SELECT COUNT(*) as count FROM product_images WHERE product_id = $product_id");
                $count = $count_query->fetch_assoc()['count'];
                
                $urls = array_filter(array_map('trim', explode("\n", $_POST['additional_image_urls'])));
                $ordre = $count + 1;
                
                foreach ($urls as $url) {
                    if (empty($url)) continue;
                    
                    $result = downloadImageFromUrl($url, $upload_dir);
                    if ($result['success']) {
                        $filename = $result['filename'];
                        resizeImage($upload_dir . $filename, $upload_dir . $filename, 1200, 1200);
                        
                        $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path, ordre) VALUES (?, ?, ?)");
                        $img_stmt->bind_param("isi", $product_id, $filename, $ordre);
                        $img_stmt->execute();
                        $img_stmt->close();
                        $ordre++;
                    }
                }
            }
            
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

require_once 'includes/admin_header.php';
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
                <label for="vendeur_id">Vendeur (optionnel)</label>
                <select id="vendeur_id" name="vendeur_id" class="form-control">
                    <option value="">Aucun (Admin)</option>
                    <?php while ($vendeur = $vendeurs->fetch_assoc()): ?>
                        <option value="<?php echo $vendeur['id']; ?>" <?php echo $product['vendeur_id'] == $vendeur['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($vendeur['nom'] . ' ' . $vendeur['prenom']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="statut">Statut</label>
                <select id="statut" name="statut" class="form-control">
                    <option value="actif" <?php echo $product['statut'] === 'actif' ? 'selected' : ''; ?>>Actif</option>
                    <option value="inactif" <?php echo $product['statut'] === 'inactif' ? 'selected' : ''; ?>>Inactif</option>
                    <option value="rupture" <?php echo $product['statut'] === 'rupture' ? 'selected' : ''; ?>>Rupture de stock</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Image principale</label>
                <?php if ($product['image_principale']): ?>
                    <div style="margin-bottom: 1rem;">
                        <p><strong>Image actuelle:</strong></p>
                        <img src="<?php echo UPLOAD_URL . $product['image_principale']; ?>" style="max-width: 200px; border-radius: 5px; border: 2px solid #ddd;">
                    </div>
                <?php endif; ?>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label for="image" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                            <i class="fas fa-upload"></i> Upload depuis l'appareil
                        </label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                        <small style="color: #666;">Laisser vide pour conserver l'actuelle</small>
                    </div>
                    <div>
                        <label for="image_url" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                            <i class="fas fa-link"></i> Ou utiliser une URL
                        </label>
                        <input type="url" id="image_url" name="image_url" class="form-control" placeholder="https://exemple.com/image.jpg">
                    </div>
                </div>
                <div id="imagePreview" style="margin-top: 1rem; display: none;">
                    <p><strong>Nouvelle image:</strong></p>
                    <img id="previewImg" src="" alt="Aperçu" style="max-width: 300px; max-height: 300px; border-radius: 5px; border: 2px solid #ddd;">
                </div>
            </div>
            
            <?php
            // Récupérer les images supplémentaires existantes
            $existing_images = $conn->query("SELECT * FROM product_images WHERE product_id = $product_id ORDER BY ordre");
            ?>
            
            <div class="form-group">
                <label>Images supplémentaires</label>
                <?php if ($existing_images->num_rows > 0): ?>
                    <div style="margin-bottom: 1rem;">
                        <p><strong>Images actuelles (<?php echo $existing_images->num_rows; ?>):</strong></p>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                            <?php while ($img = $existing_images->fetch_assoc()): ?>
                                <div style="position: relative;">
                                    <img src="<?php echo UPLOAD_URL . $img['image_path']; ?>" 
                                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px; border: 2px solid #ddd;">
                                    <a href="?action=delete_image&id=<?php echo $img['id']; ?>&product_id=<?php echo $product_id; ?>" 
                                       onclick="return confirm('Supprimer cette image ?');"
                                       style="position: absolute; top: -5px; right: -5px; background: #e74c3c; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 12px;">×</a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div style="margin-bottom: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <label for="additional_images" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                            <i class="fas fa-images"></i> Ajouter des images (upload multiple)
                        </label>
                        <input type="file" id="additional_images" name="additional_images[]" class="form-control" accept="image/*" multiple>
                    </div>
                    <div>
                        <label for="additional_image_urls" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                            <i class="fas fa-link"></i> Ou utiliser des URLs (une par ligne)
                        </label>
                        <textarea id="additional_image_urls" name="additional_image_urls" class="form-control" rows="3" placeholder="https://exemple.com/image1.jpg&#10;https://exemple.com/image2.jpg"></textarea>
                    </div>
                    <div id="additionalImagesPreview" style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem;"></div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="products.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
// Aperçu de l'image principale (upload)
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Aperçu de l'image principale (URL)
document.getElementById('image_url').addEventListener('blur', function(e) {
    const url = e.target.value;
    if (url && url.startsWith('http')) {
        document.getElementById('previewImg').src = url;
        document.getElementById('imagePreview').style.display = 'block';
    }
});

// Aperçu des images supplémentaires (upload multiple)
document.getElementById('additional_images').addEventListener('change', function(e) {
    const preview = document.getElementById('additionalImagesPreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '5px';
            img.style.border = '2px solid #ddd';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});

// Aperçu des images supplémentaires (URLs)
document.getElementById('additional_image_urls').addEventListener('blur', function(e) {
    const urls = e.target.value.split('\n').filter(url => url.trim() && url.startsWith('http'));
    const preview = document.getElementById('additionalImagesPreview');
    preview.innerHTML = '';
    
    urls.forEach(url => {
        const img = document.createElement('img');
        img.src = url.trim();
        img.style.width = '100px';
        img.style.height = '100px';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '5px';
        img.style.border = '2px solid #ddd';
        img.onerror = function() { this.style.display = 'none'; };
        preview.appendChild(img);
    });
});
</script>

<?php
// Gestion de la suppression d'image
if (isset($_GET['action']) && $_GET['action'] === 'delete_image' && isset($_GET['id'])) {
    $img_id = (int)$_GET['id'];
    $prod_id = (int)$_GET['product_id'];
    
    $img_query = $conn->prepare("SELECT image_path FROM product_images WHERE id = ? AND product_id = ?");
    $img_query->bind_param("ii", $img_id, $prod_id);
    $img_query->execute();
    $img_result = $img_query->get_result();
    
    if ($img_result->num_rows > 0) {
        $img_data = $img_result->fetch_assoc();
        $img_path = __DIR__ . '/../uploads/products/' . $img_data['image_path'];
        
        if (file_exists($img_path)) {
            unlink($img_path);
        }
        
        $delete_stmt = $conn->prepare("DELETE FROM product_images WHERE id = ?");
        $delete_stmt->bind_param("i", $img_id);
        $delete_stmt->execute();
        $delete_stmt->close();
        
        header('Location: product_edit.php?id=' . $prod_id);
        exit();
    }
    $img_query->close();
}

$product_query->close();
$conn->close();
require_once 'includes/admin_footer.php';
?>

