<?php
/**
 * Script pour diagnostiquer et corriger les problèmes d'affichage des images
 */
require_once 'config/config.php';
require_once 'config/database.php';

$conn = getDBConnection();
$issues = [];
$fixed = [];

echo "<h1>Diagnostic des Images Produits</h1>";
echo "<p><strong>UPLOAD_DIR:</strong> " . UPLOAD_DIR . "</p>";
echo "<p><strong>UPLOAD_URL:</strong> " . UPLOAD_URL . "</p>";
echo "<hr>";

// Récupérer tous les produits avec images
$products = $conn->query("SELECT id, nom, image_principale FROM products WHERE image_principale IS NOT NULL AND image_principale != ''");

echo "<h2>Vérification des images principales:</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>ID</th><th>Nom</th><th>Image</th><th>Fichier existe</th><th>URL</th><th>Test</th></tr>";

while ($product = $products->fetch_assoc()) {
    $image_name = $product['image_principale'];
    $file_path = UPLOAD_DIR . $image_name;
    $file_exists = file_exists($file_path);
    $file_url = UPLOAD_URL . $image_name;
    
    $status = $file_exists ? '<span style="color: green;">✓ OUI</span>' : '<span style="color: red;">✗ NON</span>';
    
    echo "<tr>";
    echo "<td>" . $product['id'] . "</td>";
    echo "<td>" . htmlspecialchars($product['nom']) . "</td>";
    echo "<td>" . htmlspecialchars($image_name) . "</td>";
    echo "<td>" . $status . "</td>";
    echo "<td><a href='" . htmlspecialchars($file_url) . "' target='_blank'>" . htmlspecialchars($file_url) . "</a></td>";
    
    if ($file_exists) {
        echo "<td><img src='" . htmlspecialchars($file_url) . "' alt='Test' style='max-width: 100px; border: 2px solid #ddd;' onerror='this.style.borderColor=\"red\"; this.alt=\"ERREUR\";'></td>";
    } else {
        echo "<td style='color: red;'>Fichier manquant</td>";
        $issues[] = "Produit #{$product['id']}: {$product['nom']} - Image manquante: {$image_name}";
    }
    
    echo "</tr>";
}

echo "</table>";

// Vérifier les images supplémentaires
echo "<hr><h2>Vérification des images supplémentaires:</h2>";
$additional_images = $conn->query("SELECT pi.*, p.nom as product_nom FROM product_images pi JOIN products p ON pi.product_id = p.id LIMIT 20");

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Produit</th><th>Image</th><th>Fichier existe</th><th>URL</th></tr>";

$count = 0;
while ($img = $additional_images->fetch_assoc()) {
    if ($count++ >= 20) break;
    
    $file_path = UPLOAD_DIR . $img['image_path'];
    $file_exists = file_exists($file_path);
    $file_url = UPLOAD_URL . $img['image_path'];
    
    $status = $file_exists ? '<span style="color: green;">✓ OUI</span>' : '<span style="color: red;">✗ NON</span>';
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($img['product_nom']) . "</td>";
    echo "<td>" . htmlspecialchars($img['image_path']) . "</td>";
    echo "<td>" . $status . "</td>";
    echo "<td><a href='" . htmlspecialchars($file_url) . "' target='_blank'>" . htmlspecialchars($file_url) . "</a></td>";
    echo "</tr>";
}

echo "</table>";

// Statistiques
$total_products = $conn->query("SELECT COUNT(*) as total FROM products WHERE image_principale IS NOT NULL AND image_principale != ''")->fetch_assoc()['total'];
$total_images = $conn->query("SELECT COUNT(*) as total FROM product_images")->fetch_assoc()['total'];

echo "<hr>";
echo "<h2>Statistiques:</h2>";
echo "<ul>";
echo "<li>Produits avec image principale: <strong>$total_products</strong></li>";
echo "<li>Images supplémentaires: <strong>$total_images</strong></li>";
echo "<li>Fichiers dans le dossier: <strong>" . count(glob(UPLOAD_DIR . '*.jpg')) . "</strong></li>";
echo "</ul>";

if (!empty($issues)) {
    echo "<hr><h2 style='color: red;'>Problèmes détectés:</h2>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li>" . htmlspecialchars($issue) . "</li>";
    }
    echo "</ul>";
    echo "<p><strong>Solution:</strong> Exécutez <code>generate_product_images.php</code> pour régénérer les images manquantes.</p>";
} else {
    echo "<hr><h2 style='color: green;'>✓ Tous les fichiers images existent!</h2>";
}

$conn->close();
?>

