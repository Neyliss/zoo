<?php

namespace App\Repository;

use App\Entity\Habitat;
use PDO;

class HabitatRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Alias pour findById
    public function find(string $id): ?Habitat
    {
        return $this->findById($id);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM habitat');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $habitats = [];
        foreach ($results as $result) {
            $habitats[] = new Habitat($result['id'], $result['name'], $result['description'], $result['image_path']);
        }

        return $habitats;
    }

    public function findById(string $id): ?Habitat
    {
        $stmt = $this->pdo->prepare('SELECT * FROM habitat WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Habitat($result['id'], $result['name'], $result['description'], $result['image_path']);
        }

        return null;
    }

    public function save(Habitat $habitat): void
    {
        $existingHabitat = $this->findById($habitat->getId());
        
        if ($existingHabitat) {
            $stmt = $this->pdo->prepare('UPDATE habitat SET name = :name, description = :description, image_path = :image_path WHERE id = :id');
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO habitat (id, name, description, image_path) VALUES (:id, :name, :description, :image_path)');
        }

        $stmt->execute([
            'id' => $habitat->getId(),
            'name' => $habitat->getName(),
            'description' => $habitat->getDescription(),
            'image_path' => $habitat->getImagePath(),
        ]);
    }
}
