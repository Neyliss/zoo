<?php

namespace App\Entity;

class Schedule
{
    private int $id;
    private string $day;
    private string $hours;

    public function __construct(string $day, string $hours, ?int $id = null)
    {
        $this->id = $id ?? 0;
        $this->day = $day;
        $this->hours = $hours;
    }

    public function getId(): int
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
