<?php

namespace App\Entity;

class Animal
{
    private $id;
    private $name;
    private $race;
    private $habitatId;
    private $imagePath; // Ajout de la propriété image

    public function __construct(string $id, string $name, string $race, string $habitatId, ?string $imagePath = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->race = $race;
        $this->habitatId = $habitatId;
        $this->imagePath = $imagePath; // Initialisation de l'image
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRace(): string
    {
        return $this->race;
    }

    public function getHabitatId(): string
    {
        return $this->habitatId;
    }

    public function getImagePath(): ?string // Ajout du getter pour l'image
    {
        return $this->imagePath;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setRace(string $race): void
    {
        $this->race = $race;
    }

    public function setHabitatId(string $habitatId): void
    {
        $this->habitatId = $habitatId;
    }

    public function setImagePath(?string $imagePath): void // Ajout du setter pour l'image
    {
        $this->imagePath = $imagePath;
    }
}
