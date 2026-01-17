-- Ajout de 8 produits de test SUPPLÉMENTAIRES (Batch 3) - VERSION P.ID FORCÉS
-- Cette version FORCE les IDs des produits pour garantir que les images s'y rattachent correctement.

-- 1. Catégories (IDs 1, 2, 3 + 6, 8, 11, 50)
INSERT IGNORE INTO categories (id, nom, description, parent_id) VALUES 
(1, 'Vêtements', 'Mode et habillement', NULL),
(2, 'Maison', 'Articles pour la maison', NULL),
(3, 'Décoration', 'Objets décoratifs', NULL);

INSERT IGNORE INTO categories (id, nom, description, parent_id) VALUES 
(6, 'Accessoires', 'Accessoires de mode', 1),
(8, 'Électroménagers', 'Petits appareils', 2),
(11, 'Objets Divers', 'Loisirs et Déco', 3),
(50, 'High Tech', 'Produits électroniques et gadgets', 2);

-- 2. Insérer les produits avec ID EXPLICITE pour matcher les images
INSERT INTO products (id, nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
(43, 'Appareil Photo Reflex', 'Appareil photo numérique reflex 24MP avec objectif 18-55mm.', 250000, 450000, 5, 2, 50, 'product_43_main.jpg', 'actif'),
(44, 'Tapis de Yoga Pro', 'Tapis de yoga antidérapant et écologique, épaisseur 6mm.', 8000, 15000, 30, 5, 11, 'product_44_main.jpg', 'actif'),
(45, 'Machine à Café Expresso', 'Machine à café automatique pour expresso et cappuccino.', 45000, 85000, 8, 2, 8, 'product_45_main.jpg', 'actif'),
(46, 'Parfum Eau de Toilette', 'Eau de toilette fraîcheur marine, flacon 100ml.', 15000, 35000, 20, 5, 6, 'product_46_main.jpg', 'actif'),
(47, 'Guitare Acoustique', 'Guitare acoustique folk en bois d\'épicéa, idéale débutants.', 35000, 65000, 10, 2, 11, 'product_47_main.jpg', 'actif'),
(48, 'Souris Gaming RGB', 'Souris gamer filaire avec capteur haute précision et lumières RGB.', 12000, 25000, 25, 5, 50, 'product_48_main.jpg', 'actif'),
(49, 'Sac de Sport', 'Sac de sport pratique avec compartiment chaussures.', 10000, 19000, 15, 3, 6, 'product_49_main.jpg', 'actif'),
(50, 'Clavier Mécanique', 'Clavier mécanique switches bleus pour une frappe réactive.', 18000, 32000, 12, 3, 50, 'product_50_main.jpg', 'actif')
ON DUPLICATE KEY UPDATE nom=VALUES(nom); -- Evite erreur si ID existe déjà

-- 3. Images (IDs 43-50 garantis existants)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(43, 'product_43_main.jpg', 1),
(44, 'product_44_main.jpg', 1),
(45, 'product_45_main.jpg', 1),
(46, 'product_46_main.jpg', 1),
(47, 'product_47_main.jpg', 1),
(48, 'product_48_main.jpg', 1),
(49, 'product_49_main.jpg', 1),
(50, 'product_50_main.jpg', 1);
