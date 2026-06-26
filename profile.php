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

// Récupérer les statistiques de l'utilisateur
$stats_query = $conn->prepare("
    SELECT 
        COUNT(*) as total_commandes,
        COALESCE(SUM(total), 0) as total_depense,
        COUNT(CASE WHEN statut = 'livree' THEN 1 END) as commandes_livrees
    FROM orders 
    WHERE client_id = ?
");
$stats_query->bind_param("i", $user_id);
$stats_query->execute();
$stats = $stats_query->get_result()->fetch_assoc();

// Récupérer les commandes de l'utilisateur
$orders = $conn->query("SELECT * FROM orders WHERE client_id = $user_id ORDER BY created_at DESC LIMIT 5");
?>

<style>
.profile-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 3rem 0;
}

.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.profile-header {
    text-align: center;
    margin-bottom: 3rem;
    animation: fadeInDown 0.6s ease;
}

.profile-header h1 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.profile-header p {
    color: rgba(255,255,255,0.9);
    font-size: 1.1rem;
}

/* Avatar Section */
.avatar-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 2rem;
}

.avatar-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    margin-bottom: 1rem;
}

.avatar-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
    font-weight: bold;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    border: 4px solid white;
    transition: all 0.3s ease;
}

.avatar-circle:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card-modern {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    animation: fadeInUp 0.6s ease;
}

.stat-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 1rem;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}

.stat-icon.purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.green {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.stat-icon.orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Modern Card with Glassmorphism */
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2.5rem;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    animation: fadeInUp 0.6s ease;
    margin-bottom: 2rem;
}

.glass-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f0f0f0;
}

.glass-card-header i {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
}

.glass-card-header h2 {
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
}

/* Main Grid */
.profile-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

/* Modern Form Inputs */
.modern-form-group {
    margin-bottom: 1.5rem;
}

.modern-form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #2c3e50;
    font-size: 0.95rem;
}

.modern-input {
    width: 100%;
    padding: 0.9rem 1.2rem;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.modern-input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.modern-input:disabled {
    background: #f0f0f0;
    cursor: not-allowed;
    color: #999;
}

.input-hint {
    font-size: 0.85rem;
    color: #999;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* Modern Button */
.btn-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 2.5rem;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
}

.btn-modern:active {
    transform: translateY(0);
}

.btn-logout {
    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    color: white;
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(255, 75, 43, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    margin-top: 1rem;
}

.btn-logout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 75, 43, 0.4);
    color: white;
}

/* Modern Table */
.modern-table-container {
    overflow-x: auto;
    border-radius: 12px;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.modern-table th {
    padding: 1rem;
    text-align: left;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-table th:first-child {
    border-top-left-radius: 12px;
}

.modern-table th:last-child {
    border-top-right-radius: 12px;
}

.modern-table tbody tr {
    transition: all 0.3s ease;
}

.modern-table tbody tr:hover {
    background: #f8f9fa;
}

.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
    color: #2c3e50;
}

/* Modern Badge */
.modern-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
}

.modern-badge.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.modern-badge.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.modern-badge.pending {
    background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
    color: white;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #999;
}

.empty-state i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 1rem;
}

.empty-state p {
    font-size: 1.1rem;
    color: #999;
}

/* Alert Modern */
.alert-modern {
    padding: 1.2rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: slideInDown 0.4s ease;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}

.alert-modern i {
    font-size: 1.5rem;
}

.alert-modern.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-modern.danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header h1 {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .glass-card {
        padding: 1.5rem;
    }
    
    .modern-table {
        font-size: 0.85rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.75rem 0.5rem;
    }
}

@media (min-width: 992px) {
    .profile-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<div class="profile-page">
    <div class="profile-container">
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> Mon Profil</h1>
            <p>Gérez vos informations personnelles et suivez vos commandes</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert-modern <?php echo $message_type === 'success' ? 'success' : 'danger'; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>
        
        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card-modern">
                <div class="stat-icon purple">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-value"><?php echo $stats['total_commandes']; ?></div>
                <div class="stat-label">Commandes</div>
            </div>
            <div class="stat-card-modern">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $stats['commandes_livrees']; ?></div>
                <div class="stat-label">Livrées</div>
            </div>
            <div class="stat-card-modern">
                <div class="stat-icon orange">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-value"><?php echo formatPrice($stats['total_depense']); ?></div>
                <div class="stat-label">Total Dépensé</div>
            </div>
        </div>
        
        <!-- Main Content Grid -->
        <div class="profile-grid">
            <!-- Profile Info Card -->
            <div class="glass-card">
                <div class="avatar-section">
                    <div class="avatar-wrapper">
                        <div class="avatar-circle">
                            <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>
                        </div>
                    </div>
                    <h3 style="color: #2c3e50; margin: 0; font-size: 1.3rem;"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h3>
                    <p style="color: #999; margin: 0.3rem 0 0 0;"><?php echo htmlspecialchars($user['email']); ?></p>
                    <a href="<?php echo SITE_URL; ?>/auth/logout.php" class="btn-logout">
                        <i class="fas fa-power-off"></i>
                        Déconnexion
                    </a>
                </div>
                
                <div class="glass-card-header" style="margin-top: 2rem;">
                    <i class="fas fa-user-edit"></i>
                    <h2>Informations Personnelles</h2>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="modern-form-group">
                        <label for="nom"><i class="fas fa-user"></i> Nom</label>
                        <input type="text" id="nom" name="nom" class="modern-input" required value="<?php echo htmlspecialchars($user['nom']); ?>">
                    </div>
                    
                    <div class="modern-form-group">
                        <label for="prenom"><i class="fas fa-user"></i> Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="modern-input" required value="<?php echo htmlspecialchars($user['prenom']); ?>">
                    </div>
                    
                    <div class="modern-form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" id="email" class="modern-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                        <div class="input-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>L'email ne peut pas être modifié</span>
                        </div>
                    </div>
                    
                    <div class="modern-form-group">
                        <label for="telephone"><i class="fas fa-phone"></i> Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" class="modern-input" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>" placeholder="+225 XX XX XX XX">
                    </div>
                    
                    <button type="submit" class="btn-modern">
                        <i class="fas fa-save"></i>
                        Mettre à jour
                    </button>
                </form>
            </div>
            
            <!-- Orders Card -->
            <div class="glass-card">
                <div class="glass-card-header">
                    <i class="fas fa-box"></i>
                    <h2>Commandes Récentes</h2>
                </div>
                
                <?php if ($orders->num_rows > 0): ?>
                    <div class="modern-table-container">
                        <table class="modern-table">
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
                                        <td><strong><?php echo htmlspecialchars($order['numero_commande']); ?></strong></td>
                                        <td><strong><?php echo formatPrice($order['total']); ?></strong></td>
                                        <td>
                                            <span class="modern-badge <?php 
                                                echo $order['statut'] == 'livree' ? 'success' : 
                                                    ($order['statut'] == 'en_attente' ? 'warning' : 'pending');
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
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Aucune commande pour le moment</p>
                        <a href="<?php echo SITE_URL; ?>/shop.php" class="btn-modern" style="text-decoration: none; margin-top: 1rem;">
                            <i class="fas fa-shopping-bag"></i>
                            Commencer vos achats
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$user_query->close();
$stats_query->close();
$conn->close();
require_once 'includes/footer.php';
?>
