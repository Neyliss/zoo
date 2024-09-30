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

class PhotoController extends AbstractController
{
    private const UPLOAD_DIRECTORY = 'public/uploads/photos'; 

    #[Route('/api/photos', name: 'api_add_photo', methods: ['POST'])]
    public function savePhoto(Request $request): JsonResponse
    {
        $filesystem = new Filesystem();
        $files = $request->files;
        $title = $request->request->get('title');
        $image = $files->get('image');
        $habitatId = $request->request->get('habitatId');
        $animalId = $request->request->get('animalId');
        $nosOffres = $request->request->get('offers'); // IDs des offres

        if (!$title) {
            return new JsonResponse(['message' => 'Le titre est requis'], Response::HTTP_BAD_REQUEST);
        }

        if ($image) {
            if (!$image instanceof UploadedFile) {
                return new JsonResponse(['message' => 'Fichier invalide'], Response::HTTP_BAD_REQUEST);
            }

            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower();', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

            try {
                $image->move(self::UPLOAD_DIRECTORY, $newFilename);
            } catch (IOExceptionInterface $exception) {
                return new JsonResponse(['message' => 'Erreur lors de l\'upload de l\'image'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $imageUrl = '/uploads/photos/' . $newFilename;
        } else {
            $imageUrl = ''; 
        }

        // Simuler la sauvegarde dans la base de données avec les relations
        // Associer la photo à l'habitat, à l'animal et aux offres

        return new JsonResponse(['message' => 'Photo sauvegardée', 'imageUrl' => $imageUrl], Response::HTTP_OK);
    }

    #[Route('/api/photos/{id}', name: 'api_delete_photo', methods: ['DELETE'])]
    public function deletePhoto(int $id): JsonResponse
    {
        // Simuler la récupération et suppression de la photo par son ID
        $imagePath = self::UPLOAD_DIRECTORY . '/photo' . $id; // Exemple d'URL

        $filesystem = new Filesystem();
        if ($filesystem->exists($imagePath)) {
            try {
                $filesystem->remove($imagePath);
                return new JsonResponse(['message' => 'Photo supprimée'], Response::HTTP_OK);
            } catch (IOExceptionInterface $exception) {
                return new JsonResponse(['message' => 'Erreur lors de la suppression de la photo'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return new JsonResponse(['message' => 'Photo non trouvée'], Response::HTTP_NOT_FOUND);
        }
    }
}
