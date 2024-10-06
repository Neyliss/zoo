<?php

namespace App\Controller;

use App\Entity\VetForm;
use App\Repository\VetFormRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/vet-form')] // Préfixe pour toutes les routes relatives aux VetForm
class VetFormController extends AbstractController
{
    private VetFormRepository $vetFormRepository;

    public function __construct(VetFormRepository $vetFormRepository)
    {
        $this->vetFormRepository = $vetFormRepository;
    }

    #[Route('', methods: ['GET'])] // Route pour récupérer tous les formulaires vétérinaires
    public function getAllVetForms(): JsonResponse
    {
        $vetForms = $this->vetFormRepository->findAll();
        return new JsonResponse($vetForms);
    }

    #[Route('/{id}', methods: ['GET'])] // Route pour récupérer un formulaire vétérinaire spécifique
    public function getVetForms(string $id): JsonResponse
    {
        $vetForm = $this->vetFormRepository->findById($id);

        if (!$vetForm) {
            return new JsonResponse(['error' => 'VetForm not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($vetForm);
    }

    #[Route('', methods: ['POST'])] // Route pour créer un formulaire vétérinaire
    public function createVetForms(Request $request): JsonResponse
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

    #[Route('/{id}', methods: ['PUT'])] // Route pour mettre à jour un formulaire vétérinaire
    public function updateVetForms(Request $request, string $id): JsonResponse
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

    #[Route('/{id}', methods: ['DELETE'])] // Route pour supprimer un formulaire vétérinaire
    public function deleteVetForms(string $id): JsonResponse
    {
        $vetForm = $this->vetFormRepository->findById($id);

        if (!$vetForm) {
            return new JsonResponse(['error' => 'VetForm not found'], Response::HTTP_NOT_FOUND);
        }

        $this->vetFormRepository->delete($id);

        return new JsonResponse(['status' => 'VetForm deleted'], Response::HTTP_NO_CONTENT);
    }

    #[Route('/admin/all', methods: ['GET'])] // Route pour que l'admin récupère tous les formulaires vétérinaires
    public function getAllVetFormsForAdmin(): JsonResponse
    {
        if (!$this->isAdmin()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $vetForms = $this->vetFormRepository->findAllForAdmin();
        return new JsonResponse($vetForms);
    }

    private function isAdmin(): bool
    {
        return $this->isGranted('ROLE_ADMIN');
    }
}
