ALTER TABLE users ADD COLUMN created_by UUID;
ALTER TABLE users ADD FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL;


-- Table pour les photos avec UUID
CREATE TABLE photo (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    titre VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL
);

-- Relation OneToOne entre photo et habitat avec UUID
ALTER TABLE habitat
ADD COLUMN photo_id UUID UNIQUE,
ADD CONSTRAINT fk_habitat_photo FOREIGN KEY (photo_id) REFERENCES photo(id);

-- Relation OneToMany entre animal et photo avec UUID
ALTER TABLE photo
ADD COLUMN animal_id UUID,
ADD CONSTRAINT fk_photo_animal FOREIGN KEY (animal_id) REFERENCES animal(id);

-- Table de relation ManyToMany entre photo et offer avec UUID
CREATE TABLE offer_photo (
    offer_id UUID,
    photo_id UUID,
    PRIMARY KEY (offer_id, photo_id),
    FOREIGN KEY (offer_id) REFERENCES offer(id),
    FOREIGN KEY (photo_id) REFERENCES photo(id)
);

CREATE TABLE photos (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    path VARCHAR(255) NOT NULL,
    animal_id UUID DEFAULT NULL,
    habitat_id UUID DEFAULT NULL,
    offer_id UUID DEFAULT NULL,
    CONSTRAINT fk_animal FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    CONSTRAINT fk_habitat FOREIGN KEY (habitat_id) REFERENCES habitats(id) ON DELETE CASCADE,
    CONSTRAINT fk_offer FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE CASCADE
);
