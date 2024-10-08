<?php

namespace App\Entity;

class Animal
{
    private string $id;
    private string $name;
    private string $race;
    private string $habitatId;
    private ?string $imagePath = null; // Ajout de la propriété pour l'image principale
    private array $photos = []; // Tableau des photos supplémentaires

    public function __construct(string $id, string $name, string $race, string $habitatId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->race = $race;
        $this->habitatId = $habitatId;
    }

    public function addPhoto(Photo $photo): void
    {
        $this->photos[] = $photo;
    }

    public function getPhotos(): array
    {
        return $this->photos;
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

    public function getImagePath(): ?string // Getter pour l'image principale
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): void // Setter pour l'image principale
    {
        $this->imagePath = $imagePath;
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
}
