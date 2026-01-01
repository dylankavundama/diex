<?php
/**
 * Fonctions utilitaires pour la gestion des images
 */

/**
 * Télécharge une image depuis une URL
 */
function downloadImageFromUrl($url, $destination_dir) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return ['success' => false, 'error' => 'URL invalide'];
    }
    
    // Vérifier que c'est une image
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $url_path = parse_url($url, PHP_URL_PATH);
    $extension = strtolower(pathinfo($url_path, PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowed_extensions)) {
        // Essayer de détecter le type depuis les headers
        $headers = @get_headers($url, 1);
        if (!$headers) {
            return ['success' => false, 'error' => 'Impossible d\'accéder à l\'URL'];
        }
        
        $content_type = isset($headers['Content-Type']) ? $headers['Content-Type'] : '';
        if (is_array($content_type)) {
            $content_type = end($content_type);
        }
        
        if (strpos($content_type, 'image/') === false) {
            return ['success' => false, 'error' => 'L\'URL ne pointe pas vers une image'];
        }
        
        // Déterminer l'extension depuis le content-type
        $content_type_map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        $extension = $content_type_map[$content_type] ?? 'jpg';
    }
    
    // Télécharger l'image
    $image_data = @file_get_contents($url);
    if ($image_data === false) {
        return ['success' => false, 'error' => 'Impossible de télécharger l\'image'];
    }
    
    // Vérifier que c'est bien une image
    $temp_file = tempnam(sys_get_temp_dir(), 'img_');
    file_put_contents($temp_file, $image_data);
    $image_info = @getimagesize($temp_file);
    
    if ($image_info === false) {
        unlink($temp_file);
        return ['success' => false, 'error' => 'Le fichier téléchargé n\'est pas une image valide'];
    }
    
    // Générer un nom unique
    $filename = uniqid() . '.' . $extension;
    $destination = $destination_dir . $filename;
    
    // Copier le fichier
    if (copy($temp_file, $destination)) {
        unlink($temp_file);
        return ['success' => true, 'filename' => $filename];
    } else {
        unlink($temp_file);
        return ['success' => false, 'error' => 'Erreur lors de l\'enregistrement'];
    }
}

/**
 * Traite un upload de fichier
 */
function processFileUpload($file, $destination_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erreur lors de l\'upload'];
    }
    
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_ext, $allowed_ext)) {
        return ['success' => false, 'error' => 'Type de fichier non autorisé'];
    }
    
    // Vérifier la taille (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Fichier trop volumineux (max 5MB)'];
    }
    
    // Vérifier que c'est bien une image
    $image_info = @getimagesize($file['tmp_name']);
    if ($image_info === false) {
        return ['success' => false, 'error' => 'Le fichier n\'est pas une image valide'];
    }
    
    // Générer un nom unique
    $filename = uniqid() . '.' . $file_ext;
    $destination = $destination_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Erreur lors de l\'enregistrement'];
    }
}

/**
 * Redimensionne une image
 */
function resizeImage($source_path, $destination_path, $max_width = 1200, $max_height = 1200, $quality = 85) {
    if (!file_exists($source_path)) {
        return false;
    }
    
    $image_info = getimagesize($source_path);
    if ($image_info === false) {
        return false;
    }
    
    $source_width = $image_info[0];
    $source_height = $image_info[1];
    $mime_type = $image_info['mime'];
    
    // Calculer les nouvelles dimensions
    $ratio = min($max_width / $source_width, $max_height / $source_height);
    $new_width = (int)($source_width * $ratio);
    $new_height = (int)($source_height * $ratio);
    
    // Créer l'image source
    switch ($mime_type) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            $source_image = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    if ($source_image === false) {
        return false;
    }
    
    // Créer l'image de destination
    $destination_image = imagecreatetruecolor($new_width, $new_height);
    
    // Préserver la transparence pour PNG et GIF
    if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
        imagealphablending($destination_image, false);
        imagesavealpha($destination_image, true);
        $transparent = imagecolorallocatealpha($destination_image, 255, 255, 255, 127);
        imagefilledrectangle($destination_image, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // Redimensionner
    imagecopyresampled($destination_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $source_width, $source_height);
    
    // Sauvegarder
    $result = false;
    switch ($mime_type) {
        case 'image/jpeg':
            $result = imagejpeg($destination_image, $destination_path, $quality);
            break;
        case 'image/png':
            $result = imagepng($destination_image, $destination_path, 9);
            break;
        case 'image/gif':
            $result = imagegif($destination_image, $destination_path);
            break;
        case 'image/webp':
            $result = imagewebp($destination_image, $destination_path, $quality);
            break;
    }
    
    imagedestroy($source_image);
    imagedestroy($destination_image);
    
    return $result;
}
?>

