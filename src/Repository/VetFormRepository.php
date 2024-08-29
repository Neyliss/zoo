<?php

namespace App\Repository;

use App\Entity\VetForm;
use PDO;

class VetFormRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM vet_forms');
        $vetForms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($vetForm) {
            return new VetForm(
                $vetForm['id'],
                $vetForm['animal_id'],
                $vetForm['etat_animal'],
                $vetForm['nourriture_proposee'],
                $vetForm['grammage_nourriture'],
                $vetForm['date_passage'],
                $vetForm['detail_etat_animal'],
                $vetForm['created_by']
            );
        }, $vetForms);
    }

    public function findById($id): ?VetForm
    {
        $stmt = $this->pdo->prepare('SELECT * FROM vet_forms WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $vetForm = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($vetForm) {
            return new VetForm(
                $vetForm['id'],
                $vetForm['animal_id'],
                $vetForm['etat_animal'],
                $vetForm['nourriture_proposee'],
                $vetForm['grammage_nourriture'],
                $vetForm['date_passage'],
                $vetForm['detail_etat_animal'],
                $vetForm['created_by']
            );
        }

        return null;
    }

    public function save(VetForm $vetForm): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO vet_forms (id, animal_id, etat_animal, nourriture_proposee, grammage_nourriture, date_passage, detail_etat_animal, created_by) 
            VALUES (:id, :animal_id, :etat_animal, :nourriture_proposee, :grammage_nourriture, :date_passage, :detail_etat_animal, :created_by)'
        );
        $stmt->execute([
            'id' => $vetForm->getId(),
            'animal_id' => $vetForm->getAnimalId(),
            'etat_animal' => $vetForm->getEtatAnimal(),
            'nourriture_proposee' => $vetForm->getNourritureProposee(),
            'grammage_nourriture' => $vetForm->getGrammageNourriture(),
            'date_passage' => $vetForm->getDatePassage(),
            'detail_etat_animal' => $vetForm->getDetailEtatAnimal(),
            'created_by' => $vetForm->getCreatedBy()
        ]);
    }

    public function update(VetForm $vetForm): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE vet_forms SET animal_id = :animal_id, etat_animal = :etat_animal, nourriture_proposee = :nourriture_proposee, 
            grammage_nourriture = :grammage_nourriture, date_passage = :date_passage, detail_etat_animal = :detail_etat_animal, created_by = :created_by 
            WHERE id = :id'
        );
        $stmt->execute([
            'id' => $vetForm->getId(),
            'animal_id' => $vetForm->getAnimalId(),
            'etat_animal' => $vetForm->getEtatAnimal(),
            'nourriture_proposee' => $vetForm->getNourritureProposee(),
            'grammage_nourriture' => $vetForm->getGrammageNourriture(),
            'date_passage' => $vetForm->getDatePassage(),
            'detail_etat_animal' => $vetForm->getDetailEtatAnimal(),
            'created_by' => $vetForm->getCreatedBy()
        ]);
    }

    public function delete($id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM vet_forms WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
