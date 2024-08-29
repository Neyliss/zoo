<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ContactController
{
    private $mailer;
    private $validator;
    private $serializer;
    private $contactRepository;

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

    /**
     * @Route("/api/contact", name="contact", methods={"POST"})
     */
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
                ->to('contact@yourdomain.com') // Remplacez par l'email de destination
                ->subject($contact->getTitre())
                ->text($contact->getDescription());

            $this->mailer->send($email);

            return new JsonResponse(['message' => 'Email sent successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Gérer les erreurs, notamment pour l'envoi d'email ou la sauvegarde
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
