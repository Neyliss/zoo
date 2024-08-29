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

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM avis');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return new Avis($row['id'], $row['pseudo'], $row['avis'], $row['rating'], $row['validated_by']);
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

        return new Avis($row['id'], $row['pseudo'], $row['avis'], $row['rating'], $row['validated_by']);
    }

    public function save(Avis $avis): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO avis (pseudo, avis, rating, validated_by) 
            VALUES (:pseudo, :avis, :rating, :validated_by)
        ');
        $stmt->execute([
            'pseudo' => $avis->getPseudo(),
            'avis' => $avis->getAvis(),
            'rating' => $avis->getRating(),
            'validated_by' => $avis->getValidatedBy(),
        ]);
        $avis->setId($this->pdo->lastInsertId());
    }

    public function update(Avis $avis): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE avis 
            SET pseudo = :pseudo, avis = :avis, rating = :rating, validated_by = :validated_by 
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $avis->getId(),
            'pseudo' => $avis->getPseudo(),
            'avis' => $avis->getAvis(),
            'rating' => $avis->getRating(),
            'validated_by' => $avis->getValidatedBy(),
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM avis WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
