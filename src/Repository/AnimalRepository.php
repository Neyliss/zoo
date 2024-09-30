<?php

namespace App\Repository;

use App\Entity\Animal;
use PDO;

class AnimalRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM animal');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $animals = [];
        foreach ($results as $result) {
            $animals[] = new Animal(
                $result['id'], 
                $result['name'], 
                $result['race'], 
                $result['habitat_id'], 
                $result['image_path'] // Ajout de la gestion de l'image
            );
        }

        return $animals;
    }

    public function findById(string $id): ?Animal
    {
        $stmt = $this->pdo->prepare('SELECT * FROM animal WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Animal(
                $result['id'], 
                $result['name'], 
                $result['race'], 
                $result['habitat_id'],
                $result['image_path'] // Ajout de la gestion de l'image
            );
        }

        return null;
    }

    public function findByHabitatId(string $habitatId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM animal WHERE habitat_id = :habitat_id');
        $stmt->execute(['habitat_id' => $habitatId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $animals = [];
        foreach ($results as $result) {
            $animals[] = new Animal(
                $result['id'], 
                $result['name'], 
                $result['race'], 
                $result['habitat_id'],
                $result['image_path'] // Ajout de la gestion de l'image
            );
        }

        return $animals;
    }

    public function save(Animal $animal): void
    {
        if ($animal->getId()) {
            // Si l'ID est défini, nous mettons à jour l'enregistrement existant
            $stmt = $this->pdo->prepare('UPDATE animal SET name = :name, race = :race, habitat_id = :habitat_id, image_path = :image_path WHERE id = :id');
            $stmt->execute([
                'id' => $animal->getId(),
                'name' => $animal->getName(),
                'race' => $animal->getRace(),
                'habitat_id' => $animal->getHabitatId(),
                'image_path' => $animal->getImagePath(), // Gestion de l'image
            ]);
        } else {
            // Sinon, nous insérons un nouvel enregistrement
            $stmt = $this->pdo->prepare('INSERT INTO animal (id, name, race, habitat_id, image_path) VALUES (:id, :name, :race, :habitat_id, :image_path)');
            $stmt->execute([
                'id' => $animal->getId(),
                'name' => $animal->getName(),
                'race' => $animal->getRace(),
                'habitat_id' => $animal->getHabitatId(),
                'image_path' => $animal->getImagePath(), // Gestion de l'image
            ]);
        }
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM animal WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
