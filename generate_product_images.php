<?php
/**
 * Script pour générer et insérer des images pour les produits
 * Génère des images placeholder colorées et les associe aux produits
 */
require_once 'config/database.php';

// Ne pas charger config.php pour éviter les problèmes de session
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/uploads/products/');
}

$conn = getDBConnection();
$errors = [];
$success = [];

// Créer le dossier uploads si nécessaire
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

/**
 * Génère une image placeholder colorée avec gradient
 */
function generatePlaceholderImage($width, $height, $text, $bgColor, $textColor = '#FFFFFF', $filename, $variant = 1) {
    // Créer une image
    $image = imagecreatetruecolor($width, $height);
    
    // Convertir les couleurs hex en RGB
    $bg = hex2rgb($bgColor);
    $txt = hex2rgb($textColor);
    
    $bgColor = imagecolorallocate($image, $bg['r'], $bg['g'], $bg['b']);
    $textColor = imagecolorallocate($image, $txt['r'], $txt['g'], $txt['b']);
    
    // Créer un gradient selon la variante
    if ($variant == 1) {
        // Gradient vertical
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = (int)($bg['r'] * (1 - $ratio * 0.3));
            $g = (int)($bg['g'] * (1 - $ratio * 0.3));
            $b = (int)($bg['b'] * (1 - $ratio * 0.3));
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }
    } else {
        // Fond uni avec motif
        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);
        
        // Ajouter des cercles décoratifs
        $circleColor = imagecolorallocatealpha($image, 255, 255, 255, 60);
        for ($i = 0; $i < 3; $i++) {
            $cx = rand($width * 0.2, $width * 0.8);
            $cy = rand($height * 0.2, $height * 0.8);
            $radius = rand(50, 150);
            imagefilledellipse($image, $cx, $cy, $radius, $radius, $circleColor);
        }
    }
    
    // Ajouter un rectangle pour le style
    $borderColor = imagecolorallocate($image, 255, 255, 255);
    imagerectangle($image, 10, 10, $width - 11, $height - 11, $borderColor);
    imagerectangle($image, 12, 12, $width - 13, $height - 13, $borderColor);
    
    // Ajouter le texte (nom du produit) - gérer le multiligne
    $lines = explode("\n", $text);
    $fontSize = 5;
    
    // Utiliser une police plus grande si disponible
    if (function_exists('imagettftext')) {
        // Essayer d'utiliser une police système
        $fonts = [
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/calibri.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/System/Library/Fonts/Helvetica.ttc'
        ];
        
        $fontFound = false;
        $fontPath = null;
        foreach ($fonts as $font) {
            if (file_exists($font)) {
                $fontPath = $font;
                $fontFound = true;
                break;
            }
        }
        
        if ($fontFound && $fontPath) {
            // Calculer la taille de police adaptée
            $maxLineLength = max(array_map('strlen', $lines));
            $fontSize = min(36, $width / (max($maxLineLength, 10) * 0.4));
            $fontSize = max(18, $fontSize);
            
            $lineHeight = $fontSize * 1.3;
            $totalHeight = count($lines) * $lineHeight;
            $startY = ($height - $totalHeight) / 2 + $fontSize;
            
            // Ombre du texte
            $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 50);
            
            foreach ($lines as $index => $line) {
                if (empty(trim($line))) continue;
                
                $bbox = imagettfbbox($fontSize, 0, $fontPath, $line);
                $textX = ($width - abs($bbox[4] - $bbox[0])) / 2;
                $textY = $startY + ($index * $lineHeight);
                
                // Ombre
                imagettftext($image, $fontSize, 0, $textX + 3, $textY + 3, $shadowColor, $fontPath, $line);
                // Texte principal
                imagettftext($image, $fontSize, 0, $textX, $textY, $textColor, $fontPath, $line);
            }
        } else {
            // Fallback sans police TTF
            $lineHeight = imagefontheight($fontSize) + 5;
            $totalHeight = count($lines) * $lineHeight;
            $startY = ($height - $totalHeight) / 2;
            
            foreach ($lines as $index => $line) {
                $textX = ($width - (strlen($line) * imagefontwidth($fontSize))) / 2;
                $textY = $startY + ($index * $lineHeight);
                imagestring($image, $fontSize, $textX, $textY, $line, $textColor);
            }
        }
    } else {
        // Fallback sans police TTF
        $lineHeight = imagefontheight($fontSize) + 5;
        $totalHeight = count($lines) * $lineHeight;
        $startY = ($height - $totalHeight) / 2;
        
        foreach ($lines as $index => $line) {
            $textX = ($width - (strlen($line) * imagefontwidth($fontSize))) / 2;
            $textY = $startY + ($index * $lineHeight);
            imagestring($image, $fontSize, $textX, $textY, $line, $textColor);
        }
    }
    
    // Ajouter un numéro de vue en bas à droite
    if ($variant > 1) {
        $viewText = "Vue " . $variant;
        $viewX = $width - (strlen($viewText) * imagefontwidth(3)) - 15;
        $viewY = $height - imagefontheight(3) - 15;
        $viewBg = imagecolorallocatealpha($image, 0, 0, 0, 50);
        imagefilledrectangle($image, $viewX - 5, $viewY - 2, $viewX + strlen($viewText) * 6 + 5, $viewY + 12, $viewBg);
        imagestring($image, 3, $viewX, $viewY, $viewText, $textColor);
    }
    
    // Sauvegarder l'image
    imagejpeg($image, $filename, 90);
    imagedestroy($image);
    
    return true;
}

