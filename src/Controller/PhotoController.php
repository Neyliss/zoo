<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use App\Repository\PhotoRepository;



#[Route('/api/photos')]
class PhotoController extends AbstractController
{
    private const UPLOAD_DIRECTORY = 'public/uploads/photos'; 

    private PhotoRepository $photoRepository;

    public function __construct(PhotoRepository $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }



    // METHOD POST 
    #[Route('/add', name: 'api_photos_add', methods: ['POST'])]
    #[OA\Post(
        path: '/api/photos/add',
        summary: "Ajout d'une photo",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données pour ajouter une photo",
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Fichier image à télécharger'),
                        new OA\Property(property: 'animalId', type: 'string', example: 'UUID de l\'animal lié (facultatif)'),
                        new OA\Property(property: 'habitatId', type: 'string', example: 'UUID de l\'habitat lié (facultatif)'),
                        new OA\Property(property: 'offerId', type: 'string', example: 'UUID de l\'offre liée (facultatif)')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Photo ajoutée avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Photo ajoutée avec succès'),
                        new OA\Property(property: 'photo', type: 'string', example: 'URL de la photo')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Données invalides'
            )
        ]
    )]
    

    public function addPhoto(Request $request): JsonResponse
    {
        $files = $request->files;
        $image = $files->get('image');
        $animalId = $request->request->get('animalId');
        $habitatId = $request->request->get('habitatId');
        $offerId = $request->request->get('offerId');
    
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
            $entityId = $animalId ?? $habitatId ?? $offerId;
            $entityType = $animalId ? 'animal' : ($habitatId ? 'habitat' : 'offer');

            $photo = $this->photoRepository->save($entityId, $imageUrl, $entityType);

            return new JsonResponse(['message' => 'Photo ajoutée', 'photo' => $photo], Response::HTTP_CREATED);
        }
    
        return new JsonResponse(['message' => 'Fichier image non valide'], Response::HTTP_BAD_REQUEST);
    }


    // METHOD GET 

    #[Route('/acces/{id}', name: 'api_photos_get', methods: ['GET'])]
    #[OA\Get(
        path: '/api/photos/acces/{id}',
        summary: "Récupérer une photo",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de la photo à récupérer',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Détails de la photo',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: 'UUID de la photo'),
                        new OA\Property(property: 'path', type: 'string', example: '/uploads/photos/photo.jpg')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Photo non trouvée'
            )
        ]
    )]    


    public function getPhoto(string $id): JsonResponse
    {
        $photo = $this->photoRepository->find($id);

        if (!$photo) {
            return new JsonResponse(['message' => 'Photo non trouvée'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($photo, Response::HTTP_OK);
    }

    //METHOD PUT
    #[Route('/maj/{id}', name: 'api_photos_update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/photos/maj/{id}',
        summary: "Mise à jour d'une photo",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de la photo à mettre à jour',
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            required: false,
            description: "Nouvelles données pour la photo",
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'animalId', type: 'string', example: 'UUID de l\'animal lié (facultatif)'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Nouvelle image (facultatif)')
                    ]
                )
            )
        ),        
        responses: [
            new OA\Response(
                response: 200,
                description: 'Photo mise à jour avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Photo mise à jour avec succès')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Photo non trouvée'
            )
        ]
    )]
    


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

            $oldImageUrl = $photo->getPath();
            if ($oldImageUrl && file_exists(self::UPLOAD_DIRECTORY . '/' . basename($oldImageUrl))) {
                unlink(self::UPLOAD_DIRECTORY . '/' . basename($oldImageUrl));
            }

            $photo->setPath('/uploads/photos/' . $newFilename);
            $entityId = $photo->getAnimalId() ?? $photo->getHabitatId() ?? $photo->getOfferId();
            $entityType = $photo->getAnimalId() ? 'animal' : ($photo->getHabitatId() ? 'habitat' : 'offer');

            $this->photoRepository->save($entityId, $photo->getPath(), $entityType);

            return new JsonResponse(['message' => 'Photo mise à jour', 'photo' => $photo], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Fichier image non valide'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/delete/{id}', name: 'api_photos_delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/photos/delete/{id}',
        summary: 'Supprimer une photo',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: "ID de la photo à supprimer",
                schema: new OA\Schema(type: 'string', example: 'photo_id')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Photo supprimée avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Photo supprimée')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Photo non trouvée',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Photo non trouvée')
                    ]
                )
            )
        ]
    )]

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
