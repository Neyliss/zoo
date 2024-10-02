<?php

namespace App\Entity;

class Photo
{
    private string $id;
    private string $path;
    private ?string $animalId;
    private ?string $habitatId;
    private ?string $offerId;

    public function __construct(string $id, string $path, ?string $animalId = null, ?string $habitatId = null, ?string $offerId = null)
    {
        $this->id = $id;
        $this->path = $path;
        $this->animalId = $animalId;
        $this->habitatId = $habitatId;
        $this->offerId = $offerId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    // Ajout de la méthode setPath
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getAnimalId(): ?string
    {
        return $this->animalId;
    }

    public function getHabitatId(): ?string
    {
        return $this->habitatId;
    }

    public function getOfferId(): ?string
    {
        return $this->offerId;
    }

    public function setAnimalId(?string $animalId): void
    {
        $this->animalId = $animalId;
    }

    public function setHabitatId(?string $habitatId): void
    {
        $this->habitatId = $habitatId;
    }

    public function setOfferId(?string $offerId): void
    {
        $this->offerId = $offerId;
    }
}
