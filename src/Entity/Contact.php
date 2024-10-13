<?php

namespace App\Entity;

class Contact
{
    private ?string $id;
    private string $titre;
    private string $email;
    private string $description;

    public function __construct(?string $id, string $titre, string $email, string $description)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->email = $email;
        $this->description = $description;
    }

    // Getters and setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
