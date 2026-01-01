-- Script d'insertion de 4 produits par catégorie principale
-- Total: 12 produits (4 x 3 catégories)
-- Exécutez ce script dans phpMyAdmin

-- Catégorie 1: Vêtements (4 produits)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('T-shirt Homme Classique', 'T-shirt 100% coton pour homme, coupe régulière, confortable au quotidien. Disponible en plusieurs couleurs et tailles.', 8.00, 15.00, 30, 5, 1, 'product_23_main.jpg', 'actif'),
('Robe Été Femme', 'Robe légère et fluide pour l''été, idéale pour les journées chaudes. Tissu respirant et coupe élégante.', 12.00, 25.00, 20, 4, 1, 'product_24_main.jpg', 'actif'),
('Pantalon Chino Homme', 'Pantalon chino élégant pour homme, parfait pour le bureau ou les occasions décontractées. Tissu résistant.', 15.00, 30.00, 25, 5, 1, 'product_25_main.jpg', 'actif'),
('Jupe Midi Femme', 'Jupe midi moderne pour femme, coupe A élégante. Disponible en plusieurs motifs et couleurs.', 10.00, 22.00, 18, 4, 1, 'product_26_main.jpg', 'actif');

-- Catégorie 2: Articles Ménagers (4 produits)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Casserole Anti-Adhésive 24cm', 'Casserole en aluminium avec revêtement anti-adhésif, poignées ergonomiques. Parfaite pour la cuisine quotidienne.', 18.00, 35.00, 15, 3, 2, 'product_27_main.jpg', 'actif'),
('Set de Bols en Céramique 6 Pièces', 'Set de 6 bols en céramique de qualité, design moderne. Idéal pour le petit-déjeuner ou les salades.', 12.00, 24.00, 20, 4, 2, 'product_28_main.jpg', 'actif'),
('Machine à Café Expresso', 'Machine à café expresso automatique, préparation rapide et facile. Réservoir d''eau amovible 1.5L.', 45.00, 85.00, 8, 2, 2, 'product_29_main.jpg', 'actif'),
('Set de Casseroles 3 Pièces', 'Set complet de 3 casseroles avec couvercles en verre, différentes tailles. Compatible tous feux.', 35.00, 65.00, 12, 3, 2, 'product_30_main.jpg', 'actif');

-- Catégorie 3: Décoration Intérieure (4 produits)
INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Canapé 3 Places Moderne', 'Canapé 3 places design moderne, tissu résistant et confortable. Parfait pour le salon. Dimensions: 200x90x85cm.', 180.00, 350.00, 5, 1, 3, 'product_31_main.jpg', 'actif'),
('Tapis Décoratif 200x300cm', 'Tapis décoratif en laine, design contemporain. Doux et confortable sous les pieds. Plusieurs motifs disponibles.', 45.00, 90.00, 10, 2, 3, 'product_32_main.jpg', 'actif'),
('Plante Décorative Artificielle', 'Plante artificielle haute qualité, aspect naturel. Parfaite pour décorer sans entretien. Pot inclus.', 15.00, 30.00, 25, 5, 3, 'product_33_main.jpg', 'actif'),
('Horloge Murale Design', 'Horloge murale design moderne, grand cadran 40cm. Silencieuse et élégante. Plusieurs styles disponibles.', 12.00, 25.00, 20, 4, 3, 'product_34_main.jpg', 'actif');

-- Insertion des images supplémentaires pour chaque produit (6 images par produit)
-- Produit 23 (T-shirt Homme)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(23, 'product_23_1.jpg', 1), (23, 'product_23_2.jpg', 2), (23, 'product_23_3.jpg', 3),
(23, 'product_23_4.jpg', 4), (23, 'product_23_5.jpg', 5), (23, 'product_23_6.jpg', 6);

-- Produit 24 (Robe Été)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(24, 'product_24_1.jpg', 1), (24, 'product_24_2.jpg', 2), (24, 'product_24_3.jpg', 3),
(24, 'product_24_4.jpg', 4), (24, 'product_24_5.jpg', 5), (24, 'product_24_6.jpg', 6);

-- Produit 25 (Pantalon Chino)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(25, 'product_25_1.jpg', 1), (25, 'product_25_2.jpg', 2), (25, 'product_25_3.jpg', 3),
(25, 'product_25_4.jpg', 4), (25, 'product_25_5.jpg', 5), (25, 'product_25_6.jpg', 6);

-- Produit 26 (Jupe Midi)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(26, 'product_26_1.jpg', 1), (26, 'product_26_2.jpg', 2), (26, 'product_26_3.jpg', 3),
(26, 'product_26_4.jpg', 4), (26, 'product_26_5.jpg', 5), (26, 'product_26_6.jpg', 6);

-- Produit 27 (Casserole)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(27, 'product_27_1.jpg', 1), (27, 'product_27_2.jpg', 2), (27, 'product_27_3.jpg', 3),
(27, 'product_27_4.jpg', 4), (27, 'product_27_5.jpg', 5), (27, 'product_27_6.jpg', 6);

-- Produit 28 (Set de Bols)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(28, 'product_28_1.jpg', 1), (28, 'product_28_2.jpg', 2), (28, 'product_28_3.jpg', 3),
(28, 'product_28_4.jpg', 4), (28, 'product_28_5.jpg', 5), (28, 'product_28_6.jpg', 6);

-- Produit 29 (Machine à Café)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(29, 'product_29_1.jpg', 1), (29, 'product_29_2.jpg', 2), (29, 'product_29_3.jpg', 3),
(29, 'product_29_4.jpg', 4), (29, 'product_29_5.jpg', 5), (29, 'product_29_6.jpg', 6);

-- Produit 30 (Set de Casseroles)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(30, 'product_30_1.jpg', 1), (30, 'product_30_2.jpg', 2), (30, 'product_30_3.jpg', 3),
(30, 'product_30_4.jpg', 4), (30, 'product_30_5.jpg', 5), (30, 'product_30_6.jpg', 6);

-- Produit 31 (Canapé)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(31, 'product_31_1.jpg', 1), (31, 'product_31_2.jpg', 2), (31, 'product_31_3.jpg', 3),
(31, 'product_31_4.jpg', 4), (31, 'product_31_5.jpg', 5), (31, 'product_31_6.jpg', 6);

-- Produit 32 (Tapis)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(32, 'product_32_1.jpg', 1), (32, 'product_32_2.jpg', 2), (32, 'product_32_3.jpg', 3),
(32, 'product_32_4.jpg', 4), (32, 'product_32_5.jpg', 5), (32, 'product_32_6.jpg', 6);

-- Produit 33 (Plante)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(33, 'product_33_1.jpg', 1), (33, 'product_33_2.jpg', 2), (33, 'product_33_3.jpg', 3),
(33, 'product_33_4.jpg', 4), (33, 'product_33_5.jpg', 5), (33, 'product_33_6.jpg', 6);

-- Produit 34 (Horloge)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(34, 'product_34_1.jpg', 1), (34, 'product_34_2.jpg', 2), (34, 'product_34_3.jpg', 3),
(34, 'product_34_4.jpg', 4), (34, 'product_34_5.jpg', 5), (34, 'product_34_6.jpg', 6);

-- Vérification
SELECT 
    c.nom as categorie,
    COUNT(p.id) as nombre_produits
FROM categories c
LEFT JOIN products p ON c.id = p.categorie_id
WHERE c.parent_id IS NULL
GROUP BY c.id, c.nom
ORDER BY c.id;

