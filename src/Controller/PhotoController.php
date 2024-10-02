<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use App\Repository\PhotoRepository;

class PhotoController extends AbstractController
{
    private const UPLOAD_DIRECTORY = 'public/uploads/photos'; 

    private PhotoRepository $photoRepository;

    public function __construct(PhotoRepository $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }

    #[Route('/api/photos', name: 'api_photos_add', methods: ['POST'])]
    public function addPhoto(Request $request): JsonResponse
    {
        $files = $request->files;
        $image = $files->get('image');
        $animalId = $request->request->get('animalId');
        $habitatId = $request->request->get('habitatId'); // Optionnel
        $offerId = $request->request->get('offerId');     // Optionnel
    
        if (!$image) {
            return new JsonResponse(['message' => 'Image requise'], Response::HTTP_BAD_REQUEST);
        }
    
        if ($image instanceof UploadedFile) {
            if (!$this->isValidImage($image)) {
                return new JsonResponse(['message' => 'Fichier image non valide'], Response::HTTP_BAD_REQUEST);
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

            // Déterminer l'ID et le type d'entité (animal, habitat ou offre)
            $entityId = $animalId ?? $habitatId ?? $offerId;
            $entityType = $animalId ? 'animal' : ($habitatId ? 'habitat' : 'offer');

            // Appel à la méthode save avec les bons arguments
            $photo = $this->photoRepository->save($entityId, $imageUrl, $entityType);

            return new JsonResponse(['message' => 'Photo ajoutée', 'photo' => $photo], Response::HTTP_CREATED);
        }
    
        return new JsonResponse(['message' => 'Fichier image non valide'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/photos/{id}', name: 'api_photos_get', methods: ['GET'])]
    public function getPhoto(string $id): JsonResponse
    {
        $photo = $this->photoRepository->find($id);

        if (!$photo) {
            return new JsonResponse(['message' => 'Photo non trouvée'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($photo, Response::HTTP_OK);
    }

    #[Route('/api/photos/{id}', name: 'api_photos_update', methods: ['PUT'])]
    public function updatePhoto(string $id, Request $request): JsonResponse
    {
        $photo = $this->photoRepository->find($id);

        if (!$photo) {
            return new JsonResponse(['message' => 'Photo non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $animalId = $request->request->get('animalId');
        $image = $request->files->get('image');

        if ($animalId) {
            $photo->setAnimalId($animalId);
        }

        if ($image && $image instanceof UploadedFile) {
            if (!$this->isValidImage($image)) {
                return new JsonResponse(['message' => 'Fichier image non valide'], Response::HTTP_BAD_REQUEST);
            }

            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower();', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

            try {
                $image->move(self::UPLOAD_DIRECTORY, $newFilename);
            } catch (IOExceptionInterface $exception) {
                return new JsonResponse(['message' => 'Erreur lors de l\'upload de l\'image'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Supprimer l'ancienne image
            $oldImageUrl = $photo->getPath();
                if ($oldImageUrl && file_exists(self::UPLOAD_DIRECTORY . '/' . basename($oldImageUrl))) {
                     unlink(self::UPLOAD_DIRECTORY . '/' . basename($oldImageUrl));
                        }

                    $photo->setPath('/uploads/photos/' . $newFilename);

                    // Récupérer l'ID et le type d'entité
                $entityId = $photo->getAnimalId() ?? $photo->getHabitatId() ?? $photo->getOfferId();
                $entityType = $photo->getAnimalId() ? 'animal' : ($photo->getHabitatId() ? 'habitat' : 'offer');

                // Appel à la méthode save avec les bons arguments
                $this->photoRepository->save($entityId, $photo->getPath(), $entityType);

                return new JsonResponse(['message' => 'Photo mise à jour', 'photo' => $photo], Response::HTTP_OK);

    }
}

    #[Route('/api/photos/{id}', name: 'api_photos_delete', methods: ['DELETE'])]
    public function deletePhoto(string $id): JsonResponse
    {
        $photo = $this->photoRepository->find($id);

        if (!$photo) {
            return new JsonResponse(['message' => 'Photo non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $imageUrl = $photo->getPath();
        if ($imageUrl && file_exists(self::UPLOAD_DIRECTORY . '/' . basename($imageUrl))) {
            unlink(self::UPLOAD_DIRECTORY . '/' . basename($imageUrl));
        }

        $this->photoRepository->delete($id);

        return new JsonResponse(['message' => 'Photo supprimée'], Response::HTTP_OK);
    }

    private function isValidImage(UploadedFile $image): bool
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        return in_array($image->getMimeType(), $allowedMimeTypes);
    }
}
