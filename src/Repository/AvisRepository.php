<?php
namespace App\Repository;

use App\Entity\Avis;
use PDO;

class AvisRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAllValidated(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM avis WHERE is_validated = 1');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return new Avis($row['id'], $row['pseudo'], $row['avis'], $row['rating'], $row['validated_by'], $row['is_validated']);
        }, $results);
    }

    public function findById(string $id): ?Avis
    {
        $stmt = $this->pdo->prepare('SELECT * FROM avis WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Avis($row['id'], $row['pseudo'], $row['avis'], $row['rating'], $row['validated_by'], $row['is_validated']);
    }

    public function save(Avis $avis): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO avis (pseudo, avis, rating, validated_by, is_validated) 
            VALUES (:pseudo, :avis, :rating, :validated_by, :is_validated)
        ');
        $stmt->execute([
            'pseudo' => $avis->getPseudo(),
            'avis' => $avis->getAvis(),
            'rating' => $avis->getRating(),
            'validated_by' => $avis->getValidatedBy(),
            'is_validated' => $avis->isValidated() ? 1 : 0,
        ]);

        // Récupère l'id généré par l'auto-incrémentation après l'insertion
        $avis->setId($this->pdo->lastInsertId());
    }

    public function update(Avis $avis): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE avis 
            SET pseudo = :pseudo, avis = :avis, rating = :rating, validated_by = :validated_by, is_validated = :is_validated 
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $avis->getId(),
            'pseudo' => $avis->getPseudo(),
            'avis' => $avis->getAvis(),
            'rating' => $avis->getRating(),
            'validated_by' => $avis->getValidatedBy(),
            'is_validated' => $avis->isValidated() ? 1 : 0,
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM avis WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function validateAvis(string $id, string $validatedBy): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE avis 
            SET is_validated = 1, validated_by = :validated_by
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'validated_by' => $validatedBy
        ]);
    }
}
