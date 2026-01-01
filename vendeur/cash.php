<?php
$page_title = "Gestion de la Caisse";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_VENDEUR);

$conn = getDBConnection();
$message = '';
$message_type = '';
$user_id = $_SESSION['user_id'];

// Traitement de l'enregistrement de dépense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_expense') {
    $montant = (float)$_POST['montant'];
    $mode_paiement = sanitize($_POST['mode_paiement']);
    $motif = sanitize($_POST['motif'] ?? '');
    $date_expense = sanitize($_POST['date_expense'] ?? date('Y-m-d'));
    $user_id = $_SESSION['user_id']; // Utilisateur qui fait la sortie
    
    if ($montant > 0 && !empty($mode_paiement) && !empty($motif)) {
        // Utiliser client_id pour stocker l'ID de l'utilisateur qui fait la sortie
        $insert_expense = $conn->prepare("INSERT INTO payments (order_id, client_id, montant, type_paiement, mode_paiement, description, statut, created_at) VALUES (NULL, ?, ?, 'sortie', ?, ?, 'valide', ?)");
        $date_time = $date_expense . ' ' . date('H:i:s');
        $insert_expense->bind_param("idsss", $user_id, $montant, $mode_paiement, $motif, $date_time);
        
        if ($insert_expense->execute()) {
            $message = 'Sortie de caisse enregistrée avec succès.';
            $message_type = 'success';
        } else {
            $message = 'Erreur lors de l\'enregistrement.';
            $message_type = 'danger';
        }
        $insert_expense->close();
    } else {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $message_type = 'danger';
    }
}

// Statistiques financières (sans recettes/bénéfice)
$stats = [];

