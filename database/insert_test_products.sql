-- Script d'insertion de 18 produits de test avec images
-- Exécutez ce script après avoir créé la base de données
-- Note: Les images doivent être générées avec generate_product_images.php ou uploadées manuellement

-- Vider la table produits (optionnel, pour recommencer)
-- TRUNCATE TABLE products;
-- TRUNCATE TABLE product_images;

-- Produits Vêtements (6 produits)
-- Catégorie: Vêtements Enfants (id = 4)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('T-shirt Enfant Coton Bio', 'T-shirt 100% coton bio pour enfant, confortable et doux. Disponible en plusieurs couleurs et tailles (4-12 ans).', 2500, 4500, 25, 5, 4, 'product_1_main.jpg', 'actif'),
('Robe Fille Fleurie', 'Belle robe fleurie pour petite fille, idéale pour les occasions spéciales. Tissu léger et respirant.', 3500, 6500, 15, 3, 4, 'product_2_main.jpg', 'actif'),
('Pantalon Garçon Jeans', 'Pantalon jean classique pour garçon, résistant et confortable. Taille ajustable.', 4000, 7500, 20, 5, 4, 'product_3_main.jpg', 'actif');

-- Catégorie: Vêtements Adultes (id = 5)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Chemise Homme Élégante', 'Chemise homme en coton de qualité supérieure, parfaite pour le bureau ou les occasions formelles. Plusieurs couleurs disponibles.', 8000, 15000, 30, 5, 5, 'product_4_main.jpg', 'actif'),
('Robe Femme Élégante', 'Robe élégante pour femme, design moderne et confortable. Idéale pour soirées et événements.', 12000, 22000, 18, 4, 5, 'product_5_main.jpg', 'actif'),
('Jeans Femme Taille Haute', 'Jeans femme taille haute, coupe moderne et confortable. Plusieurs tailles disponibles.', 9000, 18000, 22, 5, 5, 'product_6_main.jpg', 'actif');

-- Produits Articles Ménagers (6 produits)
-- Catégorie: Vaisselle (id = 7)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Service de Table 12 Personnes', 'Service de table complet pour 12 personnes en porcelaine de qualité. Inclut assiettes, bols, tasses et soucoupes.', 25000, 45000, 8, 2, 7, 'product_7_main.jpg', 'actif'),
('Verres à Vin Cristal', 'Set de 6 verres à vin en cristal, élégants et durables. Parfaits pour recevoir.', 8000, 15000, 15, 3, 7, 'product_8_main.jpg', 'actif'),
('Couverts Inox 24 Pièces', 'Service de couverts en inox 18/10, 24 pièces. Design moderne et résistant aux taches.', 15000, 28000, 12, 3, 7, 'product_9_main.jpg', 'actif');

-- Catégorie: Électroménagers (id = 8)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Mixeur Blender 500W', 'Mixeur blender puissant 500W, idéal pour smoothies, soupes et préparations. Bol en verre 1.5L.', 18000, 32000, 10, 2, 8, 'product_10_main.jpg', 'actif'),
('Bouilloire Électrique 1.7L', 'Bouilloire électrique rapide, capacité 1.7L. Arrêt automatique et indicateur de niveau.', 12000, 22000, 15, 3, 8, 'product_11_main.jpg', 'actif'),
('Grille-Pain 4 Fentes', 'Grille-pain 4 fentes avec fonction décongélation. Contrôle de brunissement réglable.', 10000, 18000, 12, 3, 8, 'product_12_main.jpg', 'actif');

-- Catégorie: Accessoires Cuisine (id = 9)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Set Ustensiles de Cuisine', 'Set complet d\'ustensiles de cuisine en silicone et inox. 12 pièces essentielles pour votre cuisine.', 6000, 12000, 20, 5, 9, 'product_13_main.jpg', 'actif'),
('Planche à Découper Bambou', 'Planche à découper en bambou écologique, antibactérienne et durable. 3 tailles disponibles.', 3000, 5500, 25, 5, 9, 'product_14_main.jpg', 'actif');

-- Produits Décoration Intérieure (6 produits)
-- Catégorie: Meubles (id = 10)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Table Basse Moderne', 'Table basse moderne en bois massif avec étagère inférieure. Design épuré et élégant. Dimensions: 120x60x45cm.', 45000, 85000, 5, 1, 10, 'product_15_main.jpg', 'actif'),
('Chaise Design Scandinave', 'Chaise design style scandinave, confortable et élégante. Bois naturel et assise rembourrée.', 25000, 48000, 8, 2, 10, 'product_16_main.jpg', 'actif'),
('Étagère Murale 5 Niveaux', 'Étagère murale 5 niveaux en métal et bois, parfaite pour ranger et décorer. Facile à monter.', 18000, 35000, 10, 2, 10, 'product_17_main.jpg', 'actif');

