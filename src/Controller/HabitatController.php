<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class HabitatController
{
    private $habitatRepository;

    public function __construct(HabitatRepository $habitatRepository)
    {
        $this->habitatRepository = $habitatRepository;
    }

    /**
     * @Route("/api/habitats", methods={"GET"})
     * @OA\Get(
     *     path="/api/habitats",
     *     summary="Get all habitats",
     *     @OA\Response(
     *         response=200,
     *         description="List of habitats",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Habitat::class))
     *         )
     *     )
     * )
     */
    public function list(): JsonResponse
    {
        $habitats = $this->habitatRepository->findAll();
        $data = [];

        foreach ($habitats as $habitat) {
            $data[] = [
                'id' => $habitat->getId(),
                'name' => $habitat->getName(),
                'description' => $habitat->getDescription(),
                'image_path' => $habitat->getImagePath(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/api/habitats/{id}", methods={"GET"})
     * @OA\Get(
     *     path="/api/habitats/{id}",
     *     summary="Get a specific habitat by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the habitat",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of the habitat",
     *         @OA\JsonContent(ref=@Model(type=Habitat::class))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat not found"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $habitat = $this->habitatRepository->findById($id);

        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $habitat->getId(),
            'name' => $habitat->getName(),
            'description' => $habitat->getDescription(),
            'image_path' => $habitat->getImagePath(),
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/api/habitats", methods={"POST"})
     * @OA\Post(
     *     path="/api/habitats",
     *     summary="Create a new habitat",
     *     @OA\RequestBody(
     *         description="Habitat to be created",
     *         required=true,
     *         @OA\JsonContent(ref=@Model(type=Habitat::class))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Habitat created successfully",
     *         @OA\JsonContent(ref=@Model(type=Habitat::class))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $habitat = new Habitat();
        $habitat->setName($data['name']);
        $habitat->setDescription($data['description']);
        $habitat->setImagePath($data['image_path']);

        $this->habitatRepository->create($habitat);

        return new JsonResponse(['message' => 'Habitat created'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/habitats/{id}", methods={"PUT"})
     * @OA\Put(
     *     path="/api/habitats/{id}",
     *     summary="Update an existing habitat",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the habitat to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Habitat data to be updated",
     *         required=true,
     *         @OA\JsonContent(ref=@Model(type=Habitat::class))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Habitat updated successfully",
     *         @OA\JsonContent(ref=@Model(type=Habitat::class))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat not found"
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $habitat = $this->habitatRepository->findById($id);

        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat not found'], Response::HTTP_NOT_FOUND);
        }

        $habitat->setName($data['name']);
        $habitat->setDescription($data['description']);
        $habitat->setImagePath($data['image_path']);

        $this->habitatRepository->update($habitat, $id);

        return new JsonResponse(['message' => 'Habitat updated'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/habitats/{id}", methods={"DELETE"})
     * @OA\Delete(
     *     path="/api/habitats/{id}",
     *     summary="Delete a habitat by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the habitat to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Habitat deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Habitat not found"
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->habitatRepository->findById($id);

        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat not found'], Response::HTTP_NOT_FOUND);
        }

        $this->habitatRepository->delete($id);

        return new JsonResponse(['message' => 'Habitat deleted'], Response::HTTP_NO_CONTENT);
    }
}
