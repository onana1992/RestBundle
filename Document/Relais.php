<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class Relais  
{

    /**
     * @MongoDB\Id
     */
    private $id;
    
    /**
     * @MongoDB\Field(type="string") 
     */
    private $ville;

    /**
     * @MongoDB\Field(type="string") 
     */
    private $delai;
    
    /** 
     * @MongoDB\EmbedMany(targetDocument="RelaisEmbedded")
     */
    private $relais = array();
    
    public function __construct()
    {
        $this->relais = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set ville
     *
     * @param string $ville
     * @return self
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
        return $this;
    }

    /**
     * Get ville
     *
     * @return string $ville
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set delai
     *
     * @param string $delai
     * @return self
     */
    public function setDelai($delai)
    {
        $this->delai = $delai;
        return $this;
    }

    /**
     * Get delai
     *
     * @return string $delai
     */
    public function getDelai()
    {
        return $this->delai;
    }

    /**
     * Add relais
     *
     * @param RestBundle\Document\RelaisEmbedded $relais
     */
    public function addRelais(\RestBundle\Document\RelaisEmbedded $relais)
    {
        $this->relais[] = $relais;
    }

    /**
     * Remove relais
     *
     * @param RestBundle\Document\RelaisEmbedded $relais
     */
    public function removeRelais(\RestBundle\Document\RelaisEmbedded  $relais)
    {
        $this->villes->removeElement($relais);
    }

    /**
     * Get relais
     *
     * @return \Doctrine\Common\Collections\RelaisEmbedded  $relais
     */
    public function getRelais()
    {
        return $this->relais;
    }
}
