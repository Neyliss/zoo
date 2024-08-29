<?php

namespace App\Repository;

use PDO;
use MongoDB\Client as MongoDBClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AnimalRepository
{
    private $connection;
    private $mongoClient;
    private $mongoDb;

    public function __construct(ParameterBagInterface $params)
    {
        // Connexion MySQL
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8',
            $params->get('database_host'),
            $params->get('database_name')
        );

        $this->connection = new PDO($dsn, $params->get('database_user'), $params->get('database_password'));
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Connexion MongoDB
        $this->mongoClient = new MongoDBClient($params->get('mongodb_uri'));
        $this->mongoDb = $this->mongoClient->selectDatabase($params->get('mongodb_db'));
    }

    public function findAll(): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM animals');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM animals WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function insert(array $animal): void
    {
        $stmt = $this->connection->prepare('INSERT INTO animals (id, name, race, habitat_id) VALUES (:id, :name, :race, :habitat_id)');
        $stmt->execute($animal);
    }

    public function update(string $id, array $animal): void
    {
        $stmt = $this->connection->prepare('UPDATE animals SET name = :name, race = :race, habitat_id = :habitat_id WHERE id = :id');
        $animal['id'] = $id;
        $stmt->execute($animal);
    }

    public function delete(string $id): void
    {
        $stmt = $this->connection->prepare('DELETE FROM animals WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function incrementViewCount(string $animal): void
    {
        $collection = $this->mongoDb->selectCollection('animal_views');
        $collection->updateOne(
            ['animal' => $animal],
            ['$inc' => ['views' => 1]],
            ['upsert' => true]
        );
    }

    public function getViewCount(string $animal): int
    {
        $collection = $this->mongoDb->selectCollection('animal_views');
        $document = $collection->findOne(['animal' => $animal]);

        return $document['views'] ?? 0;
    }
}
