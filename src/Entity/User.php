<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    private ?int $id = null;
    private ?string $email = null;
    private ?string $password = null;
    private ?string $roles = null;
    private ?string $apitoken = null;

    public function __construct(?int $id, string $email, string $password, array $roles, ?string $token = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        // Si aucun token n'est fourni, on en génère un automatiquement
        $this->apitoken = $token ?? bin2hex(random_bytes(20));
    }

    // Getter pour l'ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et Setter pour l'email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    // Getter et Setter pour le mot de passe
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    // Getter et Setter pour le rôle de l'utilisateur
    public function getRole(): ?string
    {
        return $this->roles;
    }

    public function setRoleId(string $roles): void
    {
        $this->roles= $roles;
    }

    // Getter et Setter pour le token d'API
    public function getApiToken(): ?string
    {
        return $this->apitoken;
    }

    public function setApiToken(?string $token): void
    {
        $this->apitoken = $token;
    }

    // Génération d'un nouveau token d'API
    public function regenerateApiToken(): void
    {
        $this->apitoken = bin2hex(random_bytes(20));
    }

    // Symfony UserInterface methods
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles= $this->roles;
        $roles[] ='ROLE_USER';
        return [$this->roles];
        
    }

    public function getSalt(): ?string
    {
        return null; // No salt is needed for modern password hashing algorithms
    }

    public function eraseCredentials(): void
    {
        // Si des données sensibles sont stockées, on les efface
        $this->password = null;
    }
}