// Dépenses du mois
$result = $conn->query("SELECT SUM(montant) as total FROM payments 
                        WHERE type_paiement = 'sortie' 
                        AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                        AND YEAR(created_at) = YEAR(CURRENT_DATE()) 
                        AND statut = 'valide'");
$stats['monthly_expenses'] = $result->fetch_assoc()['total'] ?? 0;

// Total des dépenses
$result = $conn->query("SELECT COALESCE(SUM(montant), 0) as total_expenses 
                        FROM payments 
                        WHERE type_paiement = 'sortie' 
                        AND statut = 'valide'");
$stats['total_expenses'] = $result->fetch_assoc()['total'] ?? 0;

// Solde de caisse (calculé sans afficher les recettes)
// Le vendeur a accès à toutes les ventes, donc on calcule sur toutes les commandes
$result = $conn->query("SELECT 
    (SELECT COALESCE(SUM(montant), 0) FROM payments WHERE type_paiement = 'entree' AND statut = 'valide') as total_income,
    (SELECT COALESCE(SUM(montant), 0) FROM payments WHERE type_paiement = 'sortie' AND statut = 'valide') as total_expenses");
$cash_data = $result->fetch_assoc();
$stats['cash_balance'] = ($cash_data['total_income'] ?? 0) - ($cash_data['total_expenses'] ?? 0);

// Paiements récents (entrées et sorties) - Pour les sorties, client_id contient l'ID de l'utilisateur qui a fait la sortie
$recent_payments = $conn->query("SELECT p.*, 
                                 CASE 
                                     WHEN p.type_paiement = 'sortie' THEN u_sortie.nom
                                     ELSE u_client.nom 
                                 END as nom,
                                 CASE 
                                     WHEN p.type_paiement = 'sortie' THEN u_sortie.prenom
                                     ELSE u_client.prenom 
                                 END as prenom
                                 FROM payments p 
                                 LEFT JOIN users u_client ON p.client_id = u_client.id AND p.type_paiement = 'entree'
                                 LEFT JOIN users u_sortie ON p.client_id = u_sortie.id AND p.type_paiement = 'sortie'
                                 ORDER BY p.created_at DESC 
                                 LIMIT 30");

require_once 'includes/vendeur_header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 1.5rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card-modern danger">
        <div class="stat-icon">
            <i class="fas fa-arrow-down"></i>
        </div>
        <h3>Dépenses (Mois)</h3>
        <div class="stat-value"><?php echo formatPrice($stats['monthly_expenses']); ?></div>
        <div class="stat-change">Total: <?php echo formatPrice($stats['total_expenses']); ?></div>
    </div>
    
    <div class="stat-card-modern <?php echo $stats['cash_balance'] >= 0 ? 'success' : 'danger'; ?>">
        <div class="stat-icon">
            <i class="fas fa-wallet"></i>
        </div>
        <h3>Solde de Caisse</h3>
        <div class="stat-value"><?php echo formatPrice($stats['cash_balance']); ?></div>
        <div class="stat-change">Total disponible</div>
    </div>
</div>

    <div class="page-actions" style="margin-bottom: 1.5rem;">
        <button onclick="document.getElementById('expenseModal').style.display='flex'" class="btn btn-danger">
            <i class="fas fa-minus-circle"></i> Sortie de caisse
        </button>
    </div>

<div class="content-card">
    <div class="content-card-header">
        <h2><i class="fas fa-money-bill-wave"></i> Mouvements de Caisse</h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                        <tr>
                            <th>Date</th>
                            <th>Client/Utilisateur</th>
                            <th>Montant</th>
                            <th>Type</th>
                            <th>Mode</th>
                            <th>Motif</th>
                        </tr>
            </thead>
            <tbody>
                <?php if ($recent_payments->num_rows > 0): ?>
                    <?php while ($payment = $recent_payments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($payment['created_at'])); ?></td>
                            <td>
                                <?php if ($payment['type_paiement'] === 'sortie'): ?>
                                    <span style="color: #7f8c8d; font-size: 0.85rem;">
                                        <i class="fas fa-user"></i> <?php echo $payment['nom'] ? htmlspecialchars($payment['nom'] . ' ' . $payment['prenom']) : 'Utilisateur inconnu'; ?>
                                    </span>
                                <?php else: ?>
                                    <?php echo $payment['nom'] ? htmlspecialchars($payment['nom'] . ' ' . $payment['prenom']) : '-'; ?>
                                <?php endif; ?>
                            </td>
                            <td style="color: <?php echo $payment['type_paiement'] === 'entree' ? '#27ae60' : '#e74c3c'; ?>; font-weight: bold;">
                                <?php echo $payment['type_paiement'] === 'entree' ? '+' : '-'; ?><?php echo formatPrice($payment['montant']); ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $payment['type_paiement'] === 'entree' ? 'success' : 'danger'; ?>">
                                    <?php echo ucfirst($payment['type_paiement']); ?>
                                </span>
                            </td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment['mode_paiement'])); ?></td>
                            <td><?php echo htmlspecialchars($payment['description'] ?? '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                            Aucun mouvement enregistré
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal d'enregistrement de dépense -->
<div id="expenseModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div class="content-card" style="max-width: 500px; margin: 2rem; position: relative;">
        <button onclick="document.getElementById('expenseModal').style.display='none'" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #2c3e50;">&times;</button>
        <div class="content-card-header">
            <h2><i class="fas fa-minus-circle"></i> Sortie de Caisse</h2>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_expense">
            
            <div class="form-group">
                <label for="expense_montant">Montant (USD) *</label>
                <input type="number" id="expense_montant" name="montant" class="form-control" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="expense_motif">Motif *</label>
                <textarea id="expense_motif" name="motif" class="form-control" rows="3" placeholder="Ex: Achat de matériel, Frais de transport, Paiement fournisseur, etc." required></textarea>
            </div>
            
            <div class="form-group">
                <label for="expense_date">Date *</label>
                <input type="date" id="expense_date" name="date_expense" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="expense_mode_paiement">Mode de paiement *</label>
                <select id="expense_mode_paiement" name="mode_paiement" class="form-control" required>
                    <option value="espece">Espèce</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="carte">Carte</option>
                    <option value="virement">Virement</option>
                </select>
            </div>
            
            <div class="form-group" style="padding: 0.75rem; background: #f8f9fa; border-radius: 5px;">
                <label style="font-weight: 600;">Utilisateur</label>
                <p style="margin: 0.5rem 0 0 0; color: #2c3e50;">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_nom'] . ' ' . $_SESSION['user_prenom']); ?>
                </p>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-danger">Enregistrer la sortie</button>
                <button type="button" onclick="document.getElementById('expenseModal').style.display='none'" class="btn btn-secondary">Annuler</button>
            </div>
        </form>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/vendeur_footer.php';
?>

