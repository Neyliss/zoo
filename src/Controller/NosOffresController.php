<?php

namespace App\Controller;

use App\Entity\NosOffres;
use App\Repository\NosOffresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NosOffresController extends AbstractController
{
    private NosOffresRepository $nosOffresRepository;

    public function __construct(NosOffresRepository $nosOffresRepository)
    {
        $this->nosOffresRepository = $nosOffresRepository;
    }

    /**
     * @Route("/nos-offres", name="nos_offres_list")
     */
    public function index(): Response
    {
        $nosOffres = $this->nosOffresRepository->findAll();
        return $this->render('nos_offres/index.html.twig', ['nosOffres' => $nosOffres]);
    }

    /**
     * @Route("/nos-offres/new", name="nos_offres_new", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request): Response
    {
        $nosOffres = new NosOffres();
        $nosOffres->setName($request->request->get('name'));
        $nosOffres->setDescription($request->request->get('description'));
        $nosOffres->setImage($request->files->get('image')->getPathname());

        $this->nosOffresRepository->save($nosOffres);

        return $this->redirectToRoute('nos_offres_list');
    }

    /**
     * @Route("/nos-offres/edit/{id}", name="nos_offres_edit", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, string $id): Response
    {
        $nosOffres = $this->nosOffresRepository->findById($id);
        if (!$nosOffres) {
            throw $this->createNotFoundException('Offre non trouvée');
        }

        $nosOffres->setName($request->request->get('name'));
        $nosOffres->setDescription($request->request->get('description'));

        if ($request->files->get('image')) {
            $nosOffres->setImage($request->files->get('image')->getPathname());
        }

        $this->nosOffresRepository->update($nosOffres);

        return $this->redirectToRoute('nos_offres_list');
    }

    /**
     * @Route("/nos-offres/delete/{id}", name="nos_offres_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(string $id): Response
    {
        $nosOffres = $this->nosOffresRepository->findById($id);
        if (!$nosOffres) {
            throw $this->createNotFoundException('Offre non trouvée');
        }

        $this->nosOffresRepository->delete($id);

        return $this->redirectToRoute('nos_offres_list');
    }
}
