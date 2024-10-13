<?php

namespace App\Entity;

class Habitat
{
    private ?string $id;
    private string $name;
    private ?string $description;
    private ?string $imagePath;
    private ?Photo $photo = null;

    public function __construct(?string $id = null, string $name, ?string $description = null, ?string $imagePath = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->imagePath = $imagePath;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): string
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;
        return $this;
    }

    public function setPhoto(?Photo $photo): void
    {
        $this->photo = $photo;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }
}
