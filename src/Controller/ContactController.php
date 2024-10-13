<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/contact')]
class ContactController extends AbstractController
{
    private MailerInterface $mailer;
    private ValidatorInterface $validator;
    private SerializerInterface $serializer;
    private ContactRepository $contactRepository;

    public function __construct(
        MailerInterface $mailer,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        ContactRepository $contactRepository
    ) {
        $this->mailer = $mailer;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->contactRepository = $contactRepository;
    }


    // MEHTOD POST 
    #[Route('/create', name: 'contact_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/contact/create',
        summary: "Création d'un nouveau contact",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données du contact à créer",
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'Titre', type: 'string', example: 'Problème avec mon compte'),
                    new OA\Property(property: 'Email', type: 'string', example: 'user@example.com'),
                    new OA\Property(property: 'Description', type: 'string', example: 'Je rencontre des problèmes de connexion.')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Contact créé avec succès et email envoyé',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Email envoyé avec succès')
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Erreur de validation des données',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string'))
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Erreur interne lors de l\'envoi de l\'email',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Une erreur est survenue lors de l\'envoi de l\'email.')
                    ]
                )
            )
        ]
    )]
    

    public function contact(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        $constraints = new Assert\Collection([
            'Titre' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
            'Email' => [new Assert\NotBlank(), new Assert\Email()],
            'Description' => [new Assert\NotBlank()]
        ]);

        $errors = $this->validator->validate($data, $constraints);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Créer une instance de Contact
        $contact = new Contact(null, $data['Titre'], $data['Email'], $data['Description']);

        try {
            // Enregistrer le contact dans la base de données
            $this->contactRepository->save($contact);

            // Envoyer l'email
            $email = (new Email())
                ->from($contact->getEmail())
                ->to('fenix425@live.com') // Remplacez par l'email de destination
                ->subject($contact->getTitre())
                ->text($contact->getDescription());

            $this->mailer->send($email);

            return new JsonResponse(['message' => 'Email envoyé avec succès'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
