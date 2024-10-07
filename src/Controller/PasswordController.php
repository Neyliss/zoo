<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/user/password')] // Préfixe pour toutes les routes relatives à la modification de mot de passe
class PasswordController extends AbstractController
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/update', methods: ['PUT'])] // Route pour mettre à jour le mot de passe
    public function updatePassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['id'] ?? null;
        $newPassword = $data['new_password'] ?? null;

        if (!$userId || !$newPassword) {
            return new JsonResponse(['error' => 'User ID and new password required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userRepository->findById($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Hachage du nouveau mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        // Génération d'un nouveau token API
        $user->regenerateApiToken();

        // Sauvegarde des modifications
        $this->userRepository->save($user); 

        return new JsonResponse(['message' => 'Password updated successfully', 'api_token' => $user->getApiToken()]);
    }
}
