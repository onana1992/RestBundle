<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class  Store  
{

	/**
     * @MongoDB\Id
     */
     private $id;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	 private $name;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	 private $description;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	 private $presentation;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	 private $country;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	 private $town;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	 private $locality;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	 private $idLogo;
	 
	 /**
	  *@MongoDB\EmbedOne(targetDocument="Coordinates")
	  */
      public $coordinates;
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="Catalogue")
	 */
    public $catalogues = array();
	
	
	
	
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
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set presentation
     *
     * @param string $presentation
     * @return self
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;
        return $this;
    }

    /**
     * Get presentation
     *
     * @return string $presentation
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set town
     *
     * @param string $town
     * @return self
     */
    public function setTown($town)
    {
        $this->town = $town;
        return $this;
    }

    /**
     * Get town
     *
     * @return string $town
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set locality
     *
     * @param string $locality
     * @return self
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
        return $this;
    }

    /**
     * Get locality
     *
     * @return string $locality
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set idLogo
     *
     * @param string $idLogo
     * @return self
     */
    public function setIdLogo($idLogo)
    {
        $this->idLogo = $idLogo;
        return $this;
    }

    /**
     * Get idLogo
     *
     * @return string $idLogo
     */
    public function getIdLogo()
    {
        return $this->idLogo;
    }
  
    
    /**
     * Set coordinates
     *
     * @param RestBundle\Document\Coordinates $coordinates
     * @return self
     */
    public function setCoordinates(\RestBundle\Document\Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    /**
     * Get coordinates
     *
     * @return RestBundle\Document\Coordinates $coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }
    
    public function __construct()
    {
        $this->catalogues = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add catalogue
     *
     * @param RestBundle\Document\Catalogue $catalogue
     */
    public function addCatalogue(\RestBundle\Document\Catalogue $catalogue)
    {
        $this->catalogues[] = $catalogue;
    }

    /**
     * Remove catalogue
     *
     * @param RestBundle\Document\Catalogue $catalogue
     */
    public function removeCatalogue(\RestBundle\Document\Catalogue $catalogue)
    {
        $this->catalogues->removeElement($catalogue);
    }

    /**
     * Get catalogues
     *
     * @return \Doctrine\Common\Collections\Collection $catalogues
     */
    public function getCatalogues()
    {
        return $this->catalogues;
    }
}
