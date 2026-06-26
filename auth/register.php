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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title . ' - ' . SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Video Background */
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -2;
        }

        .video-background video {
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        /* Fallback gradient if no video */
        .video-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            z-index: -1;
        }

        /* Dark overlay */
        .video-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        /* Animated particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 15s infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }

        /* Register Container */
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        /* Glassmorphism Card */
        .register-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem 2rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
            overflow: hidden;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes shine {
            0%, 100% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }
            50% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        /* Header */
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .register-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: pulse 2s infinite;
        }

        .register-logo i {
            font-size: 1.8rem;
            color: white;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .register-header h1 {
            color: white;
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .register-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        /* Form */
        .register-form {
            position: relative;
            z-index: 1;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            color: white;
            font-weight: 500;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.8rem;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: white;
            font-size: 0.9rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }

        .form-input:focus + .input-icon {
            color: white;
        }

        /* Toggle Password */
        .password-toggle {
            position: absolute;
            right: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: white;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            margin-top: 0.8rem;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Alert */
        .alert {
            padding: 0.85rem 1rem;
            border-radius: 12px;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            animation: slideDown 0.5s ease;
            backdrop-filter: blur(10px);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #fff;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.2);
            border: 1px solid rgba(34, 197, 94, 0.5);
            color: #fff;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Footer Links */
        .register-footer {
            text-align: center;
            margin-top: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .register-footer p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }

        .register-footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .register-footer a:hover {
            border-bottom-color: white;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .register-card {
                padding: 2rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .register-header h1 {
                font-size: 1.4rem;
            }
        }

        /* Loading animation */
        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-submit.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .required {
            color: #f093fb;
        }

        /* Password Strength Meter */
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: linear-gradient(90deg, #ff6b6b, #ee5a6f);
        }

        .password-strength-bar.medium {
            width: 66%;
            background: linear-gradient(90deg, #f093fb, #f5576c);
        }

        .password-strength-bar.strong {
            width: 100%;
            background: linear-gradient(90deg, #11998e, #38ef7d);
        }

        .password-strength-text {
            margin-top: 0.3rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Validation Indicators */
        .validation-indicator {
            position: absolute;
            right: 3.5rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .validation-indicator.show {
            opacity: 1;
        }

        .validation-indicator.valid {
            color: #38ef7d;
        }

        .validation-indicator.invalid {
            color: #ff6b6b;
        }

        /* Enhanced form input states */
        .form-input.valid {
            border-color: rgba(56, 239, 125, 0.5);
        }

        .form-input.invalid {
            border-color: rgba(255, 107, 107, 0.5);
        }

        /* Social Login Buttons */
        .social-login {
            margin-top: 1.5rem;
            text-align: center;
        }

        .social-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .social-divider::before,
        .social-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .social-divider span {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
        }

        .social-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-social {
            padding: 0.75rem;
            border-radius: 10px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-social:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
        }

        .btn-social i {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Video Background -->
    <div class="video-background">
        <video autoplay muted loop playsinline>
            <source src="<?php echo SITE_URL; ?>/assets/vd.mp4" type="video/mp4">
        </video>
    </div>
    
    <!-- Dark Overlay -->
    <div class="video-overlay"></div>

    <!-- Animated Particles -->
    <div class="particles">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 4s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 1s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 3s;"></div>
        <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 2.5s;"></div>
        <div class="particle" style="left: 80%; animation-delay: 4.5s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 1.5s;"></div>
    </div>

    <!-- Register Container -->
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Créer un compte</h1>
                <p>Rejoignez-nous dès aujourd'hui</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $success; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="register-form" id="registerForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">
                            <i class="fas fa-user"></i> Nom <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="input-icon fas fa-user"></i>
                            <input type="text" 
                                   id="nom" 
                                   name="nom" 
                                   class="form-input" 
                                   required 
                                   placeholder="Votre nom"
                                   value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="prenom">
                            <i class="fas fa-user"></i> Prénom <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <i class="input-icon fas fa-user"></i>
                            <input type="text" 
                                   id="prenom" 
                                   name="prenom" 
                                   class="form-input" 
                                   required 
                                   placeholder="Votre prénom"
                                   value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-envelope"></i>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-input" 
                               required 
                               placeholder="votre.email@exemple.com"
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        <i class="validation-indicator fas fa-check-circle" id="emailIndicator"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telephone">
                        <i class="fas fa-phone"></i> Téléphone
                    </label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-phone"></i>
                        <input type="tel" 
                               id="telephone" 
                               name="telephone" 
                               class="form-input" 
                               placeholder="+225 XX XX XX XX"
                               value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Mot de passe <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-key"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input" 
                               required
                               minlength="6"
                               placeholder="Minimum 6 caractères">
                        <i class="password-toggle fas fa-eye" id="togglePassword"></i>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-strength-text" id="strengthText"></div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">
                        <i class="fas fa-lock"></i> Confirmer le mot de passe <span class="required">*</span>
                    </label>
                    <div class="input-wrapper">
                        <i class="input-icon fas fa-key"></i>
                        <input type="password" 
                               id="password_confirm" 
                               name="password_confirm" 
                               class="form-input" 
                               required
                               minlength="6"
                               placeholder="Confirmez votre mot de passe">
                        <i class="password-toggle fas fa-eye" id="togglePasswordConfirm"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>

            <div class="register-footer">
                <p>
                    Déjà un compte ? 
                    <a href="<?php echo SITE_URL; ?>/auth/login.php">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility for password field
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Toggle password visibility for confirm password field
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirmInput = document.getElementById('password_confirm');

        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Password strength checker
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        function checkPasswordStrength(password) {
            let strength = 0;
            let level = '';
            let icon = '';

            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            strengthBar.className = 'password-strength-bar';
            
            if (strength <= 2) {
                strengthBar.classList.add('weak');
                level = 'Faible';
                icon = '<i class="fas fa-exclamation-triangle"></i>';
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
                level = 'Moyen';
                icon = '<i class="fas fa-shield-alt"></i>';
            } else {
                strengthBar.classList.add('strong');
                level = 'Fort';
                icon = '<i class="fas fa-shield-check"></i>';
            }

            strengthText.innerHTML = password.length > 0 ? `${icon} ${level}` : '';
        }

        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        // Email validation
        const emailInput = document.getElementById('email');
        const emailIndicator = document.getElementById('emailIndicator');

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        emailInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                if (validateEmail(this.value)) {
                    emailIndicator.classList.remove('invalid', 'fa-times-circle');
                    emailIndicator.classList.add('valid', 'fa-check-circle', 'show');
                    this.classList.remove('invalid');
                    this.classList.add('valid');
                } else {
                    emailIndicator.classList.remove('valid', 'fa-check-circle');
                    emailIndicator.classList.add('invalid', 'fa-times-circle', 'show');
                    this.classList.remove('valid');
                    this.classList.add('invalid');
                }
            } else {
                emailIndicator.classList.remove('show');
                this.classList.remove('valid', 'invalid');
            }
        });

        // Add loading animation on submit
        const registerForm = document.getElementById('registerForm');
        registerForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('.btn-submit');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = 'Inscription en cours...';
        });

        // Add floating animation to input icons on focus
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                const icon = this.previousElementSibling || this.parentElement.querySelector('.input-icon');
                if (icon) {
                    icon.style.transform = 'translateY(-50%) scale(1.1)';
                }
            });

            input.addEventListener('blur', function() {
                const icon = this.previousElementSibling || this.parentElement.querySelector('.input-icon');
                if (icon) {
                    icon.style.transform = 'translateY(-50%) scale(1)';
                }
            });
        });

        // Password match validation with enhanced feedback
        passwordConfirmInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                if (this.value === passwordInput.value) {
                    this.classList.remove('invalid');
                    this.classList.add('valid');
                } else {
                    this.classList.remove('valid');
                    this.classList.add('invalid');
                }
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });

        // Real-time form validation
        document.querySelectorAll('.form-input[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('invalid');
                } else if (!this.classList.contains('invalid')) {
                    this.classList.add('valid');
                }
            });
        });
    </script>
</body>
</html>
