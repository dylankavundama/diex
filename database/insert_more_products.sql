-- Ajout de 10 nouveaux produits
-- Images placeholder utilisées

-- Catégorie: Vêtements Hommes (Assumé id=5 ou similaire, basé sur les données précédentes 5=Vêtements Adultes)
-- Catégorie: Accessoires (id=3 basé sur header.php)

INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Montre Classique Cuir', 'Montre élégante avec bracelet en cuir véritable. Résistante à l\'eau.', 15000, 29000, 10, 2, 3, 'product_23_main.jpg', 'actif'),
('Sac à Dos Urbain', 'Sac à dos pratique pour la ville, compartiment ordinateur 15 pouces.', 8000, 16000, 15, 3, 3, 'product_24_main.jpg', 'actif'),
('Ceinture Cuir Noir', 'Ceinture en cuir noir de haute qualité, boucle argentée.', 3000, 6000, 20, 5, 3, 'product_25_main.jpg', 'actif'),
('Chapeau Panama', 'Chapeau style Panama léger pour l\'été. Protection UV.', 4000, 8500, 12, 3, 3, 'product_26_main.jpg', 'actif'),
('Écharpe Laine Douce', 'Écharpe en laine mérinos douce et chaude. Plusieurs coloris.', 5000, 9500, 18, 4, 3, 'product_27_main.jpg', 'actif'),
('Lunettes de Soleil Aviator', 'Lunettes de soleil style aviateur, verres polarisés.', 6000, 12000, 25, 5, 3, 'product_28_main.jpg', 'actif'),
('Portefeuille Homme', 'Portefeuille compact en cuir, protection RFID.', 4500, 9000, 15, 3, 3, 'product_29_main.jpg', 'actif'),
('Cravate Soie Rouge', 'Cravate en soie rouge à motifs discrets. Idéale pour les costumes.', 3500, 7000, 20, 5, 3, 'product_30_main.jpg', 'actif'),
('Gants Cuir Hiver', 'Gants en cuir doublés polaire pour l\'hiver. Tactiles.', 7000, 14000, 10, 2, 3, 'product_31_main.jpg', 'actif'),
('Sac de Voyage Weekend', 'Grand sac de voyage pour le weekend, toile résistante et cuir.', 18000, 35000, 8, 2, 3, 'product_32_main.jpg', 'actif');

-- Images pour les nouveaux produits (Simplifié à 1 image principale par produit pour l'instant dans product_images)
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(23, 'product_23_main.jpg', 1),
(24, 'product_24_main.jpg', 1),
(25, 'product_25_main.jpg', 1),
(26, 'product_26_main.jpg', 1),
(27, 'product_27_main.jpg', 1),
(28, 'product_28_main.jpg', 1),
(29, 'product_29_main.jpg', 1),
(30, 'product_30_main.jpg', 1),
(31, 'product_31_main.jpg', 1),
(32, 'product_32_main.jpg', 1);