-- Catégorie: Objets Déco (id = 11)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Vase Décoratif Céramique', 'Vase décoratif en céramique, design moderne et coloré. Parfait pour fleurs ou décoration seule.', 5000, 9500, 18, 4, 11, 'product_18_main.jpg', 'actif'),
('Tableau Décoratif Moderne', 'Tableau décoratif moderne, impression haute qualité sur toile. Plusieurs motifs disponibles.', 8000, 15000, 15, 3, 11, 'product_19_main.jpg', 'actif'),
('Miroir Décoratif Murale', 'Miroir décoratif mural avec cadre en bois, design élégant. Dimensions: 60x80cm.', 12000, 22000, 8, 2, 11, 'product_20_main.jpg', 'actif');

-- Catégorie: Luminaires (id = 12)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Lampe de Bureau LED', 'Lampe de bureau LED moderne, réglable en hauteur et intensité. Éclairage doux et confortable.', 15000, 28000, 12, 3, 12, 'product_21_main.jpg', 'actif'),
('Lustre Moderne 5 Bras', 'Lustre moderne 5 bras, design contemporain. Parfait pour salon ou salle à manger.', 35000, 65000, 6, 1, 12, 'product_22_main.jpg', 'actif');

-- Insertion des images supplémentaires (6 images par produit)
-- Note: Les IDs de produits doivent correspondre aux produits insérés ci-dessus
-- Si vous avez déjà des produits, ajustez les IDs en conséquence

-- Images pour le produit 1 (T-shirt Enfant)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(1, 'product_1_1.jpg', 1),
(1, 'product_1_2.jpg', 2),
(1, 'product_1_3.jpg', 3),
(1, 'product_1_4.jpg', 4),
(1, 'product_1_5.jpg', 5),
(1, 'product_1_6.jpg', 6);

-- Images pour le produit 2 (Robe Fille)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(2, 'product_2_1.jpg', 1),
(2, 'product_2_2.jpg', 2),
(2, 'product_2_3.jpg', 3),
(2, 'product_2_4.jpg', 4),
(2, 'product_2_5.jpg', 5),
(2, 'product_2_6.jpg', 6);

-- Images pour le produit 3 (Pantalon Garçon)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(3, 'product_3_1.jpg', 1),
(3, 'product_3_2.jpg', 2),
(3, 'product_3_3.jpg', 3),
(3, 'product_3_4.jpg', 4),
(3, 'product_3_5.jpg', 5),
(3, 'product_3_6.jpg', 6);

-- Images pour le produit 4 (Chemise Homme)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(4, 'product_4_1.jpg', 1),
(4, 'product_4_2.jpg', 2),
(4, 'product_4_3.jpg', 3),
(4, 'product_4_4.jpg', 4),
(4, 'product_4_5.jpg', 5),
(4, 'product_4_6.jpg', 6);

-- Images pour le produit 5 (Robe Femme)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(5, 'product_5_1.jpg', 1),
(5, 'product_5_2.jpg', 2),
(5, 'product_5_3.jpg', 3),
(5, 'product_5_4.jpg', 4),
(5, 'product_5_5.jpg', 5),
(5, 'product_5_6.jpg', 6);

-- Images pour le produit 6 (Jeans Femme)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(6, 'product_6_1.jpg', 1),
(6, 'product_6_2.jpg', 2),
(6, 'product_6_3.jpg', 3),
(6, 'product_6_4.jpg', 4),
(6, 'product_6_5.jpg', 5),
(6, 'product_6_6.jpg', 6);

-- Images pour le produit 7 (Service de Table)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(7, 'product_7_1.jpg', 1),
(7, 'product_7_2.jpg', 2),
(7, 'product_7_3.jpg', 3),
(7, 'product_7_4.jpg', 4),
(7, 'product_7_5.jpg', 5),
(7, 'product_7_6.jpg', 6);

-- Images pour le produit 8 (Verres à Vin)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(8, 'product_8_1.jpg', 1),
(8, 'product_8_2.jpg', 2),
(8, 'product_8_3.jpg', 3),
(8, 'product_8_4.jpg', 4),
(8, 'product_8_5.jpg', 5),
(8, 'product_8_6.jpg', 6);

-- Images pour le produit 9 (Couverts Inox)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(9, 'product_9_1.jpg', 1),
(9, 'product_9_2.jpg', 2),
(9, 'product_9_3.jpg', 3),
(9, 'product_9_4.jpg', 4),
(9, 'product_9_5.jpg', 5),
(9, 'product_9_6.jpg', 6);

-- Images pour le produit 10 (Mixeur Blender)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(10, 'product_10_1.jpg', 1),
(10, 'product_10_2.jpg', 2),
(10, 'product_10_3.jpg', 3),
(10, 'product_10_4.jpg', 4),
(10, 'product_10_5.jpg', 5),
(10, 'product_10_6.jpg', 6);

