<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
  * @MongoDB\EmbeddedDocument 
  */ 
class RelaisEmbedded   
{
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $nom;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $quartier;

    /**
     * @MongoDB\Field(type="string") 
     */
    protected $emplacement;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $prix_small;

     /**
     * @MongoDB\Field(type="float")
     */
    protected $prix_medium;

    /**
     * @MongoDB\Field(type="float")
     */
    protected $prix_big;


    /**
     * Set nom
     *
     * @param string $nom
     * @return self
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * Get nom
     *
     * @return string $nom
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set quartier
     *
     * @param string $quartier
     * @return self
     */
    public function setQuartier($quartier)
    {
        $this->quartier = $quartier;
        return $this;
    }

    /**
     * Get quartier
     *
     * @return string $quartier
     */
    public function getQuartier()
    {
        return $this->quartier;
    }

    /**
     * Set emplacement
     *
     * @param string $emplacement
     * @return self
     */
    public function setEmplacement($emplacement)
    {
        $this->emplacement = $emplacement;
        return $this;
    }

    /**
     * Get emplacement
     *
     * @return string $emplacement
     */
    public function getEmplacement()
    {
        return $this->emplacement;
    }


    /**
     * Set prix_small
     *
     * @param float $prix_small
     * @return self
     */
    public function setPrix_small($prix_small)
    {
        $this->prix_small = $prix_small;
        return $this;
    }

    /**
     * Get price
     *
     * @return float $prix_small
     */
    public function getPrix_small()
    {
        return $this->prix_small;
    }


    /**
     * Set prix_medium
     *
     * @param float $prix_medium
     * @return self
     */
    public function setPrix_medium($prix_medium)
    {
        $this->prix_medium = $prix_medium;
        return $this;
    }

    /**
     * Get price
     *
     * @return float $prix_medium
     */
    public function getPrix_medium()
    {
        return $this->prix_medium;
    }


    /**
     * Set prix_big
     *
     * @param float $prix_big
     * @return self
     */
    public function setPrix_big($prix_big)
    {
        $this->prix_big = $prix_big;
        return $this;
    }

    /**
     * Get price
     *
     * @return float prix_big
     */
    public function getPrix_big()
    {
        return $this->prix_big;
    }

}

