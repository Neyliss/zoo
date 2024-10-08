<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

#[Route('/api/animals')] // Préfixe ajouté pour toutes les routes de la classe
class AnimalController extends AbstractController
{
    private AnimalRepository $animalRepository;
    private PhotoRepository $photoRepository;
    private const UPLOAD_DIRECTORY = 'public/images/animals';

    public function __construct(AnimalRepository $animalRepository, PhotoRepository $photoRepository)
    {
        $this->animalRepository = $animalRepository;
        $this->photoRepository = $photoRepository;
    }

    #[Route('', name: 'api_get_animals', methods: ['GET'])] // Route pour GET sans répétition
    public function getAnimals(): JsonResponse
    {
        $animals = $this->animalRepository->findAll();
        return new JsonResponse($animals, Response::HTTP_OK);
    }

    #[Route('', name: 'api_add_animal', methods: ['POST'])] // Route pour POST sans répétition
    public function addAnimal(Request $request): JsonResponse
    {
        $files = $request->files;
        $name = $request->request->get('name');
        $race = $request->request->get('race');
        $habitat = $request->request->get('habitat');
        $images = $files->get('images'); // Gestion de plusieurs images

        if (!$name || !$race || !$habitat) {
            return new JsonResponse(['message' => 'Tous les champs sont requis'], Response::HTTP_BAD_REQUEST);
        }

        $animal = new Animal(uniqid(), $name, $race, $habitat);

        $imagePaths = [];
        if ($images) {
            foreach ($images as $image) {
                if ($image instanceof UploadedFile) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                    try {
                        $image->move(self::UPLOAD_DIRECTORY, $newFilename);
                        $imagePath = '/images/animals/' . $newFilename;

                        // Enregistrement de la photo en base de données
                        $photo = $this->photoRepository->save($animal->getId(), $imagePath);
                        $animal->addPhoto($photo); // Lien entre animal et photo
                        $imagePaths[] = $imagePath;
                    } catch (IOExceptionInterface $exception) {
                        return new JsonResponse(['message' => 'Erreur lors de l\'upload de l\'image'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
            }
        }

        $this->animalRepository->save($animal);

        return new JsonResponse(['message' => 'Animal ajouté', 'animal' => $animal, 'images' => $imagePaths], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_delete_animal', methods: ['DELETE'])] // Route pour DELETE avec ID
    public function deleteAnimal(string $id): JsonResponse
    {
        $animal = $this->animalRepository->findById($id);

        if (!$animal) {
            return new JsonResponse(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }

        foreach ($animal->getPhotos() as $photo) {
            $this->photoRepository->delete($photo->getId());
        }

        $this->animalRepository->delete($id);

        return new JsonResponse(['message' => 'Animal supprimé'], Response::HTTP_OK);
    }
}