-- Images pour le produit 11 (Bouilloire)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(11, 'product_11_1.jpg', 1),
(11, 'product_11_2.jpg', 2),
(11, 'product_11_3.jpg', 3),
(11, 'product_11_4.jpg', 4),
(11, 'product_11_5.jpg', 5),
(11, 'product_11_6.jpg', 6);

-- Images pour le produit 12 (Grille-Pain)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(12, 'product_12_1.jpg', 1),
(12, 'product_12_2.jpg', 2),
(12, 'product_12_3.jpg', 3),
(12, 'product_12_4.jpg', 4),
(12, 'product_12_5.jpg', 5),
(12, 'product_12_6.jpg', 6);

-- Images pour le produit 13 (Set Ustensiles)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(13, 'product_13_1.jpg', 1),
(13, 'product_13_2.jpg', 2),
(13, 'product_13_3.jpg', 3),
(13, 'product_13_4.jpg', 4),
(13, 'product_13_5.jpg', 5),
(13, 'product_13_6.jpg', 6);

-- Images pour le produit 14 (Planche à Découper)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(14, 'product_14_1.jpg', 1),
(14, 'product_14_2.jpg', 2),
(14, 'product_14_3.jpg', 3),
(14, 'product_14_4.jpg', 4),
(14, 'product_14_5.jpg', 5),
(14, 'product_14_6.jpg', 6);

-- Images pour le produit 15 (Table Basse)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(15, 'product_15_1.jpg', 1),
(15, 'product_15_2.jpg', 2),
(15, 'product_15_3.jpg', 3),
(15, 'product_15_4.jpg', 4),
(15, 'product_15_5.jpg', 5),
(15, 'product_15_6.jpg', 6);

-- Images pour le produit 16 (Chaise Scandinave)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(16, 'product_16_1.jpg', 1),
(16, 'product_16_2.jpg', 2),
(16, 'product_16_3.jpg', 3),
(16, 'product_16_4.jpg', 4),
(16, 'product_16_5.jpg', 5),
(16, 'product_16_6.jpg', 6);

-- Images pour le produit 17 (Étagère Murale)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(17, 'product_17_1.jpg', 1),
(17, 'product_17_2.jpg', 2),
(17, 'product_17_3.jpg', 3),
(17, 'product_17_4.jpg', 4),
(17, 'product_17_5.jpg', 5),
(17, 'product_17_6.jpg', 6);

-- Images pour le produit 18 (Vase Décoratif)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(18, 'product_18_1.jpg', 1),
(18, 'product_18_2.jpg', 2),
(18, 'product_18_3.jpg', 3),
(18, 'product_18_4.jpg', 4),
(18, 'product_18_5.jpg', 5),
(18, 'product_18_6.jpg', 6);

-- Images pour le produit 19 (Tableau Décoratif)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(19, 'product_19_1.jpg', 1),
(19, 'product_19_2.jpg', 2),
(19, 'product_19_3.jpg', 3),
(19, 'product_19_4.jpg', 4),
(19, 'product_19_5.jpg', 5),
(19, 'product_19_6.jpg', 6);

-- Images pour le produit 20 (Miroir Décoratif)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(20, 'product_20_1.jpg', 1),
(20, 'product_20_2.jpg', 2),
(20, 'product_20_3.jpg', 3),
(20, 'product_20_4.jpg', 4),
(20, 'product_20_5.jpg', 5),
(20, 'product_20_6.jpg', 6);

-- Images pour le produit 21 (Lampe de Bureau)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(21, 'product_21_1.jpg', 1),
(21, 'product_21_2.jpg', 2),
(21, 'product_21_3.jpg', 3),
(21, 'product_21_4.jpg', 4),
(21, 'product_21_5.jpg', 5),
(21, 'product_21_6.jpg', 6);

-- Images pour le produit 22 (Lustre Moderne)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(22, 'product_22_1.jpg', 1),
(22, 'product_22_2.jpg', 2),
(22, 'product_22_3.jpg', 3),
(22, 'product_22_4.jpg', 4),
(22, 'product_22_5.jpg', 5),
(22, 'product_22_6.jpg', 6);

-- Vérification
SELECT COUNT(*) as total_produits FROM products;
SELECT categorie_id, COUNT(*) as nombre FROM products GROUP BY categorie_id;
SELECT COUNT(*) as total_images FROM product_images;

-- Note importante:
-- Ce script insère les noms d'images dans la base de données.
-- Les fichiers images doivent être générés avec generate_product_images.php
-- ou uploadés manuellement dans le dossier uploads/products/
-- 
-- Pour générer automatiquement les images placeholder:
-- 1. Exécutez d'abord ce script SQL
-- 2. Puis exécutez: http://localhost/diexo/generate_product_images.php

