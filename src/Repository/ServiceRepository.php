<?php

namespace App\Repository;

use App\Entity\Service;
use Doctrine\DBAL\Connection;

class ServiceRepository
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $query = 'SELECT * FROM services';
        $result = $this->connection->fetchAllAssociative($query);

        return array_map(function ($row) {
            return new Service($row['id'], $row['name'], $row['description'], $row['image']);
        }, $result);
    }

    public function findById(string $id): ?Service
    {
        $query = 'SELECT * FROM services WHERE id = :id';
        $result = $this->connection->fetchAssociative($query, ['id' => $id]);

        if (!$result) {
            return null;
        }

        return new Service($result['id'], $result['name'], $result['description'], $result['image']);
    }

    public function save(Service $service): void
    {
        $query = 'INSERT INTO services (id, name, description, image) VALUES (:id, :name, :description, :image)';
        $this->connection->executeStatement($query, [
            'id' => $service->getId(),
            'name' => $service->getName(),
            'description' => $service->getDescription(),
            'image' => $service->getImage(),
        ]);
    }

    public function update(Service $service): void
    {
        $query = 'UPDATE services SET name = :name, description = :description, image = :image WHERE id = :id';
        $this->connection->executeStatement($query, [
            'id' => $service->getId(),
            'name' => $service->getName(),
            'description' => $service->getDescription(),
            'image' => $service->getImage(),
        ]);
    }

    public function delete(string $id): void
    {
        $query = 'DELETE FROM services WHERE id = :id';
        $this->connection->executeStatement($query, ['id' => $id]);
    }
}
