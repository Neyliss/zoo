<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class AnimalController extends AbstractController
{
    private AnimalRepository $animalRepository;
    private const UPLOAD_DIRECTORY = 'public/images/animals'; // Répertoire pour stocker les images des animaux

    public function __construct(AnimalRepository $animalRepository)
    {
        $this->animalRepository = $animalRepository;
    }

    #[Route('/api/animals', name: 'api_get_animals', methods: ['GET'])]
    public function getAnimals(): JsonResponse
    {
        $animals = $this->animalRepository->findAll();
        return new JsonResponse($animals, Response::HTTP_OK);
    }

    #[Route('/api/animals', name: 'api_add_animal', methods: ['POST'])]
    public function addAnimal(Request $request): JsonResponse
    {
        $filesystem = new Filesystem();
        $files = $request->files;
        $name = $request->request->get('name');
        $race = $request->request->get('race');
        $habitat = $request->request->get('habitat');
        $image = $files->get('image');

        if (!$name || !$race || !$habitat) {
            return new JsonResponse(['message' => 'Tous les champs sont requis'], Response::HTTP_BAD_REQUEST);
        }

        // Gestion de l'image
        $imagePath = '';
        if ($image && $image instanceof UploadedFile) {
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower();', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

            try {
                $image->move(self::UPLOAD_DIRECTORY, $newFilename);
                $imagePath = '/images/animals/' . $newFilename;
            } catch (IOExceptionInterface $exception) {
                return new JsonResponse(['message' => 'Erreur lors de l\'upload de l\'image'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        // Création d'un nouvel objet Animal
        $animal = new Animal(uniqid(), $name, $race, $habitat, $imagePath);

        // Sauvegarde en base de données
        $this->animalRepository->save($animal);

        return new JsonResponse(['message' => 'Animal ajouté', 'animal' => $animal], Response::HTTP_CREATED);
    }

    #[Route('/api/animals/{id}', name: 'api_delete_animal', methods: ['DELETE'])]
    public function deleteAnimal(string $id): JsonResponse
    {
        $animal = $this->animalRepository->findById($id);
    
        if (!$animal) {
            return new JsonResponse(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        // Suppression de l'animal en passant son ID
        $this->animalRepository->delete($id);
    
        return new JsonResponse(['message' => 'Animal supprimé'], Response::HTTP_OK);
    }
    
}
