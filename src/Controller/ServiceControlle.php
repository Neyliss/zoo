<?php

// src/Controller/ServiceController.php
namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ServiceController extends AbstractController
{
    private ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @Route("/services", name="service_list")
     */
    public function index(): Response
    {
        $services = $this->serviceRepository->findAll();
        return $this->render('service/index.html.twig', ['services' => $services]);
    }

    /**
     * @Route("/services/new", name="service_new", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request): Response
    {
        $service = new Service();
        $service->setName($request->request->get('name'));
        $service->setDescription($request->request->get('description'));
        $service->setImage($request->files->get('image')->getPathname());

        $this->serviceRepository->save($service);

        return $this->redirectToRoute('service_list');
    }

    /**
     * @Route("/services/edit/{id}", name="service_edit", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, string $id): Response
    {
        $service = $this->serviceRepository->findById($id);
        if (!$service) {
            throw $this->createNotFoundException('Service not found');
        }

        $service->setName($request->request->get('name'));
        $service->setDescription($request->request->get('description'));

        if ($request->files->get('image')) {
            $service->setImage($request->files->get('image')->getPathname());
        }

        $this->serviceRepository->save($service);

        return $this->redirectToRoute('service_list');
    }

    /**
     * @Route("/services/delete/{id}", name="service_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(string $id): Response
    {
        $service = $this->serviceRepository->findById($id);
        if (!$service) {
            throw $this->createNotFoundException('Service not found');
        }

        $this->serviceRepository->delete($id);

        return $this->redirectToRoute('service_list');
    }
}
