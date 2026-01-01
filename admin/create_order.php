<?php
// Script pour créer une commande manuellement (depuis WhatsApp ou autre)
require_once '../config/config.php';
require_once '../config/database.php';
requireRole(ROLE_ADMIN);

$conn = getDBConnection();
$message = '';
$message_type = '';

// Traitement de l'enregistrement d'un nouveau client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_client') {
    $nom = sanitize($_POST['nom'] ?? '');
    $telephone = sanitize($_POST['telephone'] ?? '');
    
    if (!empty($nom) && !empty($telephone)) {
        // Générer automatiquement les autres champs
        $prenom = ''; // Prénom vide
        $email = 'client_' . time() . '@diexo.local'; // Email généré automatiquement
        $password = password_hash('client123', PASSWORD_DEFAULT); // Mot de passe temporaire
        
        // Vérifier si le téléphone existe déjà
        $check_phone = $conn->prepare("SELECT id FROM users WHERE telephone = ? AND role = 'client'");
        $check_phone->bind_param("s", $telephone);
        $check_phone->execute();
        $existing = $check_phone->get_result()->fetch_assoc();
        $check_phone->close();
        
        if ($existing) {
            $message = 'Un client avec ce numéro existe déjà.';
            $message_type = 'danger';
        } else {
            // Créer le client avec seulement nom et téléphone
            $insert_client = $conn->prepare("INSERT INTO users (nom, prenom, email, telephone, password, role, statut) VALUES (?, ?, ?, ?, ?, 'client', 'actif')");
            $insert_client->bind_param("sssss", $nom, $prenom, $email, $telephone, $password);
            
            if ($insert_client->execute()) {
                $new_client_id = $conn->insert_id;
                $message = 'Client enregistré avec succès. ID: ' . $new_client_id;
                $message_type = 'success';
                
                // Rediriger pour recharger la page avec le nouveau client sélectionné
                header('Location: create_order.php?client_id=' . $new_client_id);
                exit();
            } else {
                $message = 'Erreur lors de l\'enregistrement du client.';
                $message_type = 'danger';
            }
            $insert_client->close();
        }
    } else {
        $message = 'Veuillez remplir le nom et le numéro de téléphone.';
        $message_type = 'danger';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_order') {
    $client_id = (int)$_POST['client_id'];
    $products = json_decode($_POST['products'], true);
    $notes = sanitize($_POST['notes'] ?? '');
    $type_vente = sanitize($_POST['type_vente'] ?? 'cash'); // 'cash' ou 'credit'
    $mode_paiement = sanitize($_POST['mode_paiement'] ?? 'espece');
    
    if (!empty($products) && is_array($products)) {
        $conn->begin_transaction();
        
        try {
            // Calculer le total
            $total = 0;
            $order_items = [];
            
            foreach ($products as $product_data) {
                $product_id = (int)$product_data['id'];
                $quantite = (int)$product_data['quantity'];
                
                // Récupérer le produit
                $product_query = $conn->prepare("SELECT * FROM products WHERE id = ? AND statut = 'actif'");
                $product_query->bind_param("i", $product_id);
                $product_query->execute();
                $product = $product_query->get_result()->fetch_assoc();
                
                if ($product && $product['stock'] >= $quantite) {
                    $prix_unitaire = $product['prix_vente'];
                    $prix_total = $prix_unitaire * $quantite;
                    $benefice = ($prix_unitaire - $product['prix_achat']) * $quantite;
                    
                    $total += $prix_total;
                    
                    $order_items[] = [
                        'product_id' => $product_id,
                        'quantite' => $quantite,
                        'prix_unitaire' => $prix_unitaire,
                        'prix_total' => $prix_total,
                        'benefice' => $benefice
                    ];
                }
            }
            
            if (empty($order_items)) {
                throw new Exception('Aucun produit valide dans la commande.');
            }
            
            // Générer un numéro de commande unique
            $numero_commande = 'CMD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            
            // Créer la commande
            $order_stmt = $conn->prepare("INSERT INTO orders (client_id, numero_commande, total, mode_paiement, adresse_livraison, telephone_livraison, notes, statut) VALUES (?, ?, ?, ?, NULL, NULL, ?, 'en_attente')");
            $order_stmt->bind_param("isdss", $client_id, $numero_commande, $total, $mode_paiement, $notes);
            $order_stmt->execute();
            $order_id = $conn->insert_id;
            $order_stmt->close();
            
            // Créer les items de commande et mettre à jour le stock
            foreach ($order_items as $item) {
                $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantite, prix_unitaire, prix_total, benefice) VALUES (?, ?, ?, ?, ?, ?)");
                $item_stmt->bind_param("iiiddd", $order_id, $item['product_id'], $item['quantite'], $item['prix_unitaire'], $item['prix_total'], $item['benefice']);
                $item_stmt->execute();
                $item_stmt->close();
                
                // Mettre à jour le stock
                $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update_stock->bind_param("ii", $item['quantite'], $item['product_id']);
                $update_stock->execute();
                $update_stock->close();
            }
            
            // Gérer le paiement selon le type de vente
            if ($type_vente === 'credit') {
                // Créer une dette (crédit) - ne pas ajouter l'argent à la caisse
                $debt_stmt = $conn->prepare("INSERT INTO client_debts (client_id, order_id, montant_total, montant_restant, statut) VALUES (?, ?, ?, ?, 'en_cours')");
                $debt_stmt->bind_param("iidd", $client_id, $order_id, $total, $total);
                $debt_stmt->execute();
                $debt_stmt->close();
            } else {
                // Vente cash - enregistrer le paiement immédiatement (ajouter à la caisse)
                $payment_description = "Vente cash - Commande #" . $numero_commande;
                $payment_stmt = $conn->prepare("INSERT INTO payments (order_id, client_id, montant, type_paiement, mode_paiement, description, statut) VALUES (?, ?, ?, 'entree', ?, ?, 'valide')");
                $payment_stmt->bind_param("iidss", $order_id, $client_id, $total, $mode_paiement, $payment_description);
                $payment_stmt->execute();
                $payment_stmt->close();
            }
            
            $conn->commit();
            $message = 'Commande créée avec succès. Numéro: ' . $numero_commande;
            $message_type = 'success';
            
        } catch (Exception $e) {
            $conn->rollback();
            $message = 'Erreur: ' . $e->getMessage();
            $message_type = 'danger';
        }
    } else {
        $message = 'Veuillez sélectionner au moins un produit.';
        $message_type = 'danger';
    }
}