function hex2rgb($hex) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return ['r' => $r, 'g' => $g, 'b' => $b];
}

// Couleurs par catégorie pour varier les images
$categoryColors = [
    4 => ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F'], // Vêtements Enfants
    5 => ['#6C5CE7', '#A29BFE', '#FD79A8', '#FDCB6E', '#E17055', '#00B894'], // Vêtements Adultes
    7 => ['#E8F4F8', '#D4E6F1', '#AED6F1', '#85C1E2', '#5DADE2', '#3498DB'], // Vaisselle
    8 => ['#F8C471', '#F39C12', '#E67E22', '#D35400', '#E74C3C', '#C0392B'], // Électroménagers
    9 => ['#A9DFBF', '#7DCEA0', '#52BE80', '#27AE60', '#229954', '#1E8449'], // Accessoires Cuisine
    10 => ['#D5DBDB', '#AEB6BF', '#85929E', '#5D6D7E', '#34495E', '#2C3E50'], // Meubles
    11 => ['#F1948A', '#EC7063', '#E74C3C', '#CB4335', '#A93226', '#922B21'], // Objets Déco
    12 => ['#F9E79F', '#F7DC6F', '#F4D03F', '#F1C40F', '#D4AC0D', '#B7950B'], // Luminaires
];

// Récupérer tous les produits
$products = $conn->query("SELECT id, nom, categorie_id FROM products ORDER BY id");

$total_images = 0;
$products_updated = 0;

while ($product = $products->fetch_assoc()) {
    $product_id = $product['id'];
    $product_name = $product['nom'];
    $category_id = $product['categorie_id'];
    
    // Déterminer les couleurs à utiliser
    $colors = $categoryColors[$category_id] ?? ['#3498DB', '#2ECC71', '#E74C3C', '#F39C12', '#9B59B6', '#1ABC9C'];
    
    // Vérifier si le produit a déjà une image principale
    $check_main = $conn->query("SELECT image_principale FROM products WHERE id = $product_id");
    $has_main_image = false;
    if ($check_main && $row = $check_main->fetch_assoc()) {
        $has_main_image = !empty($row['image_principale']) && file_exists(UPLOAD_DIR . $row['image_principale']);
    }
    
    // Générer l'image principale si elle n'existe pas
    if (!$has_main_image) {
        $main_image_name = 'product_' . $product_id . '_main.jpg';
        $main_image_path = UPLOAD_DIR . $main_image_name;
        
        $display_name = strlen($product_name) > 25 ? substr($product_name, 0, 22) . '...' : $product_name;
        if (generatePlaceholderImage(800, 800, $display_name, $colors[0], '#FFFFFF', $main_image_path, 1)) {
            // Mettre à jour le produit avec l'image principale
            $update = $conn->prepare("UPDATE products SET image_principale = ? WHERE id = ?");
            $update->bind_param("si", $main_image_name, $product_id);
            if ($update->execute()) {
                $success[] = "✓ Image principale créée pour: $product_name";
                $total_images++;
            }
            $update->close();
        }
    }
    
    // Vérifier combien d'images supplémentaires existent
    $existing_images = $conn->query("SELECT COUNT(*) as count FROM product_images WHERE product_id = $product_id");
    $count = $existing_images->fetch_assoc()['count'];
    
    // Générer 6 images supplémentaires (ou compléter jusqu'à 6)
    $images_to_create = max(0, 6 - $count);
    
    // Textes variés pour les images
    $text_variants = [
        'Vue de face',
        'Vue de côté',
        'Détail',
        'Vue arrière',
        'En situation',
        'Packaging'
    ];
    
    for ($i = 0; $i < $images_to_create; $i++) {
        $image_name = 'product_' . $product_id . '_' . ($count + $i + 1) . '.jpg';
        $image_path = UPLOAD_DIR . $image_name;
        
        // Utiliser différentes couleurs pour varier
        $color_index = ($count + $i) % count($colors);
        $bg_color = $colors[$color_index];
        
        // Texte pour cette image
        $text = $text_variants[$i % count($text_variants)];
        $display_name = strlen($product_name) > 25 ? substr($product_name, 0, 22) . '...' : $product_name;
        $full_text = $display_name . "\n" . $text;
        
        // Variante d'image (1 = gradient, 2+ = avec cercles)
        $variant = ($i % 2) + 1;
        
        if (generatePlaceholderImage(800, 800, $full_text, $bg_color, '#FFFFFF', $image_path, $variant + 1)) {
            // Insérer dans product_images
            $insert = $conn->prepare("INSERT INTO product_images (product_id, image_path, ordre) VALUES (?, ?, ?)");
            $ordre = $count + $i + 1;
            $insert->bind_param("isi", $product_id, $image_name, $ordre);
            if ($insert->execute()) {
                $total_images++;
            }
            $insert->close();
        }
    }
    
    if ($images_to_create > 0) {
        $products_updated++;
        $success[] = "✓ $images_to_create images ajoutées pour: $product_name";
    } elseif ($has_main_image && $count >= 6) {
        // Produit déjà complet
        $success[] = "→ $product_name a déjà toutes ses images ($count images)";
    }
}

