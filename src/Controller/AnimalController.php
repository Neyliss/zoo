<?php

namespace App\Controller;

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
    private const UPLOAD_DIRECTORY = 'public/images/animals'; // Répertoire pour stocker les images des animaux
    private const DATA_FILE = 'data/animals.json'; // Fichier JSON pour stocker les informations des animaux

    /**
     * @Route("/api/animals", name="api_get_animals", methods={"GET"})
     */
    public function getAnimals(): JsonResponse
    {
        $filePath = self::DATA_FILE;
        if (file_exists($filePath)) {
            $data = file_get_contents($filePath);
            $animals = json_decode($data, true);
            return new JsonResponse($animals, Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Aucun animal trouvé'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/api/animals", name="api_add_animal", methods={"POST"})
     */
    public function addAnimal(Request $request): JsonResponse
    {
        $filesystem = new Filesystem();
        $files = $request->files;
        $name = $request->request->get('name');
        $prenom = $request->request->get('prenom');
        $race = $request->request->get('race');
        $habitat = $request->request->get('habitat');
        $image = $files->get('image');

        if (!$name || !$prenom || !$race || !$habitat) {
            return new JsonResponse(['message' => 'Tous les champs sont requis'], Response::HTTP_BAD_REQUEST);
        }

        $animalData = [
            'name' => $name,
            'prenom' => $prenom,
            'race' => $race,
            'habitat' => $habitat,
            'image' => '',
        ];

        if ($image) {
            if (!$image instanceof UploadedFile) {
                return new JsonResponse(['message' => 'Fichier invalide'], Response::HTTP_BAD_REQUEST);
            }

            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower();', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

            try {
                $image->move(self::UPLOAD_DIRECTORY, $newFilename);
                $animalData['image'] = '/images/animals/' . $newFilename;
            } catch (IOExceptionInterface $exception) {
                return new JsonResponse(['message' => 'Erreur lors de l\'upload de l\'image'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $existingData = [];
        if (file_exists(self::DATA_FILE)) {
            $existingData = json_decode(file_get_contents(self::DATA_FILE), true);
        }

        $existingData[] = $animalData;

        try {
            file_put_contents(self::DATA_FILE, json_encode($existingData));
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la sauvegarde des données'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Animal ajouté', 'animal' => $animalData], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/animals/{name}", name="api_delete_animal", methods={"DELETE"})
     */
    public function deleteAnimal(string $name): JsonResponse
    {
        $filePath = self::DATA_FILE;
        if (!file_exists($filePath)) {
            return new JsonResponse(['message' => 'Aucun animal trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode(file_get_contents($filePath), true);
        $updatedData = array_filter($data, function ($animal) use ($name) {
            return $animal['name'] !== $name;
        });

        if (count($data) === count($updatedData)) {
            return new JsonResponse(['message' => 'Animal non trouvé'], Response::HTTP_NOT_FOUND);
        }

        try {
            file_put_contents($filePath, json_encode(array_values($updatedData)));
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la suppression des données'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Animal supprimé'], Response::HTTP_OK);
    }
}
