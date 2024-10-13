<?php

namespace App\Repository;

use App\Entity\Schedule;
use PDO;

class ScheduleRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $stmt = $this->connection->query('SELECT * FROM schedules');
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($schedule) => new Schedule($schedule['day'], $schedule['hours'], (string)$schedule['id']), $schedules);
    }

    public function findById(string $id): ?Schedule
    {
        $stmt = $this->connection->prepare('SELECT * FROM schedules WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

        return $schedule ? new Schedule($schedule['day'], $schedule['hours'], (string)$schedule['id']) : null;
    }

    public function save(Schedule $schedule): void
    {
        if (empty($schedule->getId())) {
            $stmt = $this->connection->prepare('INSERT INTO schedules (day, hours) VALUES (:day, :hours)');
            $stmt->execute(['day' => $schedule->getDay(), 'hours' => $schedule->getHours()]);
        } else {
            $stmt = $this->connection->prepare('UPDATE schedules SET day = :day, hours = :hours WHERE id = :id');
            $stmt->execute([
                'id' => $schedule->getId(),
                'day' => $schedule->getDay(),
                'hours' => $schedule->getHours()
            ]);
        }
    }

    public function delete(string $id): void
    {
        $stmt = $this->connection->prepare('DELETE FROM schedules WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
