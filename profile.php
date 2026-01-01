<?php
$page_title = "Mon Profil";
require_once 'includes/header.php';
require_once 'config/config.php';
require_once 'config/database.php';
requireLogin();

$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Récupérer les informations de l'utilisateur
$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();

// Mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nom = sanitize($_POST['nom'] ?? '');
    $prenom = sanitize($_POST['prenom'] ?? '');
    $telephone = sanitize($_POST['telephone'] ?? '');
    
    if (!empty($nom) && !empty($prenom)) {
        $update = $conn->prepare("UPDATE users SET nom = ?, prenom = ?, telephone = ? WHERE id = ?");
        $update->bind_param("sssi", $nom, $prenom, $telephone, $user_id);
        if ($update->execute()) {
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $message = 'Profil mis à jour avec succès.';
            $message_type = 'success';
            $user['nom'] = $nom;
            $user['prenom'] = $prenom;
            $user['telephone'] = $telephone;
        }
        $update->close();
    }
}

// Récupérer les commandes de l'utilisateur
$orders = $conn->query("SELECT * FROM orders WHERE client_id = $user_id ORDER BY created_at DESC LIMIT 10");

require_once 'includes/header.php';
?>

<div class="container" style="max-width: 1000px;">
    <h1>Mon Profil</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
        <div class="card">
            <div class="card-header">
                <h2>Informations personnelles</h2>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" class="form-control" required value="<?php echo htmlspecialchars($user['nom']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" required value="<?php echo htmlspecialchars($user['prenom']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    <small style="color: #666;">L'email ne peut pas être modifié</small>
                </div>
                
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="form-control" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>Mes commandes récentes</h2>
            </div>
            <?php if ($orders->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['numero_commande']); ?></td>
                                    <td><?php echo formatPrice($order['total']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $order['statut'] == 'livree' ? 'success' : 
                                                ($order['statut'] == 'en_attente' ? 'warning' : '');
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Aucune commande pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$user_query->close();
$conn->close();
require_once 'includes/footer.php';
?>

