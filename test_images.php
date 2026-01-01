<?php
/**
 * Script de test pour vérifier l'accès aux images
 */
require_once 'config/config.php';
require_once 'config/database.php';

$conn = getDBConnection();

// Récupérer quelques produits avec images
$products = $conn->query("SELECT id, nom, image_principale FROM products WHERE image_principale IS NOT NULL AND image_principale != '' LIMIT 5");

echo "<h1>Test d'accès aux images</h1>";
echo "<p><strong>UPLOAD_DIR:</strong> " . UPLOAD_DIR . "</p>";
echo "<p><strong>UPLOAD_URL:</strong> " . UPLOAD_URL . "</p>";
echo "<hr>";

echo "<h2>Vérification des fichiers:</h2>";
while ($product = $products->fetch_assoc()) {
    $file_path = UPLOAD_DIR . $product['image_principale'];
    $file_exists = file_exists($file_path);
    $file_url = UPLOAD_URL . $product['image_principale'];
    
    echo "<div style='margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h3>" . htmlspecialchars($product['nom']) . "</h3>";
    echo "<p><strong>Fichier:</strong> " . htmlspecialchars($product['image_principale']) . "</p>";
    echo "<p><strong>Chemin complet:</strong> " . htmlspecialchars($file_path) . "</p>";
    echo "<p><strong>Fichier existe:</strong> " . ($file_exists ? '<span style="color: green;">✓ OUI</span>' : '<span style="color: red;">✗ NON</span>') . "</p>";
    echo "<p><strong>URL:</strong> <a href='" . htmlspecialchars($file_url) . "' target='_blank'>" . htmlspecialchars($file_url) . "</a></p>";
    
    if ($file_exists) {
        $file_size = filesize($file_path);
        echo "<p><strong>Taille:</strong> " . number_format($file_size / 1024, 2) . " KB</p>";
        echo "<img src='" . htmlspecialchars($file_url) . "' alt='Test' style='max-width: 200px; border: 2px solid #ddd; margin-top: 10px;' onerror='this.style.borderColor=\"red\"; this.alt=\"ERREUR: Image non accessible\";'>";
    }
    
    echo "</div>";
}

// Vérifier le dossier
echo "<hr>";
echo "<h2>Vérification du dossier:</h2>";
echo "<p><strong>Dossier existe:</strong> " . (is_dir(UPLOAD_DIR) ? '<span style="color: green;">✓ OUI</span>' : '<span style="color: red;">✗ NON</span>') . "</p>";
if (is_dir(UPLOAD_DIR)) {
    echo "<p><strong>Dossier accessible en écriture:</strong> " . (is_writable(UPLOAD_DIR) ? '<span style="color: green;">✓ OUI</span>' : '<span style="color: red;">✗ NON</span>') . "</p>";
    $files = glob(UPLOAD_DIR . '*.jpg');
    echo "<p><strong>Nombre de fichiers .jpg:</strong> " . count($files) . "</p>";
}

$conn->close();
?>