// Récupérer les clients
$clients = $conn->query("SELECT * FROM users WHERE role = 'client' ORDER BY nom, prenom");

// Récupérer l'ID du client sélectionné depuis l'URL
$selected_client_id = isset($_GET['client_id']) ? (int)$_GET['client_id'] : 0;

// Récupérer les produits actifs
$products_list = $conn->query("SELECT * FROM products WHERE statut = 'actif' AND stock > 0 ORDER BY nom");

$page_title = "Créer une Commande";
require_once 'includes/admin_header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>" style="margin-bottom: 1.5rem;">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="content-card">
        <form method="POST" id="orderForm">
            <input type="hidden" name="action" value="create_order">
            
            <div class="form-group">
                <label for="client_id">Client *</label>
                <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                    <select id="client_id" name="client_id" class="form-control" required style="flex: 1;">
                        <option value="">Sélectionner un client</option>
                        <?php 
                        // Réinitialiser le pointeur du résultat
                        $clients->data_seek(0);
                        while ($client = $clients->fetch_assoc()): 
                        ?>
                            <option value="<?php echo $client['id']; ?>" <?php echo ($selected_client_id == $client['id']) ? 'selected' : ''; ?>>
                                <?php 
                                $display = $client['nom'] . ' ' . $client['prenom'];
                                if ($client['telephone']) {
                                    $display .= ' (' . $client['telephone'] . ')';
                                }
                                echo htmlspecialchars($display); 
                                ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="button" onclick="document.getElementById('newClientModal').style.display='flex'" class="btn btn-success" style="white-space: nowrap;">
                        <i class="fas fa-user-plus"></i> Nouveau client
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label>Produits *</label>
                <div id="productsList" style="border: 1px solid var(--border-color); padding: 1rem; border-radius: 5px; max-height: 500px; overflow-y: auto;">
                    <?php while ($product = $products_list->fetch_assoc()): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 1px solid var(--border-color); gap: 1rem; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor=''">
                            <div style="flex-shrink: 0;">
                                <?php if ($product['image_principale']): ?>
                                    <img src="<?php echo UPLOAD_URL . $product['image_principale']; ?>" 
                                         alt="<?php echo htmlspecialchars($product['nom']); ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid var(--border-color);"
                                         onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder.jpg'; this.onerror=null;">
                                <?php else: ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/images/placeholder.jpg" 
                                         alt="Image non disponible" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; border: 1px solid var(--border-color);">
                                <?php endif; ?>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <strong style="display: block; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($product['nom']); ?></strong>
                                <small style="color: #7f8c8d; display: block;">
                                    Stock: <?php echo $product['stock']; ?> | Prix: <?php echo formatPriceDual($product['prix_vente']); ?>
                                </small>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem; flex-shrink: 0;">
                                <label style="font-size: 0.9rem; color: #7f8c8d; white-space: nowrap;">Qté:</label>
                                <input type="number" 
                                       class="product-quantity" 
                                       data-product-id="<?php echo $product['id']; ?>"
                                       data-price="<?php echo $product['prix_vente']; ?>"
                                       min="0" 
                                       max="<?php echo $product['stock']; ?>" 
                                       value="0" 
                                       style="width: 80px; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="type_vente">Type de vente *</label>
                <select id="type_vente" name="type_vente" class="form-control" required>
                    <option value="cash">Cash (Paiement immédiat)</option>
                    <option value="credit">Crédit (Dette)</option>
                </select>
                <small class="form-text text-muted">En crédit, l'argent sera ajouté à la caisse uniquement après paiement de la dette.</small>
            </div>
            
            <div class="form-group" id="mode_paiement_group">
                <label for="mode_paiement">Mode de paiement *</label>
                <select id="mode_paiement" name="mode_paiement" class="form-control" required>
                    <option value="espece">Espèce</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="carte">Carte</option>
                    <option value="virement">Virement</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" class="form-control" rows="3"></textarea>
            </div>
            
            <div id="totalDisplay" style="font-size: 1.5rem; font-weight: bold; color: var(--accent-color); margin: 1rem 0; text-align: right;">
                Total: $0.00 (0 CDF)
            </div>
            
            <input type="hidden" name="products" id="productsInput">
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Créer la commande</button>
                <a href="orders.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Taux de change USD vers CDF (depuis PHP)
    const USD_TO_CDF_RATE = <?php echo USD_TO_CDF_RATE; ?>;
    
    const quantities = document.querySelectorAll('.product-quantity');
    const productsInput = document.getElementById('productsInput');
    const totalDisplay = document.getElementById('totalDisplay');
    const form = document.getElementById('orderForm');
    
    function updateTotal() {
        let total = 0;
        const selectedProducts = [];
        
        quantities.forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const productId = input.dataset.productId;
                const price = parseFloat(input.dataset.price);
                total += price * qty;
                
                selectedProducts.push({
                    id: productId,
                    quantity: qty
                });
            }
        });
        
        // Afficher en USD et CDF
        const totalCdf = total * USD_TO_CDF_RATE; // Taux de change actuel
        totalDisplay.textContent = 'Total: $' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 
                                   ' (' + totalCdf.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' CDF)';
        productsInput.value = JSON.stringify(selectedProducts);
    }
    
    // Gérer l'affichage du mode de paiement selon le type de vente
    const typeVente = document.getElementById('type_vente');
    const modePaiementGroup = document.getElementById('mode_paiement_group');
    
    typeVente.addEventListener('change', function() {
        if (this.value === 'credit') {
            modePaiementGroup.style.display = 'none';
            document.getElementById('mode_paiement').removeAttribute('required');
        } else {
            modePaiementGroup.style.display = 'block';
            document.getElementById('mode_paiement').setAttribute('required', 'required');
        }
    });
    
    // Initialiser l'état au chargement
    if (typeVente.value === 'credit') {
        modePaiementGroup.style.display = 'none';
        document.getElementById('mode_paiement').removeAttribute('required');
    }
    
    quantities.forEach(input => {
        input.addEventListener('change', updateTotal);
    });
    
    form.addEventListener('submit', function(e) {
        const products = JSON.parse(productsInput.value);
        if (products.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un produit.');
            return false;
        }
    });
    
    updateTotal();
});
</script>

<!-- Modal pour enregistrer un nouveau client -->
<div id="newClientModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div class="content-card" style="max-width: 500px; margin: 2rem; position: relative;">
        <button onclick="document.getElementById('newClientModal').style.display='none'" style="position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #2c3e50;">&times;</button>
        <div class="content-card-header">
            <h2><i class="fas fa-user-plus"></i> Enregistrer un nouveau client</h2>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="create_client">
            
            <div class="form-group">
                <label for="client_nom">Nom *</label>
                <input type="text" id="client_nom" name="nom" class="form-control" required placeholder="Nom du client" autofocus>
            </div>
            
            <div class="form-group">
                <label for="client_telephone">Numéro de téléphone *</label>
                <input type="tel" id="client_telephone" name="telephone" class="form-control" required placeholder="Ex: +237 6XX XXX XXX">
                <small class="form-text text-muted">Ce numéro sera utilisé pour vérifier les dettes et pour la facture.</small>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-success">Enregistrer le client</button>
                <button type="button" onclick="document.getElementById('newClientModal').style.display='none'" class="btn btn-secondary">Annuler</button>
            </div>
        </form>
    </div>
</div>

<?php
$conn->close();
require_once 'includes/admin_footer.php';
?>

