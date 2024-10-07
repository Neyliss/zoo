<?php

namespace App\Repository;

use App\Entity\User;
use PDO;
use PDOException;

class UserRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM users');
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($user) {
                return new User(
                    $user['id'],
                    $user['email'],
                    $user['password'],
                    $user['role_id'],
                    $user['apitoken']
                );
            }, $users);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération des utilisateurs : ' . $e->getMessage());
        }
    }

    public function findById($id): ?User
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return new User(
                    $user['id'],
                    $user['email'],
                    $user['password'],
                    $user['role_id'],
                    $user['token']
                );
            }

            return null;
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function findByEmail($email): ?User
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                return new User(
                    $user['id'],
                    $user['email'],
                    $user['password'],
                    $user['role_id'],
                    $user['token']
                );
            }

            return null;
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function findOneBy(array $criteria): ?User
    {
        try {
            $query = 'SELECT * FROM users WHERE ';
            $queryParts = [];
            $params = [];

            foreach ($criteria as $field => $value) {
                $queryParts[] = "$field = :$field";
                $params[":$field"] = $value;
            }

            $query .= implode(' AND ', $queryParts);
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ? new User(
                $user['id'],
                $user['email'],
                $user['password'],
                $user['role_id'],
                $user['token']
            ) : null;
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la recherche de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function save(User $user): void
    {
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO users (id, email, password, role_id, token) VALUES (:id, :email, :password, :role_id, :token)'
            );
            $stmt->execute([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'role_id' => $user->getRoles(),
                'token' => $user->getApiToken(),
            ]);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de l\'enregistrement de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function update(User $user): void
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE users SET email = :email, password = :password, role_id = :role_id, token = :token WHERE id = :id'
            );
            $stmt->execute([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'role_id' => $user->getRoles(),
                'token' => $user->getApiToken(),
            ]);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function delete($id): void
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la suppression de l\'utilisateur : ' . $e->getMessage());
        }
    }
}
