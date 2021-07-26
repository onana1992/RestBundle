<?php
namespace RestBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;


 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class LivraisonAdress{

    /**
     * @MongoDB\Id
     */
    private $id;
	
	/**
     * @MongoDB\Field(type="string")
     */
		protected $idProduct;
	
	/**
     * @MongoDB\Field(type="string")
     */
     protected $nameReceptionist;
	 
	/**
     * @MongoDB\Field(type="string")
     */
     protected $secondNameReceptionist;
	 
	/**
     * @MongoDB\Field(type="string")
     */
     protected $telephone1Receptionist;
	 
	/**
     * @MongoDB\Field(type="string")
     */
     protected $telephone2Receptionist;
	 
	/**
     * @MongoDB\Field(type="string")
     */
     protected $region;
	 
	/**
     * @MongoDB\Field(type="string")
     */
     protected $town;
	 
	/**
     * @MongoDB\Field(type="string")
     */
     protected $adresse;

    

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
     * Set idProduct
     *
     * @param string $idProduct
     * @return self
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;
        return $this;
    }

    /**
     * Get idProduct
     *
     * @return string $idProduct
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }

    /**
     * Set nameReceptionist
     *
     * @param string $nameReceptionist
     * @return self
     */
    public function setNameReceptionist($nameReceptionist)
    {
        $this->nameReceptionist = $nameReceptionist;
        return $this;
    }

    /**
     * Get nameReceptionist
     *
     * @return string $nameReceptionist
     */
    public function getNameReceptionist()
    {
        return $this->nameReceptionist;
    }

    /**
     * Set secondNameReceptionist
     *
     * @param string $secondNameReceptionist
     * @return self
     */
    public function setSecondNameReceptionist($secondNameReceptionist)
    {
        $this->secondNameReceptionist = $secondNameReceptionist;
        return $this;
    }

    /**
     * Get secondNameReceptionist
     *
     * @return string $secondNameReceptionist
     */
    public function getSecondNameReceptionist()
    {
        return $this->secondNameReceptionist;
    }

    /**
     * Set telephone1Receptionist
     *
     * @param string $telephone1Receptionist
     * @return self
     */
    public function setTelephone1Receptionist($telephone1Receptionist)
    {
        $this->telephone1Receptionist = $telephone1Receptionist;
        return $this;
    }

    /**
     * Get telephone1Receptionist
     *
     * @return string $telephone1Receptionist
     */
    public function getTelephone1Receptionist()
    {
        return $this->telephone1Receptionist;
    }

    /**
     * Set telephone2Receptionist
     *
     * @param string $telephone2Receptionist
     * @return self
     */
    public function setTelephone2Receptionist($telephone2Receptionist)
    {
        $this->telephone2Receptionist = $telephone2Receptionist;
        return $this;
    }

    /**
     * Get telephone2Receptionist
     *
     * @return string $telephone2Receptionist
     */
    public function getTelephone2Receptionist()
    {
        return $this->telephone2Receptionist;
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
     * Set adresse
     *
     * @param string $adresse
     * @return self
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * Get adresse
     *
     * @return string $adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }
}
