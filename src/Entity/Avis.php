<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

class Avis
{
    /**
     * @var string
     * @Groups({"avis:read", "avis:write"})
     */
    private $id;

    /**
     * @var string
     * @Groups({"avis:read", "avis:write"})
     */
    private $pseudo;

    /**
     * @var string
     * @Groups({"avis:read", "avis:write"})
     */
    private $avis;

    /**
     * @var int
     * @Groups({"avis:read", "avis:write"})
     */
    private $rating;

    /**
     * @var string|null
     * @Groups({"avis:read", "avis:write"})
     */
    private $validatedBy;

    public function __construct($id, $pseudo, $avis, $rating, $validatedBy = null)
    {
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->avis = $avis;
        $this->rating = $rating;
        $this->validatedBy = $validatedBy;
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
}
