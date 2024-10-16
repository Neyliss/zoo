CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Table des rôles utilisateurs
CREATE TABLE roles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE
);

-- Table des utilisateurs (admin, vétérinaire, employé)
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id UUID,
    apiToken VARCHAR(255) DEFAULT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

-- Contrainte pour n'avoir qu'un seul administrateur
ALTER TABLE users ADD CONSTRAINT one_admin CHECK (
  (SELECT COUNT(*) FROM users WHERE role_id = (SELECT id FROM roles WHERE name = 'admin')) <= 1
);

-- Table des habitats (un habitat peut avoir plusieurs animaux)
CREATE TABLE habitat (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image_path VARCHAR(255)
);

-- Table des animaux (plusieurs animaux peuvent appartenir à un seul habitat)
CREATE TABLE animal (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    race VARCHAR(255) NOT NULL,
    habitat_id UUID, -- Clé étrangère vers la table habitat
    image VARCHAR(255) DEFAULT NULL, -- Ajout de l'image pour chaque animal
    FOREIGN KEY (habitat_id) REFERENCES habitat(id) ON DELETE CASCADE -- Relation entre animal et habitat
);

-- Table des formulaires vétérinaires (Vet Forms)
CREATE TABLE vet_forms (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    etat_animal VARCHAR(255) NOT NULL,
    nourriture_proposee VARCHAR(255) NOT NULL,
    grammage_nourriture INT NOT NULL,
    date_passage DATE NOT NULL,
    detail_etat_animal TEXT,
    created_by UUID, -- Vétérinaire qui a créé le formulaire
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table pivot pour la relation plusieurs-à-plusieurs entre animal et vet_forms
CREATE TABLE animal_vet_form (
    animal_id UUID,
    vet_form_id UUID,
    PRIMARY KEY (animal_id, vet_form_id),
    FOREIGN KEY (animal_id) REFERENCES animal(id) ON DELETE CASCADE,
    FOREIGN KEY (vet_form_id) REFERENCES vet_forms(id) ON DELETE CASCADE
);

-- Relation entre un admin et les vet_forms qu'il peut voir dans le Dashboard
CREATE TABLE admin_vet_form (
    admin_id UUID,
    vet_form_id UUID,
    PRIMARY KEY (admin_id, vet_form_id),
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vet_form_id) REFERENCES vet_forms(id) ON DELETE CASCADE
);

CREATE TABLE avis (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    pseudo VARCHAR(255) NOT NULL,
    avis TEXT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    validated_by VARCHAR(255),
    is_validated BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pivot pour la relation plusieurs-à-plusieurs entre employés/admins et avis
CREATE TABLE employee_avis (
    user_id UUID,   -- Employé ou admin
    avis_id UUID,   -- Avis à valider
    PRIMARY KEY (user_id, avis_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (avis_id) REFERENCES avis(id) ON DELETE CASCADE
);

-- Table des contacts
CREATE TABLE contact (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    titre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table pour les photos avec UUID
CREATE TABLE photo (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    titre VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    animal_id UUID NULL,
    habitat_id UUID NULL,
    offer_id UUID NULL,
    FOREIGN KEY (animal_id) REFERENCES animal(id) ON DELETE SET NULL,
    FOREIGN KEY (habitat_id) REFERENCES habitat(id) ON DELETE SET NULL,
    FOREIGN KEY (offer_id) REFERENCES offer(id) ON DELETE SET NULL
);

-- Table des offer
CREATE TABLE offer (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_by UUID, -- Admin ou employé qui crée/modifie/supprime un service
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table pivot pour la relation plusieurs-à-plusieurs entre photos et offres
CREATE TABLE offer_photo (
    offer_id UUID,
    photo_id UUID,
    PRIMARY KEY (offer_id, photo_id),
    FOREIGN KEY (offer_id) REFERENCES offer(id),
    FOREIGN KEY (photo_id) REFERENCES photo(id)
);

-- Table des horaires (Schedules)
CREATE TABLE schedules (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    day VARCHAR(255) NOT NULL,
    hours VARCHAR(255) NOT NULL
);
