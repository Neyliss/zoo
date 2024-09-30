<?php

namespace App\Controller;

use App\Repository\HabitatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HabitatController extends AbstractController
{
    private HabitatRepository $habitatRepository;

    public function __construct(HabitatRepository $habitatRepository)
    {
        $this->habitatRepository = $habitatRepository;
    }

    #[Route('/api/habitats', name: 'api_habitats', methods: ['GET'])]
    public function getAllHabitats(): JsonResponse
    {
        $habitats = $this->habitatRepository->findAll();
        return $this->json($habitats);
    }

    #[Route('/api/habitats/{id}', name: 'api_habitat_show', methods: ['GET'])]
    public function getHabitatById(string $id): JsonResponse
    {
        $habitat = $this->habitatRepository->findById($id);
        if (!$habitat) {
            return $this->json(['error' => 'Habitat not found'], 404);
        }
        return $this->json($habitat);
    }
}
