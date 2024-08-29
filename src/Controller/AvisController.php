<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class AvisController
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

    /**
     * @Route("/api/review", name="review_create", methods={"POST"})
     * @OA\Post(
     *     path="/api/review",
     *     summary="Submit a new review",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pseudo", type="string", example="JohnDoe"),
     *             @OA\Property(property="avis", type="string", example="Great service!"),
     *             @OA\Property(property="rating", type="integer", example=5),
     *             @OA\Property(property="validated_by", type="string", example="123e4567-e89b-12d3-a456-426614174000")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review submitted successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"), @OA\Property(property="id", type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(type="object", @OA\Property(property="errors", type="array", @OA\Items(type="string")))
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function submitReview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        $constraints = new Assert\Collection([
            'pseudo' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
            'avis' => [new Assert\NotBlank()],
            'rating' => [new Assert\NotBlank(), new Assert\Range(['min' => 1, 'max' => 5])],
            'validated_by' => [new Assert\Optional(new Assert\Uuid())] // Validation optionnelle pour validated_by
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
            $avis = $this->serializer->deserialize(json_encode($data), Avis::class, 'json');
            $this->repository->save($avis);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while saving the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return new JsonResponse(['message' => 'Review submitted successfully', 'id' => $avis->getId()], Response::HTTP_OK);
    }

    /**
     * @Route("/api/review/{id}", name="review_get", methods={"GET"})
     * @OA\Get(
     *     path="/api/review/{id}",
     *     summary="Get a review by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the review",
     *         required=true,
     *         @OA\Schema(type="string", example="123e4567-e89b-12d3-a456-426614174000")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Avis")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review not found"
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function getReview(string $id): JsonResponse
    {
        try {
            $avis = $this->repository->findById($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        if (!$avis) {
            return new JsonResponse(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }
    
        $data = $this->serializer->serialize($avis, 'json', ['groups' => 'avis:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/review/{id}", name="review_update", methods={"PUT"})
     * @OA\Put(
     *     path="/api/review/{id}",
     *     summary="Update an existing review",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the review",
     *         required=true,
     *         @OA\Schema(type="string", example="123e4567-e89b-12d3-a456-426614174000")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pseudo", type="string", example="JohnDoe"),
     *             @OA\Property(property="avis", type="string", example="Great service!"),
     *             @OA\Property(property="rating", type="integer", example=5),
     *             @OA\Property(property="validated_by", type="string", example="123e4567-e89b-12d3-a456-426614174000")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review updated successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(type="object", @OA\Property(property="errors", type="array", @OA\Items(type="string")))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review not found"
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function updateReview(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        $constraints = new Assert\Collection([
            'pseudo' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
            'avis' => [new Assert\NotBlank()],
            'rating' => [new Assert\NotBlank(), new Assert\Range(['min' => 1, 'max' => 5])],
            'validated_by' => [new Assert\Optional(new Assert\Uuid())] // Validation optionnelle pour validated_by
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
            $avis = $this->repository->findById($id);
            if (!$avis) {
                return new JsonResponse(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
            }
    
            $this->serializer->deserialize(json_encode($data), Avis::class, 'json', ['object_to_populate' => $avis]);
            $this->repository->update($avis);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while updating the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return new JsonResponse(['message' => 'Review updated successfully'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/review/{id}", name="review_delete", methods={"DELETE"})
     * @OA\Delete(
     *     path="/api/review/{id}",
     *     summary="Delete a review by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the review",
     *         required=true,
     *         @OA\Schema(type="string", example="123e4567-e89b-12d3-a456-426614174000")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Review not found"
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function deleteReview(string $id): JsonResponse
    {
        try {
            $avis = $this->repository->findById($id);
            if (!$avis) {
                return new JsonResponse(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
            }
    
            $this->repository->delete($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while deleting the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return new JsonResponse(['message' => 'Review deleted successfully'], Response::HTTP_OK);
    }
}
