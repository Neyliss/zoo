<?php

namespace App\Repository;

use App\Entity\NosOffres;
use Doctrine\DBAL\Connection;

class NosOffresRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $query = 'SELECT * FROM nos_offres';
        $result = $this->connection->fetchAllAssociative($query);

        return array_map(function ($row) {
            return new NosOffres($row['id'], $row['name'], $row['description'], $row['image']);
        }, $result);
    }

    public function findById(string $id): ?NosOffres
    {
        $query = 'SELECT * FROM nos_offres WHERE id = :id';
        $result = $this->connection->fetchAssociative($query, ['id' => $id]);

        if (!$result) {
            return null;
        }

        return new NosOffres($result['id'], $result['name'], $result['description'], $result['image']);
    }

    public function save(NosOffres $nosOffres): void
    {
        $query = 'INSERT INTO nos_offres (id, name, description, image) VALUES (:id, :name, :description, :image)';
        $this->connection->executeStatement($query, [
            'id' => $nosOffres->getId(),
            'name' => $nosOffres->getName(),
            'description' => $nosOffres->getDescription(),
            'image' => $nosOffres->getImage(),
        ]);
    }

    public function update(NosOffres $nosOffres): void
    {
        $query = 'UPDATE nos_offres SET name = :name, description = :description, image = :image WHERE id = :id';
        $this->connection->executeStatement($query, [
            'id' => $nosOffres->getId(),
            'name' => $nosOffres->getName(),
            'description' => $nosOffres->getDescription(),
            'image' => $nosOffres->getImage(),
        ]);
    }

    public function delete(string $id): void
    {
        $query = 'DELETE FROM nos_offres WHERE id = :id';
        $this->connection->executeStatement($query, ['id' => $id]);
    }
}
