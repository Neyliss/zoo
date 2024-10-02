<?php

namespace App\Controller;

use App\Entity\VetForm;
use App\Repository\VetFormRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VetFormController extends AbstractController
{
    private $vetFormRepository;

    public function __construct(VetFormRepository $vetFormRepository)
    {
        $this->vetFormRepository = $vetFormRepository;
    }

    #[Route('/api/vet-form', methods: ['GET'])]
    public function getAllVetForms(): JsonResponse
    {
        $vetForms = $this->vetFormRepository->findAll();
        return new JsonResponse($vetForms);
    }

    #[Route('/api/vet-form/{id}', methods: ['GET'])]
    public function getVetForms($id): JsonResponse
    {
        $vetForm = $this->vetFormRepository->findById($id);

        if (!$vetForm) {
            return new JsonResponse(['error' => 'VetForm not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($vetForm);
    }

    #[Route('/api/vet-form', methods: ['POST'])]
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

    #[Route('/api/vet-form/{id}', methods: ['PUT'])]
    public function updateVetForms(Request $request, $id): JsonResponse
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

    #[Route('/api/vet-form/{id}', methods: ['DELETE'])]
    public function deleteVetForms($id): JsonResponse
    {
        $vetForm = $this->vetFormRepository->findById($id);

        if (!$vetForm) {
            return new JsonResponse(['error' => 'VetForm not found'], Response::HTTP_NOT_FOUND);
        }

        $this->vetFormRepository->delete($id);

        return new JsonResponse(['status' => 'VetForm deleted'], Response::HTTP_NO_CONTENT);
    }

    // Nouvelle route pour que l'admin récupère tous les formulaires vétérinaires
    #[Route('/api/admin/vet-forms', methods: ['GET'])]
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
