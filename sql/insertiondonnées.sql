-- Insertion d'un utilisateur (admin)
INSERT INTO users (id, email, password, role_id) 
VALUES (UUID(), 'admin@example.com', 'password_hash', (SELECT id FROM roles WHERE name = 'admin'));

-- Insertion d'un habitat
INSERT INTO habitats (id, name, description) 
VALUES (UUID(), 'Savane', 'Un large espace pour les animaux de la savane.');

-- Insertion d'un animal dans un habitat
INSERT INTO animals (id, name, race, habitat_id) 
VALUES (UUID(), 'Leo', 'Lion', (SELECT id FROM habitats WHERE name = 'Savane'));

-- Insertion d'un formulaire vétérinaire pour un animal
INSERT INTO vet_forms (id, animal_id, etat_animal, nourriture_proposee, grammage_nourriture, date_passage, created_by) 
VALUES (UUID(), (SELECT id FROM animals WHERE name = 'Leo'), 'Bonne santé', 'Viande', 5000, '2024-08-26', (SELECT id FROM users WHERE email = 'veterinaire@example.com'));
