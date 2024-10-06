<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('api/offers')] // Préfixe pour toutes les routes relatives aux offres
class OfferController extends AbstractController
{
    private OfferRepository $offerRepository;

    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    #[Route('/list', name: 'offer_list', methods: ['GET'])] // Route pour obtenir toutes les offres
    public function index(): Response
    {
        $offers = $this->offerRepository->findAll();
        return $this->json($offers);
    }

    #[Route('/new', name: 'offer_new', methods: ['POST'])] // Route pour créer une nouvelle offre
    #[IsGranted('ROLE_ADMIN')] // Autorisation uniquement pour les administrateurs
    public function create(Request $request): Response
    {
        $offer = new Offer();
        $offer->setName($request->request->get('name'))
              ->setDescription($request->request->get('description'))
              ->setImage($request->files->get('image')->getPathname());

        $this->offerRepository->save($offer);

        return $this->json(['message' => 'Offer created successfully']);
    }

    #[Route('/edit/{id}', name: 'offer_edit', methods: ['POST'])] // Route pour éditer une offre
    #[IsGranted('ROLE_ADMIN')] // Autorisation uniquement pour les administrateurs
    public function edit(Request $request, string $id): Response
    {
        $offer = $this->offerRepository->findById($id);
        if (!$offer) {
            return $this->json(['message' => 'Offer not found'], 404);
        }

        $offer->setName($request->request->get('name'))
              ->setDescription($request->request->get('description'));

        if ($request->files->get('image')) {
            $offer->setImage($request->files->get('image')->getPathname());
        }

        $this->offerRepository->update($offer);

        return $this->json(['message' => 'Offer updated successfully']);
    }

    #[Route('/delete/{id}', name: 'offer_delete', methods: ['POST'])] // Route pour supprimer une offre
    #[IsGranted('ROLE_ADMIN')] // Autorisation uniquement pour les administrateurs
    public function delete(string $id): Response
    {
        $offer = $this->offerRepository->findById($id);
        if (!$offer) {
            return $this->json(['message' => 'Offer not found'], 404);
        }

        $this->offerRepository->delete($id);

        return $this->json(['message' => 'Offer deleted successfully']);
    }
}
