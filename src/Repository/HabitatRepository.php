<?php
namespace App\Repository;

use App\Entity\Habitat;
use PDO;

class HabitatRepository
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $sql = 'SELECT * FROM habitats';
        $stmt = $this->connection->query($sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $habitats = [];
        foreach ($result as $row) {
            $imagePath = isset($row['image_path']) ? $row['image_path'] : 'default_image.jpg'; // Valeur par défaut
            $habitat = new Habitat(
                $row['id'],
                $row['name'],
                $row['description'],
                $imagePath
            );
            $habitats[] = $habitat;
        }
        return $habitats;
    }

    public function findById(int $id): ?Habitat
    {
        $sql = 'SELECT * FROM habitats WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $imagePath = isset($row['image_path']) ? $row['image_path'] : 'default_image.jpg'; // Valeur par défaut
            return new Habitat(
                $row['id'],
                $row['name'],
                $row['description'],
                $imagePath
            );
        }

        return null;
    }

    public function create(Habitat $habitat): void
    {
        $sql = 'INSERT INTO habitats (name, description, image_path) VALUES (:name, :description, :image_path)';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'name' => $habitat->getName(),
            'description' => $habitat->getDescription(),
            'image_path' => $habitat->getImagePath() ?? 'default_image.jpg', // Valeur par défaut si non définie
        ]);
    }

    public function update(Habitat $habitat, int $id): void
    {
        $sql = 'UPDATE habitats SET name = :name, description = :description, image_path = :image_path WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'name' => $habitat->getName(),
            'description' => $habitat->getDescription(),
            'image_path' => $habitat->getImagePath() ?? 'default_image.jpg', // Valeur par défaut si non définie
            'id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM habitats WHERE id = :id';
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
