<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\DBAL\Connection;

class OfferRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $query = 'SELECT * FROM offer';
        $result = $this->connection->fetchAllAssociative($query);

        return array_map(function ($row) {
            $offer = new Offer();
            $offer->setName($row['name'])
                ->setDescription($row['description'])
                ->setImage($row['image']);
            return $offer;
        }, $result);
    }

    public function findById(string $id): ?Offer
    {
        $query = 'SELECT * FROM offer WHERE id = :id';
        $result = $this->connection->fetchAssociative($query, ['id' => $id]);

        if (!$result) {
            return null;
        }

        $offer = new Offer();
        $offer->setName($result['name'])
            ->setDescription($result['description'])
            ->setImage($result['image']);
        return $offer;
    }

    public function save(Offer $offer): void
    {
        $query = 'INSERT INTO offer (id, name, description, image) VALUES (:id, :name, :description, :image)';
        $this->connection->executeStatement($query, [
            'id' => $offer->getId(),
            'name' => $offer->getName(),
            'description' => $offer->getDescription(),
            'image' => $offer->getImage(),
        ]);
    }

    public function update(Offer $offer): void
    {
        $query = 'UPDATE offer SET name = :name, description = :description, image = :image WHERE id = :id';
        $this->connection->executeStatement($query, [
            'id' => $offer->getId(),
            'name' => $offer->getName(),
            'description' => $offer->getDescription(),
            'image' => $offer->getImage(),
        ]);
    }

    public function delete(string $id): void
    {
        $query = 'DELETE FROM offer WHERE id = :id';
        $this->connection->executeStatement($query, ['id' => $id]);
    }
}
