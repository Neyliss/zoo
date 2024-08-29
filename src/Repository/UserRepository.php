<?php

namespace App\Repository;

use App\Entity\User;
use PDO;

class UserRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM users');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($user) {
            return new User(
                $user['id'],
                $user['email'],
                $user['password'],
                $user['role_id'],
                $user['token'] // Assurez-vous que la colonne token existe dans votre table
            );
        }, $users);
    }

    public function findById($id): ?User
    {
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
    }

    public function findByEmail($email): ?User
    {
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
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (id, email, password, role_id, token) VALUES (:id, :email, :password, :role_id, :token)'
        );
        $stmt->execute([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role_id' => $user->getRoleId(),
            'token' => $user->getToken(),
        ]);
    }

    public function update(User $user): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users SET email = :email, password = :password, role_id = :role_id, token = :token WHERE id = :id'
        );
        $stmt->execute([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role_id' => $user->getRoleId(),
            'token' => $user->getToken(),
        ]);
    }

    public function delete($id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
