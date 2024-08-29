-- Connexion à MySQL avec les identifiants fournis
mysql -u neyliss -p
-- Mot de passe : Neylisse.0795

-- Création de la base de données
CREATE DATABASE zoobackend CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE zoobackend;

-- Table des rôles utilisateurs
CREATE TABLE roles (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL
);

-- Table des utilisateurs (admin, vétérinaire, employé)
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id CHAR(36),
    token VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

-- Table des habitats
CREATE TABLE habitats (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL
);

-- Table des animaux
CREATE TABLE animals (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    race VARCHAR(255) NOT NULL,
    habitat_id CHAR(36),
    FOREIGN KEY (habitat_id) REFERENCES habitats(id) ON DELETE CASCADE
);

-- Table des formulaires vétérinaires
CREATE TABLE vet_forms (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    animal_id CHAR(36),
    etat_animal VARCHAR(255) NOT NULL,
    nourriture_proposee VARCHAR(255) NOT NULL,
    grammage_nourriture INT NOT NULL,
    date_passage DATE NOT NULL,
    detail_etat_animal TEXT,
    created_by CHAR(36),
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des avis
CREATE TABLE avis (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    pseudo VARCHAR(255) NOT NULL,
    avis TEXT NOT NULL,
    rating INT CHECK(rating BETWEEN 1 AND 5),
    validated_by CHAR(36),
    FOREIGN KEY (validated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des contacts
CREATE TABLE contacts (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    titre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des services
CREATE TABLE services (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL
);

-- Insertion des rôles prédéfinis (admin, vétérinaire, employé)
INSERT INTO roles (id, name) VALUES 
(UUID(), 'admin'), 
(UUID(), 'veterinaire'), 
(UUID(), 'employe');

-- Insertion d'un utilisateur exemple (admin)
INSERT INTO users (id, email, password, role_id) 
VALUES (UUID(), 'admin@example.com', 'password_hash', (SELECT id FROM roles WHERE name = 'admin'));

-- Insertion d'un habitat exemple
INSERT INTO habitats (id, name, description) 
VALUES (UUID(), 'Savane', 'Un large espace pour les animaux de la savane.');

-- Insertion d'un animal exemple dans un habitat
INSERT INTO animals (id, name, race, habitat_id) 
VALUES (UUID(), 'Leo', 'Lion', (SELECT id FROM habitats WHERE name = 'Savane'));

-- Insertion d'un formulaire vétérinaire exemple pour un animal
INSERT INTO vet_forms (id, animal_id, etat_animal, nourriture_proposee, grammage_nourriture, date_passage, created_by) 
VALUES (UUID(), 
        (SELECT id FROM animals WHERE name = 'Leo'), 
        'Bonne santé', 
        'Viande', 
        5000, 
        '2024-08-26', 
        (SELECT id FROM users WHERE email = 'veterinaire@example.com'));
