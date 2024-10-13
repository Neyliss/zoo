<?php

namespace App\Repository;

use App\Entity\Photo;
use PDO;
use PDOException;

class PhotoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function save(string $entityId, string $path, ?string $entityType = 'habitat'): Photo
    {
        $photoId = uniqid();

        $stmt = $this->pdo->prepare('INSERT INTO photos (id, path, ' . $entityType . '_id) VALUES (:id, :path, :entityId)');
        $stmt->execute([
            'id' => $photoId,
            'path' => $path,
            'entityId' => $entityId
        ]);

        return new Photo($photoId, $path, $entityType === 'animal' ? $entityId : null, $entityType === 'habitat' ? $entityId : null, $entityType === 'offer' ? $entityId : null);
    }

    public function find(string $id): ?Photo
    {
        $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $photoData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$photoData) {
            return null;
        }

        return new Photo(
            $photoData['id'],
            $photoData['path'],
            $photoData['animal_id'] ?? null,
            $photoData['habitat_id'] ?? null,
            $photoData['offer_id'] ?? null
        );
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM photos WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function update(string $id, string $path, ?string $animalId = null, ?string $habitatId = null, ?string $offerId = null): void
    {
        $stmt = $this->pdo->prepare('UPDATE photos SET path = :path, animal_id = :animalId, habitat_id = :habitatId, offer_id = :offerId WHERE id = :id');
        $stmt->execute([
            'path' => $path,
            'animalId' => $animalId,
            'habitatId' => $habitatId,
            'offerId' => $offerId,
            'id' => $id
        ]);
    }
}
