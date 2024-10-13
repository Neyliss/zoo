<?php

namespace App\Repository;

use App\Entity\Offer;
use PDO;

class OfferRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM offer');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $offers = [];
        foreach ($results as $result) {
            $offer = (new Offer())
                ->setId($result['id'])
                ->setName($result['name'])
                ->setDescription($result['description'])
                ->setImage($result['image']);
            $offers[] = $offer;
        }

        return $offers;
    }

    public function findById(string $id): ?Offer
    {
        $stmt = $this->pdo->prepare('SELECT * FROM offer WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return (new Offer())
            ->setId($result['id'])
            ->setName($result['name'])
            ->setDescription($result['description'])
            ->setImage($result['image']);
    }

    public function save(Offer $offer): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO offer (id, name, description, image) VALUES (uuid_generate_v4(), :name, :description, :image)');
        $stmt->execute([
            'name' => $offer->getName(),
            'description' => $offer->getDescription(),
            'image' => $offer->getImage(),
        ]);
    }

    public function update(Offer $offer): void
    {
        $stmt = $this->pdo->prepare('UPDATE offer SET name = :name, description = :description, image = :image WHERE id = :id');
        $stmt->execute([
            'id' => $offer->getId(),
            'name' => $offer->getName(),
            'description' => $offer->getDescription(),
            'image' => $offer->getImage(),
        ]);
    }

    public function delete(string $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM offer WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
