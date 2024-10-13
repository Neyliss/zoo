<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/offers')] // Préfixe pour toutes les routes relatives aux offres
class OfferController
{
    private OfferRepository $offerRepository;

    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    //METHOD GET
    #[Route('/list', name: 'offer_list', methods: ['GET'])] // Route pour obtenir toutes les offres
    #[OA\Get(
        path: '/api/offers/list',
        summary: "Récupérer la liste de toutes les offres",
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des offres',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: 'b62a8b23-7a34-4b94-88af-b7c7e8d00f9c'),
                            new OA\Property(property: 'name', type: 'string', example: 'Offre spéciale séjour'),
                            new OA\Property(property: 'description', type: 'string', example: 'Séjour de 3 nuits au prix de 2.'),
                            new OA\Property(property: 'image', type: 'string', example: '/images/offers/special-offer.jpg')
                        ]
                    )
                )
            )
        ]
    )]
    

    public function index(): Response
    {
        $offers = $this->offerRepository->findAll();
        return new Response(json_encode($offers), 200, ['Content-Type' => 'application/json']);
    }

    //METHOD POST POUR CREER UNE NOUVELLE OFFRE 
    #[Route('/new', name: 'offer_new', methods: ['POST'])] // Route pour créer une nouvelle offre
    #[OA\Post(
        path: '/api/offers/new',
        summary: "Créer une nouvelle offre",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'offre à créer",
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Offre d\'été'),
                        new OA\Property(property: 'description', type: 'string', example: 'Remise spéciale pour les séjours en été'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Image de l\'offre')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Offre créée avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Offer created successfully')
                    ]
                )
            )
        ]
    )]
    

    public function create(Request $request): Response
    {
        $offer = new Offer();
        $offer->setName($request->request->get('name'))
              ->setDescription($request->request->get('description'))
              ->setImage($request->files->get('image')->getPathname());

        $this->offerRepository->save($offer);

        return new Response(json_encode(['message' => 'Offer created successfully']), 201);
    }


    //Method POST EDIT UNE NOUVELLE OFFRE 
    #[Route('/edit/{id}', name: 'offer_edit', methods: ['POST'])] // Route pour éditer une offre
    #[OA\Post(
        path: '/api/offers/edit/{id}',
        summary: "Modifier une offre existante",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de l\'offre à modifier',
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'offre modifiée",
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Offre de printemps'),
                        new OA\Property(property: 'description', type: 'string', example: 'Réduction pour les séjours au printemps'),
                        new OA\Property(property: 'image', type: 'string', format: 'binary', description: 'Nouvelle image de l\'offre (facultatif)')
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Offre modifiée avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Offer updated successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Offre non trouvée',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Offer not found')
                    ]
                )
            )
        ]
    )]

    public function edit(Request $request, string $id): Response
    {
        $offer = $this->offerRepository->findById($id);
        if (!$offer) {
            return new Response(json_encode(['message' => 'Offer not found']), 404);
        }

        $offer->setName($request->request->get('name'))
              ->setDescription($request->request->get('description'));

        if ($request->files->get('image')) {
            $offer->setImage($request->files->get('image')->getPathname());
        }

        $this->offerRepository->update($offer);

        return new Response(json_encode(['message' => 'Offer updated successfully']), 200);
    }

    
    #[Route('/delete/{id}', name: 'offer_delete', methods: ['DELETE'])] // Route pour supprimer une offre
    #[OA\Post(
        path: '/api/offers/delete/{id}',
        summary: "Supprimer une offre existante",
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de l\'offre à supprimer',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Offre supprimée avec succès',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Offer deleted successfully')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Offre non trouvée',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Offer not found')
                    ]
                )
            )
        ]
    )]
    


    public function delete(string $id): Response
    {
        $offer = $this->offerRepository->findById($id);
        if (!$offer) {
            return new Response(json_encode(['message' => 'Offer not found']), 404);
        }

        $this->offerRepository->delete($id);

        return new Response(json_encode(['message' => 'Offer deleted successfully']), 200);
    }
}
