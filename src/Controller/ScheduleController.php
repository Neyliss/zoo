<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/schedules')]
class ScheduleController extends AbstractController
{
    private ScheduleRepository $scheduleRepository;

    public function __construct(ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    #[OA\Get(
        path: '/api/schedules/list',
        summary: 'Récupérer la liste des horaires',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des horaires',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: 'uuid'),
                            new OA\Property(property: 'day', type: 'string', example: 'Lundi'),
                            new OA\Property(property: 'hours', type: 'string', example: '09:00-17:00')
                        ]
                    )
                )
            )
        ]
    )]
    #[Route('/list', name: 'get_schedules', methods: ['GET'])]
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

    #[OA\Post(
        path: '/api/schedules/create',
        summary: 'Créer un nouvel horaire',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'day', type: 'string', example: 'Lundi'),
                    new OA\Property(property: 'hours', type: 'string', example: '09:00-17:00')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Horaire ajouté avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Horaire ajouté avec succès.')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Données invalides',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Données invalides.')
                    ]
                )
            )
        ]
    )]
    #[Route('/create', name: 'create_schedule', methods: ['POST'])]
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

    #[OA\Put(
        path: '/api/schedules/maj/{id}',
        summary: 'Modifier un horaire existant',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'day', type: 'string', example: 'Lundi'),
                    new OA\Property(property: 'hours', type: 'string', example: '10:00-18:00')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Horaire modifié avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Horaire modifié avec succès.')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Horaire non trouvé',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Horaire non trouvé.')
                    ]
                )
            )
        ]
    )]
    #[Route('/maj/{id}', name: 'edit_schedule', methods: ['PUT'])]
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

    #[OA\Delete(
        path: '/api/schedules/delete/{id}',
        summary: 'Supprimer un horaire',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Horaire supprimé avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Horaire supprimé avec succès.')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Horaire non trouvé',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Horaire non trouvé.')
                    ]
                )
            )
        ]
    )]
    #[Route('/delete/{id}', name: 'delete_schedule', methods: ['DELETE'])]
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
