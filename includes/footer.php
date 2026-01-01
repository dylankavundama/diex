    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p>Votre boutique en ligne pour vêtements, articles ménagers et décoration intérieure.</p>
                </div>
                <div class="footer-section">
                    <h4>Liens Rapides</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/index.php">Accueil</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php">Boutique</a></li>
                        <li><a href="<?php echo SITE_URL; ?>./admin/dashboard.php">Admin</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Catégories</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php?categorie=1">Vêtements</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php?categorie=2">Articles Ménagers</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/shop.php?categorie=3">Décoration</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p><i class="fas fa-phone"></i> +221 XX XXX XX XX</p>
                    <p><i class="fas fa-envelope"></i> contact@diexo.com</p>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" class="whatsapp-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>

