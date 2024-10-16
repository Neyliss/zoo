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
        $stmt = $this->pdo->prepare('INSERT INTO animal (id, name, race, habitat_id, image_path) VALUES (:id, :name, :race, :habitat_id, :image_path)');
        $stmt->execute([
            'id' => $animal->getId(),
            'name' => $animal->getName(),
            'race' => $animal->getRace(),
            'habitat_id' => $animal->getHabitatId(),
            'image_path' => $animal->getImagePath() // Gestion de l'image principale
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM animal WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
