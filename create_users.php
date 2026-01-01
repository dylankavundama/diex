<?php
/**
 * Script pour créer les utilisateurs jodesie (admin) et flo (vendeur)
 * Mot de passe pour les deux: 1010
 * Exécutez ce fichier dans votre navigateur
 */
require_once 'config/database.php';
// Définir SITE_URL si non défini
if (!defined('SITE_URL')) {
    define('SITE_URL', 'http://localhost/diexo');
}

$conn = getDBConnection();
$errors = [];
$success = [];

// Hash du mot de passe "1010"
$password = '1010';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Utilisateurs à créer
$users = [
    [
        'nom' => 'Jodesie',
        'prenom' => 'Admin',
        'email' => 'jodesie@diexo.com',
        'password' => $hashed_password,
        'role' => 'admin',
        'statut' => 'actif'
    ],
    [
        'nom' => 'Flo',
        'prenom' => 'Vendeur',
        'email' => 'flo@diexo.com',
        'password' => $hashed_password,
        'role' => 'vendeur',
        'statut' => 'actif'
    ]
];

foreach ($users as $user) {
    // Vérifier si l'utilisateur existe déjà
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $user['email']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows > 0) {
        // Mettre à jour le mot de passe si l'utilisateur existe
        $update = $conn->prepare("UPDATE users SET password = ?, role = ?, statut = ? WHERE email = ?");
        $update->bind_param("ssss", $user['password'], $user['role'], $user['statut'], $user['email']);
        if ($update->execute()) {
            $success[] = "✓ Utilisateur mis à jour: {$user['nom']} ({$user['email']}) - Rôle: {$user['role']}";
        } else {
            $errors[] = "✗ Erreur lors de la mise à jour de {$user['nom']}: " . $update->error;
        }
        $update->close();
    } else {
        // Créer l'utilisateur
        $stmt = $conn->prepare("INSERT INTO users (nom, prenom, email, password, role, statut) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $user['nom'], $user['prenom'], $user['email'], $user['password'], $user['role'], $user['statut']);
        
        if ($stmt->execute()) {
            $success[] = "✓ Utilisateur créé: {$user['nom']} ({$user['email']}) - Rôle: {$user['role']}";
        } else {
            $errors[] = "✗ Erreur lors de la création de {$user['nom']}: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}

// Récupérer les utilisateurs créés
$created_users = $conn->query("SELECT id, nom, prenom, email, role, statut FROM users WHERE email IN ('jodesie@diexo.com', 'flo@diexo.com')");

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création d'Utilisateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
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
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        .error {
            color: #e74c3c;
            background: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }
        .info {
            background: #d1ecf1;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .user-table th,
        .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .user-table th {
            background: #2c3e50;
            color: white;
        }
        .user-table tr:hover {
            background: #f5f5f5;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .badge-admin {
            background: #e74c3c;
            color: white;
        }
        .badge-vendeur {
            background: #f39c12;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Création d'Utilisateurs</h1>
        
        <?php if (!empty($success)): ?>
            <h2>✅ Succès:</h2>
            <?php foreach ($success as $msg): ?>
                <div class="success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <h2>❌ Erreurs:</h2>
            <?php foreach ($errors as $msg): ?>
                <div class="error"><?php echo htmlspecialchars($msg); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if ($created_users->num_rows > 0): ?>
            <div class="info">
                <h3>Utilisateurs créés/mis à jour:</h3>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $created_users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                                <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user['role'] === 'admin' ? 'admin' : 'vendeur'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo ucfirst($user['statut']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px;">
                    <h4>🔐 Informations de connexion:</h4>
                    <p><strong>Administrateur (jodesie):</strong></p>
                    <ul>
                        <li>Email: <code>jodesie@diexo.com</code></li>
                        <li>Mot de passe: <code>1010</code></li>
                    </ul>
                    <p><strong>Vendeur (flo):</strong></p>
                    <ul>
                        <li>Email: <code>flo@diexo.com</code></li>
                        <li>Mot de passe: <code>1010</code></li>
                    </ul>
                    <p style="color: #856404; margin-top: 10px;">
                        <strong>⚠️ Important:</strong> Changez ces mots de passe après la première connexion pour des raisons de sécurité.
                    </p>
                </div>
            </div>
            
            <a href="<?php echo SITE_URL ?? 'http://localhost/diexo'; ?>/auth/login.php" class="btn">Se connecter</a>
            <a href="<?php echo SITE_URL ?? 'http://localhost/diexo'; ?>/admin/dashboard.php" class="btn">Dashboard Admin</a>
        <?php endif; ?>
    </div>
</body>
</html>

