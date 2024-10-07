<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/schedules')]  // Préfixe ajouté
class ScheduleController extends AbstractController
{
    private ScheduleRepository $scheduleRepository;

    public function __construct(ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    #[Route('/list', name: 'get_schedules', methods: ['GET'])]  // Route principale pour lister les horaires
    public function getSchedules(): JsonResponse
    {
        $schedules = $this->scheduleRepository->findAll();
        $data = array_map(fn(Schedule $schedule) => [
            'id' => $schedule->getId(),
            'day' => $schedule->getDay(),
            'hours' => $schedule->getHours()
        ], $schedules);

        return new JsonResponse($data);
    }

    #[Route('/create', name: 'create_schedule', methods: ['POST'])]  // Route pour créer un nouvel horaire
    public function createSchedule(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['day'], $data['hours'])) {
            $schedule = new Schedule($data['day'], $data['hours']);
            $this->scheduleRepository->save($schedule);

            return new JsonResponse(['message' => 'Horaire ajouté avec succès.'], 201);
        }

        return new JsonResponse(['message' => 'Données invalides.'], 400);
    }

    #[Route('/maj/{id}', name: 'edit_schedule', methods: ['PUT'])]  // Route pour modifier un horaire existant
    public function editSchedule(string $id, Request $request): JsonResponse
    {
        $schedule = $this->scheduleRepository->findById($id);

        if (!$schedule) {
            return new JsonResponse(['message' => 'Horaire non trouvé.'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['day'], $data['hours'])) {
            $schedule->setDay($data['day']);
            $schedule->setHours($data['hours']);
            $this->scheduleRepository->save($schedule);

            return new JsonResponse(['message' => 'Horaire modifié avec succès.']);
        }

        return new JsonResponse(['message' => 'Données invalides.'], 400);
    }

    #[Route('/delete/{id}', name: 'delete_schedule', methods: ['DELETE'])]  // Route pour supprimer un horaire
    public function deleteSchedule(string $id): JsonResponse
    {
        $schedule = $this->scheduleRepository->findById($id);

        if (!$schedule) {
            return new JsonResponse(['message' => 'Horaire non trouvé.'], 404);
        }

        $this->scheduleRepository->delete($id);
        return new JsonResponse(['message' => 'Horaire supprimé avec succès.']);
    }
}
