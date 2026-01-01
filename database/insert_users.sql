-- Script d'insertion d'utilisateurs de test
-- Exécutez ce script dans phpMyAdmin

-- Script d'insertion d'utilisateurs de test
-- Mot de passe pour les deux utilisateurs: 1010
-- Note: Les mots de passe sont hashés avec bcrypt (password_hash PHP)

-- Créer l'utilisateur administrateur: jodesie
INSERT INTO users (nom, prenom, email, password, role, statut) VALUES
('Jodesie', 'Admin', 'jodesie@diexo.com', '$2y$10$tLSdmoQSKd0YyyhnTGIzZe.TtHaHajHVEXpgRCK/nyV8pIh8B9wrS', 'admin', 'actif');

-- Créer l'utilisateur vendeur: flo
INSERT INTO users (nom, prenom, email, password, role, statut) VALUES
('Flo', 'Vendeur', 'flo@diexo.com', '$2y$10$tLSdmoQSKd0YyyhnTGIzZe.TtHaHajHVEXpgRCK/nyV8pIh8B9wrS', 'vendeur', 'actif');

-- Vérification
SELECT id, nom, prenom, email, role, statut FROM users WHERE email IN ('jodesie@diexo.com', 'flo@diexo.com');

