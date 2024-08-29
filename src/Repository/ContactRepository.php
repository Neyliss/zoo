<?php

namespace App\Repository;

use App\Entity\Contact;
use PDO;

class ContactRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Enregistre un contact dans la base de données.
     * 
     * @param Contact $contact
     * @throws \Exception
     */
    public function save(Contact $contact): void
    {
        $query = '
            INSERT INTO contacts (titre, email, description) 
            VALUES (:titre, :email, :description)
        ';
        
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'titre' => $contact->getTitre(),
                'email' => $contact->getEmail(),
                'description' => $contact->getDescription(),
            ]);

            // Récupérer l'ID auto-généré
            $contact->setId((int) $this->pdo->lastInsertId());
            
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw new \Exception('Failed to save contact: ' . $e->getMessage());
        }
    }
}
