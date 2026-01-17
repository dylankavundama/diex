-- Ajout de 10 produits SUPPLÉMENTAIRES (Batch 2)
-- Catégories variées

INSERT INTO products (nom, description, prix_achat, prix_vente, stock, stock_minimum, categorie_id, image_principale, statut) VALUES
('Sneakers Blanches Urbaines', 'Sneakers blanches confortables, style urbain minimaliste.', 12000, 25000, 20, 5, 5, 'product_33_main.jpg', 'actif'),
('Casque Audio Sans Fil', 'Casque audio bluetooth avec réduction de bruit active.', 25000, 45000, 15, 3, 3, 'product_34_main.jpg', 'actif'),
('Enceinte Portable Bluetooth', 'Petite enceinte puissante et résistante à l\'eau.', 15000, 28000, 25, 5, 3, 'product_35_main.jpg', 'actif'),
('Bougie Parfumée Lavande', 'Bougie artisanale parfumée à la lavande, pot en verre.', 2000, 4500, 30, 5, 11, 'product_36_main.jpg', 'actif'),
('Coussin Velours Bleu', 'Coussin décoratif en velours bleu nuit, très doux.', 4000, 8000, 12, 3, 11, 'product_37_main.jpg', 'actif'),
('Plante Artificielle Pot', 'Belle plante verte artificielle en pot céramique, réaliste.', 3500, 7500, 18, 4, 11, 'product_38_main.jpg', 'actif'),
('Lampe de Chevet Design', 'Lampe de chevet tactile avec port USB intégré.', 10000, 19000, 10, 2, 12, 'product_39_main.jpg', 'actif'),
('Tapis Salon Géométrique', 'Tapis de salon motifs géométriques, 160x230cm.', 30000, 55000, 5, 1, 10, 'product_40_main.jpg', 'actif'),
('Sac à Main Cuir Camel', 'Sac à main femme en cuir couleur camel, élégant.', 20000, 38000, 8, 2, 3, 'product_41_main.jpg', 'actif'),
('Chemise Lin Blanc', 'Chemise légère en lin blanc, coupe décontractée.', 9000, 17000, 15, 3, 5, 'product_42_main.jpg', 'actif');

-- Images pour Batch 2
INSERT INTO product_images (product_id, image_path, ordre) VALUES
(33, 'product_33_main.jpg', 1),
(34, 'product_34_main.jpg', 1),
(35, 'product_35_main.jpg', 1),
(36, 'product_36_main.jpg', 1),
(37, 'product_37_main.jpg', 1),
(38, 'product_38_main.jpg', 1),
(39, 'product_39_main.jpg', 1),
(40, 'product_40_main.jpg', 1),
(41, 'product_41_main.jpg', 1),
(42, 'product_42_main.jpg', 1);
