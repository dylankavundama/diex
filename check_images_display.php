<?php
/**
 * Script pour vérifier pourquoi les images ne s'affichent pas
 */
require_once 'config/config.php';
require_once 'config/database.php';

$conn = getDBConnection();

// Récupérer quelques produits
$products = $conn->query("SELECT id, nom, image_principale, categorie_id FROM products LIMIT 10");

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test Images</title></head><body>";
echo "<h1>Test d'affichage des images</h1>";
echo "<p><strong>UPLOAD_URL configuré:</strong> " . UPLOAD_URL . "</p>";
echo "<hr>";

while ($product = $products->fetch_assoc()) {
    echo "<div style='border: 2px solid #ddd; padding: 20px; margin: 20px 0;'>";
    echo "<h3>Produit: " . htmlspecialchars($product['nom']) . " (ID: {$product['id']})</h3>";
    
    if ($product['image_principale']) {
        $image_url = UPLOAD_URL . $product['image_principale'];
        $image_path = UPLOAD_DIR . $product['image_principale'];
        
        echo "<p><strong>Image principale:</strong> " . htmlspecialchars($product['image_principale']) . "</p>";
        echo "<p><strong>Chemin fichier:</strong> " . htmlspecialchars($image_path) . "</p>";
        echo "<p><strong>Fichier existe:</strong> " . (file_exists($image_path) ? '✓ OUI' : '✗ NON') . "</p>";
        echo "<p><strong>URL complète:</strong> <a href='" . htmlspecialchars($image_url) . "' target='_blank'>" . htmlspecialchars($image_url) . "</a></p>";
        
        echo "<div style='margin: 10px 0;'>";
        echo "<strong>Test d'affichage:</strong><br>";
        echo "<img src='" . htmlspecialchars($image_url) . "' ";
        echo "alt='Test' ";
        echo "style='max-width: 300px; border: 3px solid " . (file_exists($image_path) ? 'green' : 'red') . "; margin: 10px 0;' ";
        echo "onerror=\"alert('ERREUR: Image non accessible'); this.style.border='3px solid red';\">";
        echo "</div>";
        
        // Test avec le même code que dans shop.php
        echo "<div style='margin: 10px 0; padding: 10px; background: #f0f0f0;'>";
        echo "<strong>Code utilisé dans shop.php:</strong><br>";
        echo "<code>";
        echo "&lt;?php if (\$product['image_principale']): ?&gt;<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&lt;img src=\"&lt;?php echo UPLOAD_URL . \$product['image_principale']; ?&gt;\"&gt;<br>";
        echo "&lt;?php endif; ?&gt;";
        echo "</code><br><br>";
        echo "<strong>Résultat:</strong><br>";
        if ($product['image_principale']) {
            echo "<img src='" . UPLOAD_URL . $product['image_principale'] . "' alt='" . htmlspecialchars($product['nom']) . "' style='max-width: 200px;'>";
        }
        echo "</div>";
    } else {
        echo "<p style='color: red;'><strong>⚠️ Aucune image principale définie pour ce produit!</strong></p>";
    }
    
    echo "</div>";
}

$conn->close();
echo "</body></html>";
?>

