<?php

namespace App\Entity;

class Schedule
{
    private string $id;  
    private string $day;
    private string $hours;

    public function __construct(string $day, string $hours, ?string $id = null)
    {
        $this->id = $id ?? '';  // On laisse l'ID vide si non fourni
        $this->day = $day;
        $this->hours = $hours;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function setDay(string $day): void
    {
        $this->day = $day;
    }

    public function getHours(): string
    {
        return $this->hours;
    }

    public function setHours(string $hours): void
    {
        $this->hours = $hours;
    }
}
