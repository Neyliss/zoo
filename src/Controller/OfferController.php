<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OfferController extends AbstractController
{
    private OfferRepository $offerRepository;

    public function __construct(OfferRepository $offerRepository)
    {
        $this->offerRepository = $offerRepository;
    }

    #[Route('/offers', name: 'offer_list', methods: ['GET'])]
    public function index(): Response
    {
        $offers = $this->offerRepository->findAll();
        return $this->json($offers);
    }

    #[Route('/offers/new', name: 'offer_new', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $offer = new Offer();
        $offer->setName($request->request->get('name'))
              ->setDescription($request->request->get('description'))
              ->setImage($request->files->get('image')->getPathname());

        $this->offerRepository->save($offer);

        return $this->json(['message' => 'Offer created successfully']);
    }

    #[Route('/offers/edit/{id}', name: 'offer_edit', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
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

    #[Route('/offers/delete/{id}', name: 'offer_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
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
