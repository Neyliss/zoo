<?php

namespace App\Entity;

class VetForm
{
    private $id;
    private $animalId;
    private $etatAnimal;
    private $nourritureProposee;
    private $grammageNourriture;
    private $datePassage;
    private $detailEtatAnimal;
    private $createdBy;

    public function __construct($id, $animalId, $etatAnimal, $nourritureProposee, $grammageNourriture, $datePassage, $detailEtatAnimal, $createdBy)
    {
        $this->id = $id;
        $this->animalId = $animalId;
        $this->etatAnimal = $etatAnimal;
        $this->nourritureProposee = $nourritureProposee;
        $this->grammageNourriture = $grammageNourriture;
        $this->datePassage = $datePassage;
        $this->detailEtatAnimal = $detailEtatAnimal;
        $this->createdBy = $createdBy;
    }

    // Getters and setters

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getAnimalId()
    {
        return $this->animalId;
    }

    public function setAnimalId($animalId)
    {
        $this->animalId = $animalId;
    }

    public function getEtatAnimal()
    {
        return $this->etatAnimal;
    }

    public function setEtatAnimal($etatAnimal)
    {
        $this->etatAnimal = $etatAnimal;
    }

    public function getNourritureProposee()
    {
        return $this->nourritureProposee;
    }

    public function setNourritureProposee($nourritureProposee)
    {
        $this->nourritureProposee = $nourritureProposee;
    }

    public function getGrammageNourriture()
    {
        return $this->grammageNourriture;
    }

    public function setGrammageNourriture($grammageNourriture)
    {
        $this->grammageNourriture = $grammageNourriture;
    }

    public function getDatePassage()
    {
        return $this->datePassage;
    }

    public function setDatePassage($datePassage)
    {
        $this->datePassage = $datePassage;
    }

    public function getDetailEtatAnimal()
    {
        return $this->detailEtatAnimal;
    }

    public function setDetailEtatAnimal($detailEtatAnimal)
    {
        $this->detailEtatAnimal = $detailEtatAnimal;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }
}
