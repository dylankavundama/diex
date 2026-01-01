<?php
$page_title = "Connexion";
require_once '../config/config.php';
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = sanitize($_POST['login'] ?? ''); // Peut être email ou nom
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $conn = getDBConnection();
        // Rechercher par email OU par nom (pour les admins)
        $stmt = $conn->prepare("SELECT id, nom, prenom, email, password, role, statut FROM users WHERE email = ? OR nom = ?");
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($user['statut'] === 'inactif') {
                $error = 'Votre compte est désactivé. Contactez l\'administrateur.';
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header('Location: ' . SITE_URL . '/admin/dashboard.php');
                } elseif ($user['role'] === 'vendeur') {
                    header('Location: ' . SITE_URL . '/vendeur/dashboard.php');
                } else {
                    header('Location: ' . SITE_URL . '/index.php');
                }
                exit();
            } else {
                $error = 'Identifiant ou mot de passe incorrect.';
            }
        } else {
            $error = 'Identifiant ou mot de passe incorrect.';
        }
        
        $stmt->close();
        $conn->close();
    }
}

require_once '../includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 4rem auto;">
    <div class="card">
        <div class="card-header">
            <h2>Connexion</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="login">Nom d'utilisateur ou Email</label>
                <input type="text" id="login" name="login" class="form-control" required 
                       placeholder="Nom d'utilisateur (admin) ou Email" 
                       value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
                <small style="color: #666; font-size: 0.85rem;">
                    <i class="fas fa-info-circle"></i> Pour les administrateurs: utilisez votre nom d'utilisateur ou votre email
                </small>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            Pas encore de compte ? <a href="<?php echo SITE_URL; ?>/auth/register.php">Créer un compte</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

