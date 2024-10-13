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
            INSERT INTO contact (id, titre, email, description) 
            VALUES (uuid_generate_v4(), :titre, :email, :description)
        ';
        
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'titre' => $contact->getTitre(),
                'email' => $contact->getEmail(),
                'description' => $contact->getDescription(),
            ]);

            // Récupérer l'ID auto-généré et le définir dans l'entité
            $stmt = $this->pdo->query('SELECT id FROM contact WHERE rowid = last_insert_rowid()');
            $id = $stmt->fetchColumn();
            $contact->setId($id);

            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw new \Exception('Échec de l\'enregistrement du contact : ' . $e->getMessage());
        }
    }
}
