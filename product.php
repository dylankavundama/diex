<?php
$page_title = "Détails Produit";
require_once 'includes/header.php';
require_once 'config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: shop.php');
    exit();
}

$product_id = (int)$_GET['id'];
$conn = getDBConnection();

// Récupérer les détails du produit
$query = "SELECT p.*, c.nom as categorie_nom, u.nom as vendeur_nom, u.telephone as vendeur_tel
          FROM products p 
          LEFT JOIN categories c ON p.categorie_id = c.id 
          LEFT JOIN users u ON p.vendeur_id = u.id 
          WHERE p.id = ? AND p.statut = 'actif'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: shop.php');
    exit();
}

$product = $result->fetch_assoc();

// Récupérer les images supplémentaires
$images_query = "SELECT * FROM product_images WHERE product_id = ? ORDER BY ordre";
$images_stmt = $conn->prepare($images_query);
$images_stmt->bind_param("i", $product_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();

// Récupérer les attributs (tailles, couleurs)
$attributes_query = "SELECT * FROM product_attributes WHERE product_id = ?";
$attributes_stmt = $conn->prepare($attributes_query);
$attributes_stmt->bind_param("i", $product_id);
$attributes_stmt->execute();
$attributes_result = $attributes_stmt->get_result();

$attributes = [];
while ($attr = $attributes_result->fetch_assoc()) {
    $attributes[$attr['attribute_type']][] = $attr;
}
?>

<section class="product-detail-section">
    <div class="container">
        <div class="product-detail">
            <div class="product-images">
                <img src="<?php echo $product['image_principale'] ? UPLOAD_URL . $product['image_principale'] : SITE_URL . '/assets/images/placeholder.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                     class="product-main-image" id="mainImage">
                
                <?php if ($images_result->num_rows > 0): ?>
                    <div class="product-thumbnails">
                        <?php if ($product['image_principale']): ?>
                            <img src="<?php echo UPLOAD_URL . $product['image_principale']; ?>" 
                                 class="product-thumbnail active" 
                                 onclick="changeMainImage(this.src)">
                        <?php endif; ?>
                        <?php while ($img = $images_result->fetch_assoc()): ?>
                            <img src="<?php echo UPLOAD_URL . $img['image_path']; ?>" 
                                 class="product-thumbnail" 
                                 onclick="changeMainImage(this.src)">
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="product-info-detail">
                <h1><?php echo htmlspecialchars($product['nom']); ?></h1>
                <p class="product-category"><?php echo htmlspecialchars($product['categorie_nom']); ?></p>
                <p class="product-price-detail"><?php echo formatPrice($product['prix_vente']); ?></p>
                
                <?php if ($product['stock'] > 0): ?>
                    <p class="stock-info" style="color: var(--success-color); margin: 1rem 0;">
                        <i class="fas fa-check-circle"></i> En stock (<?php echo $product['stock']; ?> disponibles)
                    </p>
                <?php else: ?>
                    <p class="stock-info" style="color: var(--accent-color); margin: 1rem 0;">
                        <i class="fas fa-times-circle"></i> Rupture de stock
                    </p>
                <?php endif; ?>
                
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <?php if (!empty($attributes)): ?>
                    <div class="product-attributes">
                        <?php foreach ($attributes as $type => $attrs): ?>
                            <div class="attribute-group">
                                <label><?php echo ucfirst($type); ?>:</label>
                                <div class="attribute-options">
                                    <?php foreach ($attrs as $attr): ?>
                                        <span class="attribute-option" data-type="<?php echo $type; ?>" data-value="<?php echo htmlspecialchars($attr['attribute_value']); ?>">
                                            <?php echo htmlspecialchars($attr['attribute_value']); ?>
                                            <?php if ($attr['stock'] > 0): ?>
                                                <small>(<?php echo $attr['stock']; ?>)</small>
                                            <?php endif; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="quantity-selector" style="margin: 2rem 0;">
                    <label for="quantity">Quantité:</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $product['stock']; ?>" value="1" style="width: 100px; padding: 0.5rem; margin-left: 1rem;">
                </div>
                
                <?php if ($product['stock'] > 0): ?>
                    <a href="#" 
                       class="whatsapp-order-btn" 
                       onclick="orderViaWhatsApp(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['nom'], ENT_QUOTES); ?>', <?php echo $product['prix_vente']; ?>); return false;">
                        <i class="fab fa-whatsapp"></i> Commander via WhatsApp
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>Produit indisponible</button>
                <?php endif; ?>
                
                <?php if ($product['vendeur_nom']): ?>
                    <p style="margin-top: 2rem; color: #666;">
                        <small>Vendeur: <?php echo htmlspecialchars($product['vendeur_nom']); ?></small>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function changeMainImage(src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.product-thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    event.target.classList.add('active');
}

function orderViaWhatsApp(productId, productName, price) {
    const quantity = document.getElementById('quantity').value;
    const selectedAttributes = [];
    
    document.querySelectorAll('.attribute-option.selected').forEach(option => {
        selectedAttributes.push({
            type: option.dataset.type,
            value: option.dataset.value
        });
    });
    
    const phoneNumber = '<?php echo WHATSAPP_NUMBER; ?>';
    let message = `Bonjour, je souhaite commander:\n\n`;
    message += `Produit: ${productName}\n`;
    message += `Prix unitaire: $${price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}\n`;
    message += `Quantité: ${quantity}\n`;
    message += `Total: $${(price * quantity).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}\n`;
    
    if (selectedAttributes.length > 0) {
        message += `\nOptions sélectionnées:\n`;
        selectedAttributes.forEach(attr => {
            message += `- ${attr.type}: ${attr.value}\n`;
        });
    }
    
    message += `\nMerci de me confirmer la disponibilité et les modalités de livraison.`;
    
    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}
</script>

<?php
$stmt->close();
$images_stmt->close();
$attributes_stmt->close();
$conn->close();
require_once 'includes/footer.php';
?>

