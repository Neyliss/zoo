<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignupController extends AbstractController
{
    #[Route('/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function signup(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            // Valider et traiter les données du formulaire ici
            // Enregistrez les données dans la base de données via une API ou directement en SQL

            // Exemple simple de validation
            $errors = [];
            if (empty($data['Nom']) || empty($data['Prenom']) || empty($data['Email']) || empty($data['Password']) || empty($data['PasswordConfirm']) || empty($data['AccountType'])) {
                $errors[] = 'Tous les champs sont requis.';
            }
            if ($data['Password'] !== $data['PasswordConfirm']) {
                $errors[] = 'Les mots de passe ne correspondent pas.';
            }

            if (empty($errors)) {
                // Exemple de réponse de succès
                return $this->json(['success' => true]);
            } else {
                return $this->json(['success' => false, 'errors' => $errors]);
            }
        }

        // Afficher le formulaire d'inscription (si GET)
        return $this->render('signup.html.twig');
    }
}
