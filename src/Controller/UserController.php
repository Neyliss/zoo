<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserController
{
    private $userRepository;
    private $passwordHasher;
    private $jwtManager;
    private $validator;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        ValidatorInterface $validator
    ) {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/login", methods={"POST"})
     */
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userRepository->findByEmail($email);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse([
            'token' => $token,
            'role' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/api/users", methods={"POST"})
     */
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 6])],
            'role_id' => [new Assert\NotBlank()],
        ]);

        $errors = $this->validator->validate($data, $constraints);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = new User(
            uniqid(),
            $data['email'],
            $this->passwordHasher->hashPassword(new User('', '', '', ''), $data['password']),
            $data['role_id'],
            null  // Ajoutez 'null' ou une valeur de token si disponible
        );
        

        $this->userRepository->save($user);

        return new JsonResponse(['message' => 'User created successfully'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/users/{id}", methods={"GET"})
     */
    public function getUser(string $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'role' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/api/users/{id}", methods={"PUT"})
     */
    public function updateUser(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $this->userRepository->findById($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->setEmail($data['email'] ?? $user->getEmail());
        if (isset($data['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        }
        $user->setRoleId($data['role_id'] ?? $user->getRoleId());

        $this->userRepository->update($user);

        return new JsonResponse(['message' => 'User updated successfully']);
    }

    /**
     * @Route("/api/users/{id}", methods={"DELETE"})
     */
    public function deleteUser(string $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->userRepository->delete($id);

        return new JsonResponse(['message' => 'User deleted successfully']);
    }
}
