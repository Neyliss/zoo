<?php

namespace App\Entity;

class Animal
{
    private $id;
    private $name;
    private $race;
    private $habitatId;

    public function __construct(string $id, string $name, string $race, string $habitatId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->race = $race;
        $this->habitatId = $habitatId;
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
