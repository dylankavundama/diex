-- Schéma de base de données pour Diexo E-commerce

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'vendeur', 'client') DEFAULT 'client',
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    parent_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des produits
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix_achat DECIMAL(10, 2) NOT NULL,
    prix_vente DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    stock_minimum INT DEFAULT 5,
    categorie_id INT NOT NULL,
    vendeur_id INT,
    image_principale VARCHAR(255),
    statut ENUM('actif', 'inactif', 'rupture') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (vendeur_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des images produits
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    ordre INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des attributs produits (taille, couleur, etc.)
CREATE TABLE IF NOT EXISTS product_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    attribute_type ENUM('taille', 'couleur', 'matiere') NOT NULL,
    attribute_value VARCHAR(100) NOT NULL,
    stock INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des commandes
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    numero_commande VARCHAR(50) UNIQUE NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'en_preparation', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
    total DECIMAL(10, 2) NOT NULL,
    mode_paiement ENUM('espece', 'mobile_money', 'carte', 'whatsapp') DEFAULT 'whatsapp',
    adresse_livraison TEXT,
    telephone_livraison VARCHAR(20),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des détails de commande
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    prix_total DECIMAL(10, 2) NOT NULL,
    benefice DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des paiements
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    client_id INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    type_paiement ENUM('entree', 'sortie') DEFAULT 'entree',
    mode_paiement ENUM('espece', 'mobile_money', 'carte', 'virement') NOT NULL,
    description TEXT,
    statut ENUM('valide', 'annule') DEFAULT 'valide',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des dettes clients
CREATE TABLE IF NOT EXISTS client_debts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    order_id INT,
    montant_total DECIMAL(10, 2) NOT NULL,
    montant_paye DECIMAL(10, 2) DEFAULT 0,
    montant_restant DECIMAL(10, 2) NOT NULL,
    statut ENUM('en_cours', 'partiel', 'paye') DEFAULT 'en_cours',
    date_echeance DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des statistiques de visite
CREATE TABLE IF NOT EXISTS site_statistics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_visite DATE NOT NULL,
    page_url VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    user_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_date (date_visite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des rapports financiers
CREATE TABLE IF NOT EXISTS financial_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_report DATE NOT NULL,
    type_report ENUM('journalier', 'mensuel', 'annuel') NOT NULL,
    recettes DECIMAL(10, 2) DEFAULT 0,
    depenses DECIMAL(10, 2) DEFAULT 0,
    benefice DECIMAL(10, 2) DEFAULT 0,
    nombre_ventes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date_type (date_report, type_report)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion des catégories principales
INSERT INTO categories (nom, description) VALUES
('Vêtements', 'Vêtements pour enfants et adultes'),
('Articles Ménagers', 'Vaisselle, électroménagers, accessoires de cuisine'),
('Décoration Intérieure', 'Meubles, objets déco, luminaires');

-- Insertion des sous-catégories pour vêtements
INSERT INTO categories (nom, description, parent_id) VALUES
('Vêtements Enfants', 'Vêtements pour enfants', 1),
('Vêtements Adultes', 'Vêtements pour adultes', 1),
('Accessoires', 'Accessoires de mode', 1);

-- Insertion des sous-catégories pour articles ménagers
INSERT INTO categories (nom, description, parent_id) VALUES
('Vaisselle', 'Assiettes, verres, couverts', 2),
('Électroménagers', 'Petits électroménagers', 2),
('Accessoires Cuisine', 'Ustensiles et accessoires de cuisine', 2);

-- Insertion des sous-catégories pour décoration
INSERT INTO categories (nom, description, parent_id) VALUES
('Meubles', 'Meubles de maison', 3),
('Objets Déco', 'Objets de décoration', 3),
('Luminaires', 'Lampes et éclairages', 3);

-- Création d'un utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO users (nom, prenom, email, password, role) VALUES
('Admin', 'Système', 'admin@diexo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