// Statistiques finales
$stats_query = $conn->query("SELECT 
    COUNT(DISTINCT p.id) as produits_avec_images,
    COUNT(pi.id) as total_images
    FROM products p
    LEFT JOIN product_images pi ON p.id = pi.product_id
    WHERE p.image_principale IS NOT NULL AND p.image_principale != ''");
$stats = $stats_query->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération d'Images Produits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
        }
        .success {
            color: #27ae60;
            background: #d4edda;
            padding: 8px;
            border-radius: 5px;
            margin: 5px 0;
            font-size: 0.9rem;
        }
        .error {
            color: #e74c3c;
            background: #f8d7da;
            padding: 8px;
            border-radius: 5px;
            margin: 5px 0;
        }
        .info {
            background: #d1ecf1;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 20px 0;
        }
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 5px;
            text-align: center;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 2rem;
        }
        .stat-box p {
            margin: 0.5rem 0 0 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            margin-right: 10px;
        }
        .btn:hover {
            background: #2980b9;
        }
        .success-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎨 Génération d'Images Produits</h1>
        
        <div class="stats">
            <div class="stat-box">
                <h3><?php echo $total_images; ?></h3>
                <p>Images générées</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $products_updated; ?></h3>
                <p>Produits mis à jour</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $stats['total_images'] ?? 0; ?></h3>
                <p>Total images en base</p>
            </div>
        </div>
        
        <?php if (!empty($success)): ?>
            <h2>✅ Succès (<?php echo count($success); ?>):</h2>
            <div class="success-list">
                <?php foreach (array_slice($success, 0, 50) as $msg): ?>
                    <div class="success"><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
                <?php if (count($success) > 50): ?>
                    <p style="text-align: center; color: #666; margin-top: 10px;">
                        ... et <?php echo count($success) - 50; ?> autres succès
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <h2>❌ Erreurs (<?php echo count($errors); ?>):</h2>
            <?php foreach ($errors as $msg): ?>
                <div class="error"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if ($total_images > 0): ?>
            <div class="info">
                <h3>✅ Génération terminée !</h3>
                <p><strong><?php echo $total_images; ?></strong> images ont été générées et associées aux produits.</p>
                <p><strong><?php echo $products_updated; ?></strong> produits ont été mis à jour avec des images.</p>
                <p>Chaque produit a maintenant au minimum 6 images (1 principale + 5 supplémentaires).</p>
                <p><strong>Note:</strong> Les images sont des placeholders colorés. Vous pouvez les remplacer par de vraies photos plus tard.</p>
            </div>
            
            <a href="shop.php" class="btn">Voir la boutique</a>
            <a href="admin/products.php" class="btn">Gérer les produits</a>
        <?php else: ?>
            <div class="info">
                <p>Aucune image n'a été générée. Vérifiez que l'extension GD est activée dans PHP.</p>
                <p>Pour activer GD, modifiez votre php.ini et ajoutez: <code>extension=gd</code></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

