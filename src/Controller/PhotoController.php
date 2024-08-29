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
    private const UPLOAD_DIRECTORY = 'public/uploads/photos'; // Répertoire pour stocker les photos

    /**
     * @Route("/api/photos", name="api_photos", methods={"POST", "PUT"})
     */
    public function savePhoto(Request $request): JsonResponse
    {
        $filesystem = new Filesystem();
        $files = $request->files;
        $title = $request->request->get('Titre');
        $image = $files->get('Image');

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
            $imageUrl = ''; // Cas où aucune image n'est fournie
        }

        // Stockage des données (titre et URL de l'image) - Simuler la sauvegarde
        // Vous pouvez sauvegarder les données dans une base de données ou un fichier JSON
        // Exemple : ['title' => $title, 'image' => $imageUrl]

        return new JsonResponse(['message' => 'Photo sauvegardée', 'imageUrl' => $imageUrl], Response::HTTP_OK);
    }

    /**
     * @Route("/api/photos", name="api_delete_photo", methods={"DELETE"})
     */
    public function deletePhoto(Request $request): JsonResponse
    {
        $title = $request->request->get('title');
        // Récupérer l'URL de l'image à partir du titre, ceci est un exemple simplifié
        $imagePath = self::UPLOAD_DIRECTORY . '/' . $title;

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
