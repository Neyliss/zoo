<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HabitatController extends AbstractController
{
    private HabitatRepository $habitatRepository;
    private PhotoRepository $photoRepository;

    public function __construct(HabitatRepository $habitatRepository, PhotoRepository $photoRepository)
    {
        $this->habitatRepository = $habitatRepository;
        $this->photoRepository = $photoRepository;
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
            return $this->json(['error' => 'Habitat non trouvé'], 404);
        }
        return $this->json($habitat);
    }

    #[Route('/api/habitats/{id}/photo', name: 'api_habitat_add_photo', methods: ['POST'])]
    public function addPhotoToHabitat(string $id, Request $request): JsonResponse
    {
        $habitat = $this->habitatRepository->findById($id);
        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat non trouvé'], 404);
        }

        $image = $request->files->get('photo');
        if (!$image || !$image instanceof UploadedFile) {
            return new JsonResponse(['message' => 'Image requise'], 400);
        }

        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
        $image->move('public/images/habitats', $newFilename);

        $imagePath = '/images/habitats/' . $newFilename;

        // Enregistrer la photo et retourner un objet Photo
        $photo = $this->photoRepository->save($id, $imagePath, 'habitat');
        
        // Associer l'objet Photo à l'habitat
        $habitat->setPhoto($photo);
        
        // Sauvegarder les changements de l'habitat
        $this->habitatRepository->save($habitat);

        return new JsonResponse(['message' => 'Photo ajoutée à l\'habitat', 'photo' => $photo], 201);
    }
}
