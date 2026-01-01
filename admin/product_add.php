<?php
$page_title = "Ajouter un Produit";
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/image_functions.php';
requireRole(ROLE_ADMIN);

$conn = getDBConnection();
$message = '';
$message_type = '';

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
    
    if (empty($nom) || $prix_achat <= 0 || $prix_vente <= 0 || $categorie_id <= 0) {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $message_type = 'danger';
    } else {
        $upload_dir = __DIR__ . '/../uploads/products/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Gestion de l'image principale
        $image_name = '';
        
        // Option 1: Upload depuis l'appareil
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $result = processFileUpload($_FILES['image'], $upload_dir);
            if ($result['success']) {
                $image_name = $result['filename'];
                // Redimensionner si nécessaire
                resizeImage($upload_dir . $image_name, $upload_dir . $image_name, 1200, 1200);
            } else {
                $message = $result['error'];
                $message_type = 'danger';
            }
        }
        // Option 2: URL d'image
        elseif (!empty($_POST['image_url'])) {
            $result = downloadImageFromUrl($_POST['image_url'], $upload_dir);
            if ($result['success']) {
                $image_name = $result['filename'];
                // Redimensionner si nécessaire
                resizeImage($upload_dir . $image_name, $upload_dir . $image_name, 1200, 1200);
            } else {
                $message = $result['error'];
                $message_type = 'danger';
            }
        }
        
        // Insérer le produit
        $stmt = $conn->prepare("INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, vendeur_id, image_principale) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddiiiss", $nom, $description, $prix_achat, $prix_vente, $stock, $stock_minimum, $categorie_id, $vendeur_id, $image_name);
        
        if ($stmt->execute()) {
            $product_id = $conn->insert_id;
            
            // Gestion des images supplémentaires (upload multiple)
            if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
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
                            $ordre = $uploaded_count + 1;
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
                $urls = array_filter(array_map('trim', explode("\n", $_POST['additional_image_urls'])));
                $ordre = isset($uploaded_count) ? $uploaded_count + 1 : 1;
                
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
            
            $message = 'Produit ajouté avec succès.';
            $message_type = 'success';
            header('Location: products.php');
            exit();
        } else {
            $message = 'Erreur lors de l\'ajout du produit.';
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
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="5"></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="prix_achat">Prix d'achat (USD) *</label>
                    <input type="number" id="prix_achat" name="prix_achat" class="form-control" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="prix_vente">Prix de vente (USD) *</label>
                    <input type="number" id="prix_vente" name="prix_vente" class="form-control" step="0.01" min="0" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="stock">Stock *</label>
                    <input type="number" id="stock" name="stock" class="form-control" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="stock_minimum">Stock minimum</label>
                    <input type="number" id="stock_minimum" name="stock_minimum" class="form-control" min="0" value="5">
                </div>
            </div>
            
            <div class="form-group">
                <label for="categorie_id">Catégorie *</label>
                <select id="categorie_id" name="categorie_id" class="form-control" required>
                    <option value="">Sélectionner une catégorie</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nom']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="vendeur_id">Vendeur (optionnel)</label>
                <select id="vendeur_id" name="vendeur_id" class="form-control">
                    <option value="">Aucun (Admin)</option>
                    <?php while ($vendeur = $vendeurs->fetch_assoc()): ?>
                        <option value="<?php echo $vendeur['id']; ?>">
                            <?php echo htmlspecialchars($vendeur['nom'] . ' ' . $vendeur['prenom']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Image principale</label>
                <div style="margin-bottom: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label for="image" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                                <i class="fas fa-upload"></i> Upload depuis l'appareil
                            </label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                            <small style="color: #666;">Formats acceptés: JPG, PNG, GIF, WEBP (max 5MB)</small>
                        </div>
                        <div>
                            <label for="image_url" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                                <i class="fas fa-link"></i> Ou utiliser une URL
                            </label>
                            <input type="url" id="image_url" name="image_url" class="form-control" placeholder="https://exemple.com/image.jpg">
                            <small style="color: #666;">Collez l'URL d'une image en ligne</small>
                        </div>
                    </div>
                    <div id="imagePreview" style="margin-top: 1rem; display: none;">
                        <img id="previewImg" src="" alt="Aperçu" style="max-width: 300px; max-height: 300px; border-radius: 5px; border: 2px solid #ddd;">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Images supplémentaires (optionnel)</label>
                <div style="margin-bottom: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <label for="additional_images" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                            <i class="fas fa-images"></i> Upload multiple depuis l'appareil
                        </label>
                        <input type="file" id="additional_images" name="additional_images[]" class="form-control" accept="image/*" multiple>
                        <small style="color: #666;">Sélectionnez plusieurs images (Ctrl+clic ou Cmd+clic)</small>
                    </div>
                    <div>
                        <label for="additional_image_urls" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                            <i class="fas fa-link"></i> Ou utiliser des URLs (une par ligne)
                        </label>
                        <textarea id="additional_image_urls" name="additional_image_urls" class="form-control" rows="3" placeholder="https://exemple.com/image1.jpg&#10;https://exemple.com/image2.jpg&#10;https://exemple.com/image3.jpg"></textarea>
                        <small style="color: #666;">Une URL par ligne</small>
                    </div>
                    <div id="additionalImagesPreview" style="margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem;"></div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Ajouter le produit</button>
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
$conn->close();
require_once 'includes/admin_footer.php';
?>

