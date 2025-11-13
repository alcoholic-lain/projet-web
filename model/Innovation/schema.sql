-- =============================
-- Database Schema for Innovation
-- Author: Hichem Challakhi
-- =============================

CREATE DATABASE IF NOT EXISTS innovation_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE innovation_db;

-- Table: categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    date_creation DATETIME NOT NULL,
    INDEX idx_nom (nom),
    INDEX idx_date (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: innovations
CREATE TABLE IF NOT EXISTS innovations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    categorie_id INT,
    description TEXT,
    date_creation DATETIME NOT NULL,
    statut VARCHAR(50) DEFAULT 'En attente',
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_titre (titre),
    INDEX idx_statut (statut),
    INDEX idx_date (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pieces_jointes
CREATE TABLE IF NOT EXISTS pieces_jointes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    innovation_id INT NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin VARCHAR(500) NOT NULL,
    type_fichier VARCHAR(100),
    date_upload DATETIME NOT NULL,
    FOREIGN KEY (innovation_id) REFERENCES innovations(id) ON DELETE CASCADE,
    INDEX idx_innovation (innovation_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: commentaires
CREATE TABLE IF NOT EXISTS commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    innovation_id INT NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    contenu TEXT NOT NULL,
    date_creation DATETIME NOT NULL,
    FOREIGN KEY (innovation_id) REFERENCES innovations(id) ON DELETE CASCADE,
    INDEX idx_innovation (innovation_id),
    INDEX idx_date (date_creation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: votes
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    innovation_id INT NOT NULL,
    type_vote VARCHAR(10) NOT NULL CHECK (type_vote IN ('up', 'down')),
    date_vote DATETIME NOT NULL,
    FOREIGN KEY (innovation_id) REFERENCES innovations(id) ON DELETE CASCADE,
    INDEX idx_innovation (innovation_id),
    INDEX idx_type (type_vote)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample categories
INSERT INTO categories (nom, description, date_creation) VALUES
('Exploration Spatiale', 'Projets liés à l\'exploration de l\'espace et des planètes', NOW()),
('Énergie Orbitale', 'Solutions énergétiques pour l\'espace et les satellites', NOW()),
('Habitats Lunaires', 'Conception et développement d\'habitats pour la Lune', NOW()),
('Robotique Spatiale', 'Robots et systèmes automatisés pour l\'espace', NOW()),
('Propulsion Avancée', 'Nouvelles technologies de propulsion spatiale', NOW());
