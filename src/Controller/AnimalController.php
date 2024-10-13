<?php

namespace App\Controller;

use App\Service\MongoService;
use App\Entity\Animal;
use App\Repository\AnimalRepository;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

#[Route('/api/animals')]
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


    //METHOD GET 
    #[Route('/animaux', name: 'api_get_animals', methods: ['GET'])]
    #[OA\Get(
        path: '/api/animals',
        summary: 'Récupérer tous les animaux',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des animaux',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: '123e4567-e89b-12d3-a456-426614174000'),
                            new OA\Property(property: 'name', type: 'string', example: 'Lion'),
                            new OA\Property(property: 'race', type: 'string', example: 'Panthera leo'),
                            new OA\Property(property: 'habitatId', type: 'string', example: '456e7890-e89b-12d3-a456-426614174001'),
                            new OA\Property(property: 'imagePath', type: 'string', example: '/images/animals/Lion.jpg')
                        ]
                    )
                )
            )
        ]
    )]
    

    public function getAnimals(): JsonResponse
    {
        $animals = $this->animalRepository->findAll();
        return new JsonResponse($animals, Response::HTTP_OK);
    }


   // METHOD POST

   #[Route('/add', name: 'api_add_animal', methods: ['POST'])]
   #[OA\Post(
       path: '/api/animals',
       summary: 'Ajouter un nouvel animal',
       requestBody: new OA\RequestBody(
           required: true,
           description: 'Données de l\'animal à ajouter',
           content: new OA\MediaType(
               mediaType: "multipart/form-data",
               schema: new OA\Schema(
                   type: 'object',
                   properties: [
                       new OA\Property(property: 'name', type: 'string', example: 'Lion'),
                       new OA\Property(property: 'race', type: 'string', example: 'Panthera leo'),
                       new OA\Property(property: 'habitat', type: 'string', example: '123e4567-e89b-12d3-a456-426614174000'),
                       new OA\Property(
                           property: 'images',
                           type: 'array',
                           items: new OA\Items(type: 'string', format: 'binary')
                       )
                   ]
               )
           )
       ),
       responses: [
           new OA\Response(
               response: 201,
               description: 'Animal ajouté avec succès',
               content: new OA\JsonContent(
                   type: 'object',
                   properties: [
                       new OA\Property(property: 'message', type: 'string', example: 'Animal ajouté'),
                       new OA\Property(
                           property: 'animal',
                           type: 'object',
                           properties: [
                               new OA\Property(property: 'id', type: 'string', example: '123e4567-e89b-12d3-a456-426614174000'),
                               new OA\Property(property: 'name', type: 'string', example: 'Lion'),
                               new OA\Property(property: 'race', type: 'string', example: 'Panthera leo'),
                               new OA\Property(property: 'habitatId', type: 'string', example: '456e7890-e89b-12d3-a456-426614174001'),
                               new OA\Property(property: 'imagePath', type: 'string', example: '/images/animals/lion.jpg')
                           ]
                       ),
                       new OA\Property(
                           property: 'images',
                           type: 'array',
                           items: new OA\Items(type: 'string', example: '/images/animals/lion-1.jpg')
                       )
                   ]
               )
           )
       ]
   )]
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
   

    //METHOD DELETE
    
    #[Route('/{id}', name: 'api_delete_animal', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/animals/{id}',
        summary: 'Supprimer un animal',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Identifiant unique de l\'animal',
                schema: new OA\Schema(type: 'string', example: '123e4567-e89b-12d3-a456-426614174000')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Animal supprimé avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Animal supprimé')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Animal non trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Animal non trouvé')
                    ]
                )
            )
        ]
    )]
    



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

