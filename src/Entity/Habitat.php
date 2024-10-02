<?php

namespace App\Entity;

class Habitat
{
    private ?string $id = null;
    private string $name;
    private ?string $description = null;
    private ?string $imagePath = null;
    private ?Photo $photo = null; // Propriété photo

    // Getter et setter pour photo
    public function setPhoto(?Photo $photo): void
    {
        $this->photo = $photo;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    // Getters et setters pour les autres propriétés
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }
}
