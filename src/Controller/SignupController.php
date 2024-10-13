<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignupController extends AbstractController
{
    private $userRepository;
    private $passwordHasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/api/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function signup(Request $request): JsonResponse
    {
        // Vérifier si la requête est POST (soumission du formulaire)
        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true); // Utilisation de getContent pour récupérer les données JSON
            $errors = [];
    
            // Validation des données
            // ...
if (empty($data['email']) || empty($data['password']) || empty($data['role'])) {
    $errors[] = 'Tous les champs sont requis.';
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Le format de l\'email est incorrect.';
}

if (strlen($data['password']) < 12 || !preg_match('/[A-Z]/', $data['password']) || !preg_match('/[a-z]/', $data['password']) || !preg_match('/[0-9]/', $data['password']) || !preg_match('/[\W]/', $data['password'])) {
    $errors[] = 'Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
}

// Si pas d'erreurs, on peut enregistrer l'utilisateur
if (empty($errors)) {
    try {
        // Crée un nouvel utilisateur
        $user = new User(
            null, // L'ID sera généré automatiquement
            $data['email'],
            '', // Le mot de passe sera encodé plus bas
            [$data['role']] // Le rôle
        );

        // Hacher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Enregistrer l'utilisateur
        $this->userRepository->save($user);

        // Retourne une réponse JSON en cas de succès
        return $this->json(['success' => true]);
    } catch (\Exception $e) {
        $errors[] = 'Erreur lors de l\'enregistrement de l\'utilisateur : ' . $e->getMessage();
    }
}

// Retourner les erreurs s'il y en a
    return $this->json(['success' => false, 'errors' => $errors]);
        }
    }
}
