<?php
namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Security;


#[Route('/api/review')] // Préfixe pour toutes les routes de la classe
class AvisController extends AbstractController
{
    private $repository;
    private $validator;
    private $serializer;

    public function __construct(AvisRepository $repository, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }


    //METHOD POST 

    #[Route('/create', name: 'review_create', methods: ['POST'])] // Route POST pour créer un avis
    #[OA\Post(
        path: '/api/review/create',
        summary: "Créer un avis",
        description: "Soumet un nouvel avis à propos d'un service ou produit",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'avis à soumettre",
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'pseudo', type: 'string', example: 'JeanDupont'),
                    new OA\Property(property: 'avis', type: 'string', example: 'Service excellent, rapide et fiable'),
                    new OA\Property(property: 'rating', type: 'integer', example: 5)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Avis soumis avec succès",
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Review submitted successfully'),
                        new OA\Property(property: 'id', type: 'string', example: '123e4567-e89b-12d3-a456-426614174000')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Erreur de validation",
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string', example: 'Le pseudo est requis'))
                    ]
                )
            )
        ]
    )]
    
    public function submitReview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (empty($data) || json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON format'], Response::HTTP_BAD_REQUEST);
        }
    
        $constraints = new Assert\Collection([
            'pseudo' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
            'avis' => [new Assert\NotBlank()],
            'rating' => [new Assert\NotBlank(), new Assert\Range(['min' => 1, 'max' => 5])],
        ]);
    
        $errors = $this->validator->validate($data, $constraints);
    
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }
    
        try {
            // Créer un nouvel objet Avis sans ID, l'ID sera généré par la base de données
            $avis = new Avis(null, $data['pseudo'], $data['avis'], $data['rating']);
            
            // Enregistrer l'avis dans le dépôt
            $this->repository->save($avis);
    
            // Il peut être judicieux de récupérer l'ID généré après l'enregistrement
            $avisId = $avis->getId();
            
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while saving the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return new JsonResponse(['message' => 'Review submitted successfully', 'id' => $avisId], Response::HTTP_OK);
    }
    


    // METHOD PUT 
    #[Route('/{id}/validate', name: 'review_validate', methods: ['PUT'])] // Route PUT pour valider un avis
    #[OA\Put(
        path: '/api/review/{id}/validate',
        summary: "Valider un avis",
        description: "Permet de valider un avis en spécifiant l'utilisateur validateur",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: "ID de l'avis à valider",
                schema: new OA\Schema(type: 'string', example: '123e4567-e89b-12d3-a456-426614174000')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données pour valider l'avis",
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'validated_by', type: 'string', example: 'admin')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Avis validé avec succès",
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Review validated successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Champs manquant ou invalide",
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Missing validated_by field')
                    ]
                )
            )
        ]
    )]

    //#[Security("is_granted('ROLE_EMPLOYE')")] // Utiliser cette ligne pour sécuriser avec les rôles
    public function validateReview(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['validated_by'])) {
            return new JsonResponse(['error' => 'Missing validated_by field'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->repository->validateAvis($id, $data['validated_by']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while validating the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Review validated successfully'], Response::HTTP_OK);
    }


    // METHOD GET 
    #[Route('/{id}', name: 'review_get', methods: ['GET'])] // Route GET pour récupérer un avis par ID
    #[OA\Get(
        path: '/api/review/{id}',
        summary: "Récupérer un avis validé",
        description: "Permet de récupérer un avis validé par son ID",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: "ID de l'avis",
                schema: new OA\Schema(type: 'string', example: '123e4567-e89b-12d3-a456-426614174000')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Avis trouvé",
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: '123e4567-e89b-12d3-a456-426614174000'),
                        new OA\Property(property: 'pseudo', type: 'string', example: 'JeanDupont'),
                        new OA\Property(property: 'avis', type: 'string', example: 'Service excellent'),
                        new OA\Property(property: 'rating', type: 'integer', example: 5),
                        new OA\Property(property: 'validated_by', type: 'string', example: 'admin'),
                        new OA\Property(property: 'is_validated', type: 'boolean', example: true)
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Avis non trouvé ou non validé",
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Review not found or not validated')
                    ]
                )
            )
        ]
    )]    

    public function getReview(string $id): JsonResponse
    {
        try {
            $avis = $this->repository->findById($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$avis || !$avis->isValidated()) {
            return new JsonResponse(['error' => 'Review not found or not validated'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($avis, 'json', ['groups' => 'avis:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    // Method GET 
    #[Route('/all', name: 'reviews_get_validated', methods: ['GET'])] // Route GET pour récupérer tous les avis validés
    #[OA\Get(
        path: '/api/review/all',
        summary: "Récupérer tous les avis validés",
        description: "Permet de récupérer tous les avis validés",
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des avis validés",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: '123e4567-e89b-12d3-a456-426614174000'),
                            new OA\Property(property: 'pseudo', type: 'string', example: 'JeanDupont'),
                            new OA\Property(property: 'avis', type: 'string', example: 'Très bon produit'),
                            new OA\Property(property: 'rating', type: 'integer', example: 5),
                            new OA\Property(property: 'validated_by', type: 'string', example: 'admin'),
                            new OA\Property(property: 'is_validated', type: 'boolean', example: true)
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 500,
                description: "Erreur serveur",
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'An error occurred while retrieving reviews.')
                    ]
                )
            )
        ]
    )]
    

    public function getAllValidatedReviews(): JsonResponse
    {
        try {
            $avisList = $this->repository->findAllValidated();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving reviews.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = $this->serializer->serialize($avisList, 'json', ['groups' => 'avis:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
