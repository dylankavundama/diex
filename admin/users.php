<?php
$page_title = "Gestion des Utilisateurs";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_ADMIN);

$conn = getDBConnection();
$message = '';
$message_type = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $nom = sanitize($_POST['nom'] ?? '');
        $prenom = sanitize($_POST['prenom'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $telephone = sanitize($_POST['telephone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = sanitize($_POST['role'] ?? 'client');
        
        if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (nom, prenom, email, telephone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $nom, $prenom, $email, $telephone, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $message = 'Utilisateur créé avec succès.';
                $message_type = 'success';
            } else {
                $message = 'Erreur lors de la création. Email peut-être déjà utilisé.';
                $message_type = 'danger';
            }
            $stmt->close();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $user_id = (int)$_POST['user_id'];
        $statut = sanitize($_POST['statut']);
        
        $stmt = $conn->prepare("UPDATE users SET statut = ? WHERE id = ?");
        $stmt->bind_param("si", $statut, $user_id);
        if ($stmt->execute()) {
            $message = 'Statut mis à jour.';
            $message_type = 'success';
        }
        $stmt->close();
    }
}

// Récupérer tous les utilisateurs
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

require_once 'includes/admin_header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 1.5rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="page-actions">
    <button onclick="document.getElementById('createModal').style.display='block'" class="btn btn-primary">
        <i class="fas fa-plus"></i> Créer un utilisateur
    </button>
</div>

<div class="content-card">
    <div class="content-card-header">
        <h2><i class="fas fa-users"></i> Liste des Utilisateurs</h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Date création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->num_rows > 0): ?>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['telephone'] ?? '-'); ?></td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $user['role'] === 'admin' ? 'danger' : 
                                        ($user['role'] === 'vendeur' ? 'warning' : '');
                                ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="action" value="update_status">
                                    <select name="statut" onchange="this.form.submit()" style="padding: 0.5rem; border-radius: 5px;">
                                        <option value="actif" <?php echo $user['statut'] === 'actif' ? 'selected' : ''; ?>>Actif</option>
                                        <option value="inactif" <?php echo $user['statut'] === 'inactif' ? 'selected' : ''; ?>>Inactif</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger" 
                                       style="padding: 0.5rem 1rem; font-size: 0.9rem;"
                                       onclick="return confirm('Êtes-vous sûr ?');">Supprimer</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                Aucun utilisateur enregistré.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de création -->
<div id="createModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div class="card" style="max-width: 500px; margin: 2rem; position: relative;">
        <button onclick="document.getElementById('createModal').style.display='none'" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h2>Créer un utilisateur</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe *</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="role">Rôle *</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="client">Client</option>
                    <option value="vendeur">Vendeur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">Créer</button>
                <button type="button" onclick="document.getElementById('createModal').style.display='none'" class="btn btn-secondary">Annuler</button>
            </div>
        </form>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/admin_footer.php';
?>

