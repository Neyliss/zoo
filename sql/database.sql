CREATE EXTENSION IF NOT EXISTS "uuid-ossp";


-- Table des rôles utilisateurs
CREATE TABLE roles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL
);

-- Table des utilisateurs (admin, vétérinaire, employé)
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id UUID,
    token VARCHAR(255) DEFAULT NULL, -- Utilisation de JWT pour la gestion des tokens
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

-- Table des habitats
CREATE TABLE habitats (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL -- Ajout de l'image pour chaque habitat
);

-- Table des animaux
CREATE TABLE animals (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    race VARCHAR(255) NOT NULL,
    habitat_id UUID,
    image VARCHAR(255) DEFAULT NULL, -- Ajout de l'image pour chaque animal
    FOREIGN KEY (habitat_id) REFERENCES habitats(id) ON DELETE CASCADE
);

-- Table des formulaires vétérinaires
CREATE TABLE vet_forms (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    animal_id UUID,
    etat_animal VARCHAR(255) NOT NULL,
    nourriture_proposee VARCHAR(255) NOT NULL,
    grammage_nourriture INT NOT NULL,
    date_passage DATE NOT NULL,
    detail_etat_animal TEXT,
    created_by UUID, -- Vétérinaire qui a créé le formulaire
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des avis
CREATE TABLE avis (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    pseudo VARCHAR(255) NOT NULL,
    avis TEXT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    validated_by UUID, -- Employé qui valide/invalide les avis
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (validated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des contacts
CREATE TABLE contacts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    titre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des services
CREATE TABLE services (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_by UUID, -- Admin ou employé qui crée/modifie/supprime un service
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insertion des rôles prédéfinis (admin, vétérinaire, employé)
INSERT INTO roles (id, name) VALUES 
(uuid_generate_v4(), 'admin'), 
(uuid_generate_v4(), 'veterinaire'), 
(uuid_generate_v4(), 'employe');

-- Insertion d'un utilisateur exemple (admin)
INSERT INTO users (id, email, password, role_id) 
VALUES (
    uuid_generate_v4(), 
    'admin@zoobackend.com', 
    crypt('VotreMotDePasseSécurisé', gen_salt('bf')), 
    (SELECT id FROM roles WHERE name = 'admin')
);

-- Insertion d'un habitat exemple
INSERT INTO habitats (id, name, description, image) 
VALUES (uuid_generate_v4(), 'Savane', 'Un large espace pour les animaux de la savane.', '/Images/savane.jpeg');

-- Insertion d'un animal exemple dans un habitat
INSERT INTO animals (id, name, race, habitat_id, image) 
VALUES (uuid_generate_v4(), 'Leo', 'Lion', (SELECT id FROM habitats WHERE name = 'Savane'), '/Images/lion.jpeg');

-- Insertion d'un formulaire vétérinaire exemple pour un animal
INSERT INTO vet_forms (id, animal_id, etat_animal, nourriture_proposee, grammage_nourriture, date_passage, created_by) 
VALUES (uuid_generate_v4(), 
        (SELECT id FROM animals WHERE name = 'Leo'), 
        'Bonne santé', 
        'Viande', 
        5000, 
        '2024-08-26', 
        (SELECT id FROM users WHERE email = 'veterinaire@example.com'));

-- Insertion d'un service exemple
INSERT INTO services (id, name, description, image, created_by) 
VALUES (uuid_generate_v4(), 'Nettoyage des enclos', 'Service de nettoyage des habitats pour assurer un environnement sain.', '/Images/nettoyage.jpeg', 
        (SELECT id FROM users WHERE email = 'admin@zoobackend.com'));
