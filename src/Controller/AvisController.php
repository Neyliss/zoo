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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class AvisController extends AbstractController
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

    #[Route('/api/review', name: 'review_create', methods: ['POST'])]
    public function submitReview(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'pseudo' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
            'avis' => [new Assert\NotBlank()],
            'rating' => [new Assert\NotBlank(), new Assert\Range(['min' => 1, 'max' => 5])],
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

    #[Route('/api/review/{id}/validate', name: 'review_validate', methods: ['PUT'])]
    //#[Security("is_granted('ROLE_EMPLOYE')")]
    public function validateReview(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['validated_by'])) {
            return new JsonResponse(['error' => 'Missing validated_by field'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->repository->validateAvis($id, $data['validated_by']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while validating the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Review validated successfully'], Response::HTTP_OK);
    }

    #[Route('/api/review/{id}', name: 'review_get', methods: ['GET'])]
    public function getReview(string $id): JsonResponse
    {
        try {
            $avis = $this->repository->findById($id);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving the review.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$avis || !$avis->isValidated()) {
            return new JsonResponse(['error' => 'Review not found or not validated'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($avis, 'json', ['groups' => 'avis:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/api/reviews', name: 'reviews_get_validated', methods: ['GET'])]
    public function getAllValidatedReviews(): JsonResponse
    {
        try {
            $avisList = $this->repository->findAllValidated();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving reviews.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = $this->serializer->serialize($avisList, 'json', ['groups' => 'avis:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
