<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    private ?string $id = null;
    private ?string $email = null;
    private ?string $password = null;
    private array $roles = [];
    private ?string $apiToken = null;

    public function __construct(?string $id, string $email, string $password, array $roles = ['ROLE_USER'], ?string $apiToken = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roles = $roles;
        $this->apiToken = $apiToken ?? bin2hex(random_bytes(20));
    }

    // ID
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    // Email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        // You could add a basic validation for email format here
        $this->email = $email;
    }

    // Password
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        // Ensure to encode the password before storing it
        $this->password = $password;
    }

    // Roles
    public function getRoles(): array
    {
        if (!in_array('ROLE_USER', $this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    // Api Token
    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): void
    {
        $this->apiToken = $apiToken;
    }

    // Symfony UserInterface methods
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary sensitive data, clear it here
    }
}
