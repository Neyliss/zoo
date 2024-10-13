<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/habitats')]
class HabitatController extends AbstractController
{
    private HabitatRepository $habitatRepository;
    private PhotoRepository $photoRepository;

    public function __construct(HabitatRepository $habitatRepository, PhotoRepository $photoRepository)
    {
        $this->habitatRepository = $habitatRepository;
        $this->photoRepository = $photoRepository;
    }

    // METHOD GET: Récupérer la liste de tous les habitats
    #[Route('/all', name: 'api_habitats', methods: ['GET'])]
    #[OA\Get(
        path: '/api/habitats/all',
        summary: "Récupérer la liste de tous les habitats",
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des habitats',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: '1'),
                            new OA\Property(property: 'name', type: 'string', example: 'Cabane dans les arbres'),
                            new OA\Property(property: 'description', type: 'string', example: 'Une cabane perchée dans les arbres, idéale pour une escapade nature.'),
                            new OA\Property(property: 'imagePath', type: 'string', example: '/images/habitats/cabane.png')
                        ]
                    )
                )
            )
        ]
    )]
    public function getAllHabitats(): JsonResponse
    {
        $habitats = $this->habitatRepository->findAll();
        return new JsonResponse($habitats);
    }

    // METHOD GET: Récupérer un habitat par son ID
    #[Route('/{id}', name: 'api_habitat_show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/habitats/{id}',
        summary: "Récupérer un habitat par son ID",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de l\'habitat à récupérer',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Détails de l\'habitat',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: '1'),
                        new OA\Property(property: 'name', type: 'string', example: 'Cabane dans les arbres'),
                        new OA\Property(property: 'description', type: 'string', example: 'Une cabane perchée dans les arbres, idéale pour une escapade nature.'),
                        new OA\Property(property: 'imagePath', type: 'string', example: '/images/habitats/cabane.png')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Habitat non trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Habitat non trouvé')
                    ]
                )
            )
        ]
    )]
    public function getHabitatById(string $id): JsonResponse
    {
        $habitat = $this->habitatRepository->findById($id);
        if (!$habitat) {
            return new JsonResponse(['error' => 'Habitat non trouvé'], 404);
        }
        return new JsonResponse($habitat);
    }

    // METHOD POST: Ajouter un nouvel habitat
    #[Route('/', name: 'api_habitat_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/habitats',
        summary: "Créer un nouvel habitat",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Cabane dans les arbres'),
                    new OA\Property(property: 'description', type: 'string', example: 'Une cabane perchée dans les arbres, idéale pour une escapade nature.'),
                    new OA\Property(property: 'imagePath', type: 'string', example: '/images/habitats/cabane.png')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Habitat créé avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Habitat créé avec succès'),
                        new OA\Property(property: 'habitat', type: 'object', properties: [
                            new OA\Property(property: 'name', type: 'string', example: 'Cabane dans les arbres')
                        ])
                    ]
                )
            )
        ]
    )]
    public function createHabitat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $habitat = new Habitat(
            null, // ID généré automatiquement
            $data['name'] ?? '',
            $data['description'] ?? null,
            $data['imagePath'] ?? null
        );

        $this->habitatRepository->save($habitat);

        return new JsonResponse(['message' => 'Habitat créé avec succès', 'habitat' => $habitat], 201);
    }

    // METHOD PUT: Modifier un habitat existant
    #[Route('/{id}', name: 'api_habitat_update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/habitats/{id}',
        summary: "Modifier un habitat existant",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de l\'habitat à modifier',
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Cabane dans les arbres'),
                    new OA\Property(property: 'description', type: 'string', example: 'Une cabane perchée dans les arbres, idéale pour une escapade nature.'),
                    new OA\Property(property: 'imagePath', type: 'string', example: '/images/habitats/cabane.png')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Habitat mis à jour avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Habitat mis à jour avec succès'),
                        new OA\Property(property: 'habitat', type: 'object', properties: [
                            new OA\Property(property: 'name', type: 'string', example: 'Cabane dans les arbres')
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Habitat non trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Habitat non trouvé')
                    ]
                )
            )
        ]
    )]
    public function updateHabitat(string $id, Request $request): JsonResponse
    {
        $habitat = $this->habitatRepository->findById($id);
        if (!$habitat) {
            return new JsonResponse(['error' => 'Habitat non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $habitat->setName($data['name'] ?? $habitat->getName())
            ->setDescription($data['description'] ?? $habitat->getDescription())
            ->setImagePath($data['imagePath'] ?? $habitat->getImagePath());

        $this->habitatRepository->save($habitat);

        return new JsonResponse(['message' => 'Habitat mis à jour avec succès', 'habitat' => $habitat]);
    }

    // METHOD DELETE: Supprimer un habitat
    #[Route('/{id}', name: 'api_habitat_delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/habitats/{id}',
        summary: "Supprimer un habitat",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de l\'habitat à supprimer',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Habitat supprimé avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Habitat supprimé avec succès')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Habitat non trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Habitat non trouvé')
                    ]
                )
            )
        ]
    )]
    public function deleteHabitat(string $id): JsonResponse
    {
        $habitat = $this->habitatRepository->findById($id);
        if (!$habitat) {
            return new JsonResponse(['error' => 'Habitat non trouvé'], 404);
        }

        $this->habitatRepository->delete($id);

        return new JsonResponse(['message' => 'Habitat supprimé avec succès']);
    }

    // METHOD POST: Ajouter une photo à un habitat
    #[Route('/{id}/photo', name: 'api_habitat_add_photo', methods: ['POST'])]
    #[OA\Post(
        path: '/api/habitats/{id}/photo',
        summary: "Ajouter une photo à un habitat",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de l\'habitat',
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Fichier image à ajouter à l\'habitat',
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'photo', type: 'string', format: 'binary', description: 'Le fichier image à uploader')
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
                        new OA\Property(property: 'message', type: 'string', example: 'Photo ajoutée à l\'habitat'),
                        new OA\Property(property: 'photo', type: 'object', properties: [
                            new OA\Property(property: 'path', type: 'string', example: '/images/habitats/cabane-photo1.png')
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Erreur de validation',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Image requise')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Habitat non trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Habitat non trouvé')
                    ]
                )
            )
        ]
    )]
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

        $photo = $this->photoRepository->save($id, $imagePath, 'habitat');
        
        $habitat->setPhoto($photo);
        $this->habitatRepository->save($habitat);

        return new JsonResponse(['message' => 'Photo ajoutée à l\'habitat', 'photo' => $photo], 201);
    }
}
