<?php

namespace App\Repository;

use App\Entity\User;
use PDO;
use PDOException;
use Ramsey\Uuid\Uuid; 

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
                // Récupère le rôle correspondant à l'utilisateur
                $roleName = $this->findRoleNameById($user['role_id']);

                return new User(
                    $user['id'],
                    $user['email'],
                    $user['password'],
                    [$roleName], // Passe un tableau de rôles
                    $user['apiToken']
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
                // Récupère le rôle correspondant à l'utilisateur
                $roleName = $this->findRoleNameById($user['role_id']);

                return new User(
                    $user['id'],
                    $user['email'],
                    $user['password'],
                    [$roleName], // Passe un tableau de rôles
                    $user['apiToken']
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
                // Récupère le rôle correspondant à l'utilisateur
                $roleName = $this->findRoleNameById($user['role_id']);

                return new User(
                    $user['id'],
                    $user['email'],
                    $user['password'],
                    [$roleName], // Passe un tableau de rôles
                    $user['apiToken'] ?? null // Si apiToken est manquant, définissez-le sur null
                );
            }

            return null;
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function save(User $user): void
    {
        try {
            // Générer un UUID si l'utilisateur n'en a pas
            if (empty($user->getId())) {
                $user->setId(Uuid::uuid4()->toString()); // Générer un nouvel UUID
            }

            $roleId = $this->findRoleIdByName($user->getRoles()[0]);

            $stmt = $this->pdo->prepare(
                'INSERT INTO users (id, email, password, role_id, apiToken) VALUES (:id, :email, :password, :role_id, :apiToken)'
            );
            $stmt->execute([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => password_hash($user->getPassword(), PASSWORD_BCRYPT), // Hashage du mot de passe
                'role_id' => $roleId,
                'apiToken' => $user->getApiToken(),
            ]);            
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de l\'enregistrement de l\'utilisateur : ' . $e->getMessage());
        }
    }

    public function update(User $user): void
    {
        try {
            // Récupérer l'UUID du rôle
            $roleId = $this->findRoleIdByName($user->getRoles()[0]);

            $stmt = $this->pdo->prepare(
                'UPDATE users SET email = :email, password = :password, role_id = :role_id, apiToken = :apiToken WHERE id = :id'
            );
            $stmt->execute([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'role_id' => $roleId, // Mettre à jour avec l'UUID du rôle
                'apiToken' => $user->getApiToken(),
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

    // Récupérer le nom du rôle par l'UUID
    public function findRoleNameById(?string $roleId): string
{
    if ($roleId === null) {
        return 'ROLE_USER'; // Rôle par défaut si l'utilisateur n'a pas de rôle assigné
    }

    try {
        $stmt = $this->pdo->prepare('SELECT name FROM roles WHERE id = :roleId');
        $stmt->execute(['roleId' => $roleId]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        return $role['name'] ?? 'ROLE_USER'; // Retourne également un rôle par défaut
    } catch (PDOException $e) {
        throw new \RuntimeException('Erreur lors de la récupération du nom du rôle : ' . $e->getMessage());
    }
}

    // Récupérer l'UUID du rôle par son nom
    public function findRoleIdByName(string $roleName): ?string
    {
        try {
            $stmt = $this->pdo->prepare('SELECT id FROM roles WHERE name = :roleName');
            $stmt->execute(['roleName' => $roleName]);
            $role = $stmt->fetch(PDO::FETCH_ASSOC);

            return $role['id'] ?? null;
        } catch (PDOException $e) {
            throw new \RuntimeException('Erreur lors de la récupération de l\'UUID du rôle : ' . $e->getMessage());
        }
    }
}
