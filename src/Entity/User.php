<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    private $id;
    private $email;
    private $password;
    private $roleId;
    private $token;

    public function __construct($id, $email, $password, $roleId, $token = null)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roleId = $roleId;
        $this->token = $token = bin2hex(random_bytes(20));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoleId(): string
    {
        return $this->roleId;
    }

    public function setRoleId(string $roleId): void
    {
        $this->roleId = $roleId;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return [$this->roleId];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // Clear sensitive data, if stored
    }
}
