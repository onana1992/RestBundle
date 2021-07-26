<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class Localite  
{

    /**
     * @MongoDB\Id
     */
    private $id;
    
    /**
     * @MongoDB\Field(type="string") 
     */
    private $region;
    
    /** 
     * @MongoDB\EmbedMany(targetDocument="Ville")
     */
    private $villes = array();
    
    public function __construct()
    {
        $this->villes = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set region
     *
     * @param string $region
     * @return self
     */
    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * Get region
     *
     * @return string $region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Add ville
     *
     * @param RestBundle\Document\Ville $ville
     */
    public function addVille(\RestBundle\Document\Ville $ville)
    {
        $this->villes[] = $ville;
    }

    /**
     * Remove ville
     *
     * @param RestBundle\Document\Ville $ville
     */
    public function removeVille(\RestBundle\Document\Ville $ville)
    {
        $this->villes->removeElement($ville);
    }

    /**
     * Get villes
     *
     * @return \Doctrine\Common\Collections\Collection $villes
     */
    public function getVilles()
    {
        return $this->villes;
    }
}
