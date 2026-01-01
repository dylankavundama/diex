<?php
$page_title = "Inscription";
require_once '../config/config.php';
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = sanitize($_POST['nom'] ?? '');
    $prenom = sanitize($_POST['prenom'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $telephone = sanitize($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        $conn = getDBConnection();
        
        // Vérifier si l'email existe déjà
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Cet email est déjà utilisé.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'client'; // Par défaut, les nouveaux utilisateurs sont des clients
            
            $stmt = $conn->prepare("INSERT INTO users (nom, prenom, email, telephone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $nom, $prenom, $email, $telephone, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                // Optionnel: connecter automatiquement l'utilisateur
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = $role;
                
                header('Location: ' . SITE_URL . '/index.php');
                exit();
            } else {
                $error = 'Une erreur est survenue lors de l\'inscription.';
            }
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
            <h2>Créer un compte</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" class="form-control" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" class="form-control" required value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" class="form-control" value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe *</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe *</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control" required minlength="6">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">S'inscrire</button>
        </form>
        
        <p style="text-align: center; margin-top: 1.5rem;">
            Déjà un compte ? <a href="<?php echo SITE_URL; ?>/auth/login.php">Se connecter</a>
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

