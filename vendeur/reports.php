<?php
$page_title = "Mes Rapports";
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_VENDEUR);

$conn = getDBConnection();
$vendeur_id = $_SESSION['user_id'];

// Filtres
$report_type = isset($_GET['type']) ? sanitize($_GET['type']) : 'journalier';
$date = isset($_GET['date']) ? sanitize($_GET['date']) : date('Y-m-d');
$month = isset($_GET['month']) ? sanitize($_GET['month']) : date('Y-m');
$year = isset($_GET['year']) ? sanitize($_GET['year']) : date('Y');

$report_data = [];

if ($report_type === 'journalier') {
    // Rapport journalier - toutes les ventes (le vendeur a accès à tous les produits)
    $stmt = $conn->prepare("SELECT 
        COUNT(DISTINCT o.id) as nombre_ventes
        FROM orders o
        WHERE DATE(o.created_at) = ? 
        AND o.statut != 'annulee'");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $report_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
} elseif ($report_type === 'mensuel') {
    // Rapport mensuel - toutes les ventes (le vendeur a accès à tous les produits)
    $stmt = $conn->prepare("SELECT 
        COUNT(DISTINCT o.id) as nombre_ventes
        FROM orders o
        WHERE MONTH(o.created_at) = MONTH(?) 
        AND YEAR(o.created_at) = YEAR(?) 
        AND o.statut != 'annulee'");
    $month_date = $month . '-01';
    $stmt->bind_param("ss", $month_date, $month_date);
    $stmt->execute();
    $report_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
} elseif ($report_type === 'annuel') {
    // Rapport annuel - toutes les ventes (le vendeur a accès à tous les produits)
    $stmt = $conn->prepare("SELECT 
        COUNT(DISTINCT o.id) as nombre_ventes
        FROM orders o
        WHERE YEAR(o.created_at) = ? 
        AND o.statut != 'annulee'");
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $report_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Produits les plus vendus (sans revenu) - tous les produits (le vendeur a accès à tous)
$top_products = $conn->query("SELECT p.nom, SUM(oi.quantite) as total_vendu
                              FROM order_items oi
                              JOIN products p ON oi.product_id = p.id
                              JOIN orders o ON oi.order_id = o.id
                              WHERE o.statut != 'annulee'
                              GROUP BY p.id, p.nom
                              ORDER BY total_vendu DESC
                              LIMIT 10");

require_once 'includes/vendeur_header.php';
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

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card-modern success">
        <div class="stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h3>Nombre de ventes</h3>
        <div class="stat-value"><?php echo $report_data['nombre_ventes'] ?? 0; ?></div>
        <div class="stat-change">Période sélectionnée</div>
    </div>
</div>

<div class="content-card">
    <div class="content-card-header">
        <h2><i class="fas fa-trophy"></i> Produits les plus vendus</h2>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité vendue</th>
                    </tr>
            </thead>
            <tbody>
                    <?php if ($top_products->num_rows > 0): ?>
                        <?php while ($product = $top_products->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($product['nom']); ?></strong></td>
                                <td><strong><?php echo $product['total_vendu']; ?></strong> unités</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem; color: #7f8c8d;">
                                Aucune vente enregistrée
                            </td>
                        </tr>
                    <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/vendeur_footer.php';
?>

