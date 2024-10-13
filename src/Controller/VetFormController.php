<?php

namespace App\Controller;

use App\Entity\VetForm;
use App\Repository\VetFormRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;


#[Route('/api/vet-form')]
class VetFormController extends AbstractController
{
    private VetFormRepository $vetFormRepository;

    public function __construct(VetFormRepository $vetFormRepository)
    {
        $this->vetFormRepository = $vetFormRepository;
    }

    #[Route('/all', methods: ['GET'])]


    #[OA\Get(
        path: '/api/vet-form/all',
        summary: "Récupère tous les formulaires vétérinaires",
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des formulaires vétérinaires',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: 'UUID'),
                            new OA\Property(property: 'animal_id', type: 'string', example: 'UUID de l\'animal'),
                            new OA\Property(property: 'etat_animal', type: 'string', example: 'Bon état'),
                            new OA\Property(property: 'nourriture_proposee', type: 'string', example: 'Foin'),
                            new OA\Property(property: 'grammage_nourriture', type: 'integer', example: 500),
                            new OA\Property(property: 'date_passage', type: 'string', format: 'date', example: '2024-10-10'),
                            new OA\Property(property: 'detail_etat_animal', type: 'string', example: 'Animal en bonne santé générale'),
                            new OA\Property(property: 'created_by', type: 'string', example: 'UUID du vétérinaire')
                        ]
                    )
                )
            )
        ]
    )]
    
    public function getAllVetForms(): JsonResponse
    {
        $vetForms = $this->vetFormRepository->findAll();
        return new JsonResponse($vetForms);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Get(
        path: '/api/vet-form/{id}',
        summary: "Récupère un formulaire vétérinaire par ID",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Identifiant du formulaire vétérinaire',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Formulaire vétérinaire trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: 'UUID'),
                        new OA\Property(property: 'animal_id', type: 'string', example: 'UUID de l\'animal'),
                        new OA\Property(property: 'etat_animal', type: 'string', example: 'Bon état'),
                        new OA\Property(property: 'nourriture_proposee', type: 'string', example: 'Foin'),
                        new OA\Property(property: 'grammage_nourriture', type: 'integer', example: 500),
                        new OA\Property(property: 'date_passage', type: 'string', format: 'date', example: '2024-10-10'),
                        new OA\Property(property: 'detail_etat_animal', type: 'string', example: 'Animal en bonne santé générale'),
                        new OA\Property(property: 'created_by', type: 'string', example: 'UUID du vétérinaire')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Formulaire vétérinaire non trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'VetForm not found')
                    ]
                )
            )
        ]
    )]
    


    public function getVetForm(string $id): JsonResponse
    {
        $vetForm = $this->vetFormRepository->findById($id);

        if (!$vetForm) {
            return new JsonResponse(['error' => 'VetForm not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($vetForm);
    }

    #[Route('/new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/vet-form/new',
        summary: "Crée un nouveau formulaire vétérinaire",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du formulaire vétérinaire à créer",
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'animal_id', type: 'string', example: 'UUID de l\'animal'),
                    new OA\Property(property: 'etat_animal', type: 'string', example: 'Bon état'),
                    new OA\Property(property: 'nourriture_proposee', type: 'string', example: 'Foin'),
                    new OA\Property(property: 'grammage_nourriture', type: 'integer', example: 500),
                    new OA\Property(property: 'date_passage', type: 'string', format: 'date', example: '2024-10-10'),
                    new OA\Property(property: 'detail_etat_animal', type: 'string', example: 'Animal en bonne santé générale'),
                    new OA\Property(property: 'created_by', type: 'string', example: 'UUID du vétérinaire')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Formulaire vétérinaire créé avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: 'UUID'),
                        new OA\Property(property: 'animal_id', type: 'string', example: 'UUID de l\'animal'),
                        new OA\Property(property: 'etat_animal', type: 'string', example: 'Bon état'),
                        new OA\Property(property: 'nourriture_proposee', type: 'string', example: 'Foin'),
                        new OA\Property(property: 'grammage_nourriture', type: 'integer', example: 500),
                        new OA\Property(property: 'date_passage', type: 'string', format: 'date', example: '2024-10-10'),
                        new OA\Property(property: 'detail_etat_animal', type: 'string', example: 'Animal en bonne santé générale'),
                        new OA\Property(property: 'created_by', type: 'string', example: 'UUID du vétérinaire')
                    ]
                )
            )
        ]
    )]
    
    public function createVetForm(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $vetForm = new VetForm(
            $data['id'],
            $data['animal_id'],
            $data['etat_animal'],
            $data['nourriture_proposee'],
            $data['grammage_nourriture'],
            $data['date_passage'],
            $data['detail_etat_animal'],
            $data['created_by']
        );
        $this->vetFormRepository->save($vetForm);

        return new JsonResponse($vetForm, Response::HTTP_CREATED);
    }

    #[Route('/maj/{id}', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/vet-form/maj/{id}',
        summary: "Met à jour un formulaire vétérinaire existant",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Identifiant du formulaire vétérinaire',
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du formulaire vétérinaire à mettre à jour",
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'animal_id', type: 'string', example: 'UUID de l\'animal'),
                    new OA\Property(property: 'etat_animal', type: 'string', example: 'Bon état'),
                    new OA\Property(property: 'nourriture_proposee', type: 'string', example: 'Foin'),
                    new OA\Property(property: 'grammage_nourriture', type: 'integer', example: 500),
                    new OA\Property(property: 'date_passage', type: 'string', format: 'date', example: '2024-10-10'),
                    new OA\Property(property: 'detail_etat_animal', type: 'string', example: 'Animal en bonne santé générale'),
                    new OA\Property(property: 'created_by', type: 'string', example: 'UUID du vétérinaire')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Formulaire vétérinaire mis à jour avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'string', example: 'UUID'),
                        new OA\Property(property: 'animal_id', type: 'string', example: 'UUID de l\'animal'),
                        new OA\Property(property: 'etat_animal', type: 'string', example: 'Bon état'),
                        new OA\Property(property: 'nourriture_proposee', type: 'string', example: 'Foin'),
                        new OA\Property(property: 'grammage_nourriture', type: 'integer', example: 500),
                        new OA\Property(property: 'date_passage', type: 'string', format: 'date', example: '2024-10-10'),
                        new OA\Property(property: 'detail_etat_animal', type: 'string', example: 'Animal en bonne santé générale'),
                        new OA\Property(property: 'created_by', type: 'string', example: 'UUID du vétérinaire')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Formulaire vétérinaire non trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'VetForm not found')
                    ]
                )
            )
        ]
    )]
    


    public function updateVetForm(Request $request, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $vetForm = $this->vetFormRepository->findById($id);

        if (!$vetForm) {
            return new JsonResponse(['error' => 'VetForm not found'], Response::HTTP_NOT_FOUND);
        }

        $vetForm->setAnimalId($data['animal_id']);
        $vetForm->setEtatAnimal($data['etat_animal']);
        $vetForm->setNourritureProposee($data['nourriture_proposee']);
        $vetForm->setGrammageNourriture($data['grammage_nourriture']);
        $vetForm->setDatePassage($data['date_passage']);
        $vetForm->setDetailEtatAnimal($data['detail_etat_animal']);
        $vetForm->setCreatedBy($data['created_by']);
        $this->vetFormRepository->update($vetForm);

        return new JsonResponse($vetForm);
    }

    #[Route('/delete/{id}', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/vet-form/delete/{id}',
        summary: "Supprime un formulaire vétérinaire par ID",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Identifiant du formulaire vétérinaire',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Formulaire vétérinaire supprimé avec succès'
            ),
            new OA\Response(
                response: 404,
                description: 'Formulaire vétérinaire non trouvé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'VetForm not found')
                    ]
                )
            )
        ]
    )]    


    public function deleteVetForm(string $id): JsonResponse
    {
        $vetForm = $this->vetFormRepository->findById($id);

        if (!$vetForm) {
            return new JsonResponse(['error' => 'VetForm not found'], Response::HTTP_NOT_FOUND);
        }

        $this->vetFormRepository->delete($id);

        return new JsonResponse(['status' => 'VetForm deleted'], Response::HTTP_NO_CONTENT);
    }
}
