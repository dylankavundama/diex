<?php
$page_title = "Rapports et Statistiques";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_ADMIN);

$conn = getDBConnection();

// Filtres
$report_type = isset($_GET['type']) ? sanitize($_GET['type']) : 'journalier';
$date = isset($_GET['date']) ? sanitize($_GET['date']) : date('Y-m-d');
$month = isset($_GET['month']) ? sanitize($_GET['month']) : date('Y-m');
$year = isset($_GET['year']) ? sanitize($_GET['year']) : date('Y');

$report_data = [];

if ($report_type === 'journalier') {
    // Rapport journalier
    $result = $conn->query("SELECT 
        COUNT(DISTINCT o.id) as nombre_ventes,
        SUM(o.total) as recettes,
        SUM(oi.benefice) as benefice
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE DATE(o.created_at) = '$date' AND o.statut != 'annulee'");
    $report_data = $result->fetch_assoc();
    
    // Dépenses du jour
    $result = $conn->query("SELECT SUM(montant) as depenses FROM payments 
                            WHERE type_paiement = 'sortie' 
                            AND DATE(created_at) = '$date' 
                            AND statut = 'valide'");
    $expenses = $result->fetch_assoc();
    $report_data['depenses'] = $expenses['depenses'] ?? 0;
    
} elseif ($report_type === 'mensuel') {
    // Rapport mensuel
    $result = $conn->query("SELECT 
        COUNT(DISTINCT o.id) as nombre_ventes,
        SUM(o.total) as recettes,
        SUM(oi.benefice) as benefice
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE MONTH(o.created_at) = MONTH('$month-01') 
        AND YEAR(o.created_at) = YEAR('$month-01') 
        AND o.statut != 'annulee'");
    $report_data = $result->fetch_assoc();
    
    // Dépenses du mois
    $result = $conn->query("SELECT SUM(montant) as depenses FROM payments 
                            WHERE type_paiement = 'sortie' 
                            AND MONTH(created_at) = MONTH('$month-01') 
                            AND YEAR(created_at) = YEAR('$month-01') 
                            AND statut = 'valide'");
    $expenses = $result->fetch_assoc();
    $report_data['depenses'] = $expenses['depenses'] ?? 0;
    
} elseif ($report_type === 'annuel') {
    // Rapport annuel
    $result = $conn->query("SELECT 
        COUNT(DISTINCT o.id) as nombre_ventes,
        SUM(o.total) as recettes,
        SUM(oi.benefice) as benefice
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        WHERE YEAR(o.created_at) = $year 
        AND o.statut != 'annulee'");
    $report_data = $result->fetch_assoc();
    
    // Dépenses de l'année
    $result = $conn->query("SELECT SUM(montant) as depenses FROM payments 
                            WHERE type_paiement = 'sortie' 
                            AND YEAR(created_at) = $year 
                            AND statut = 'valide'");
    $expenses = $result->fetch_assoc();
    $report_data['depenses'] = $expenses['depenses'] ?? 0;
}

// Statistiques de visite
$visit_stats = [];
$result = $conn->query("SELECT COUNT(*) as total, DATE(created_at) as date_visite 
                        FROM site_statistics 
                        WHERE DATE(created_at) >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
                        GROUP BY DATE(created_at) 
                        ORDER BY date_visite DESC 
                        LIMIT 30");

require_once 'includes/admin_header.php';
?>

<div class="content-card" style="margin-bottom: 1.5rem;">
    <div class="content-card-header">
        <h2><i class="fas fa-filter"></i> Filtres</h2>
    </div>
        <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: end;">
            <div class="form-group">
                <label>Type de rapport</label>
                <select name="type" class="form-control" onchange="this.form.submit()">
                    <option value="journalier" <?php echo $report_type === 'journalier' ? 'selected' : ''; ?>>Journalier</option>
                    <option value="mensuel" <?php echo $report_type === 'mensuel' ? 'selected' : ''; ?>>Mensuel</option>
                    <option value="annuel" <?php echo $report_type === 'annuel' ? 'selected' : ''; ?>>Annuel</option>
                </select>
            </div>
            
            <?php if ($report_type === 'journalier'): ?>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo $date; ?>" onchange="this.form.submit()">
                </div>
            <?php elseif ($report_type === 'mensuel'): ?>
                <div class="form-group">
                    <label>Mois</label>
                    <input type="month" name="month" class="form-control" value="<?php echo $month; ?>" onchange="this.form.submit()">
                </div>
            <?php elseif ($report_type === 'annuel'): ?>
                <div class="form-group">
                    <label>Année</label>
                    <input type="number" name="year" class="form-control" value="<?php echo $year; ?>" min="2020" max="<?php echo date('Y'); ?>" onchange="this.form.submit()">
                </div>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card success">
            <h3>Recettes</h3>
            <div class="stat-value"><?php echo formatPrice($report_data['recettes'] ?? 0); ?></div>
        </div>
        
        <div class="stat-card danger">
            <h3>Dépenses</h3>
            <div class="stat-value"><?php echo formatPrice($report_data['depenses'] ?? 0); ?></div>
        </div>
        
        <div class="stat-card success">
            <h3>Bénéfice</h3>
            <div class="stat-value"><?php echo formatPrice(($report_data['benefice'] ?? 0) - ($report_data['depenses'] ?? 0)); ?></div>
        </div>
        
        <div class="stat-card">
            <h3>Nombre de ventes</h3>
            <div class="stat-value"><?php echo $report_data['nombre_ventes'] ?? 0; ?></div>
        </div>
    </div>
    
    <div class="content-card" style="margin-top: 1.5rem;">
        <div class="card-header">
            <h2>Statistiques de visite (30 derniers jours)</h2>
        </div>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Nombre de visites</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($visit = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($visit['date_visite'])); ?></td>
                            <td><?php echo $visit['total']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                                Aucune visite enregistrée pour cette période.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/admin_footer.php';
?>

