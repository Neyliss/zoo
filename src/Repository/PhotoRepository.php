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
        $photoId = uniqid(); // Générer un identifiant unique pour la photo

        // Insertion dans la base de données
        $stmt = $this->pdo->prepare('INSERT INTO photos (id, path, ' . $entityType . '_id) VALUES (:id, :path, :entityId)');
        $stmt->execute([
            'id' => $photoId,
            'path' => $path,
            'entityId' => $entityId
        ]);

        // Retourner l'objet Photo après l'insertion
        return new Photo(
            $photoId,
            $path,
            $entityType === 'animal' ? $entityId : null, 
            $entityType === 'habitat' ? $entityId : null, 
            $entityType === 'offer' ? $entityId : null
        );
    }

    public function delete(string $id): void
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM photos WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la suppression de la photo : ' . $e->getMessage());
        }
    }

    public function find(string $id): ?Photo
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return new Photo(
                    $result['id'], 
                    $result['path'], 
                    $result['animal_id'] ?? null, 
                    $result['habitat_id'] ?? null, 
                    $result['offer_id'] ?? null
                );
            }
            return null;
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération de la photo : ' . $e->getMessage());
        }
    }

    public function findByAnimalId(string $animalId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE animal_id = :animal_id');
            $stmt->execute(['animal_id' => $animalId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des photos : ' . $e->getMessage());
        }
    }

    public function findByHabitatId(string $habitatId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE habitat_id = :habitat_id');
            $stmt->execute(['habitat_id' => $habitatId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des photos : ' . $e->getMessage());
        }
    }

    public function findByOfferId(string $offerId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM photos WHERE offer_id = :offer_id');
            $stmt->execute(['offer_id' => $offerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des photos : ' . $e->getMessage());
        }
    }
}
