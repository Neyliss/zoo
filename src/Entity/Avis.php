<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class Avis
{
    private $id;
    private $pseudo;
    private $avis;
    private $rating;
    private $validatedBy;
    
    /**
     * @var bool
     * @Groups({"avis:read", "avis:write"})
     */
    private $isValidated;

    public function __construct($id, $pseudo, $avis, $rating, $validatedBy = null, $isValidated = false)
    {
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->avis = $avis;
        $this->rating = $rating;
        $this->validatedBy = $validatedBy;
        $this->isValidated = $isValidated;
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

    public function getPseudo()
    {
        return $this->pseudo;
    }

    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }

    public function getAvis()
    {
        return $this->avis;
    }

    public function setAvis($avis)
    {
        $this->avis = $avis;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function getValidatedBy()
    {
        return $this->validatedBy;
    }

    public function setValidatedBy($validatedBy)
    {
        $this->validatedBy = $validatedBy;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setValidated(bool $isValidated)
    {
        $this->isValidated = $isValidated;
    }
}
