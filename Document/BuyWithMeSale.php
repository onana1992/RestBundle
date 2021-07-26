<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use RestBundle\Document\AuthToken;

 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class BuyWithMeSale{

    /**
     * @MongoDB\Id
     */
    private $id;
	
	/**
     * @MongoDB\Field(type="float")
     */
    protected $price;

	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $lotQuantity;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $duree;
	
	
	/**
     * @MongoDB\Field(type="boolean")
     */
    protected $isPersonalizable;
	
	/**
     * @MongoDB\Field(type="boolean")
     */
    protected $isActivated;
	
	/**
     * Set isActivated
     *
     * @param boolean $isActivated
     * @return self
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;
        return $this;
    }

    /**
     * Get isActivated
     *
     * @return boolean $isActivated
     */
    public function getIsActivated()
    {
        return $this->isActivated;
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
     * Set price
     *
     * @param float $price
     * @return self
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    
    /**
     * Set lotQuantity
     *
     * @param int $lotQuantity
     * @return self
     */
    public function setLotQuantity($lotQuantity)
    {
        $this->lotQuantity = $lotQuantity;
        return $this;
    }

    /**
     * Get lotQuantity
     *
     * @return int $lotQuantity
     */
    public function getLotQuantity()
    {
        return $this->lotQuantity;
    }
	
	/**
     * Set duree
     *
     * @param int $duree
     * @return self
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;
        return $this;
    }

    /**
     * Get duree
     *
     * @return int $duree
     */
    public function getDuree()
    {
        return $this->duree;
    }

    

	 /**
     * Set isPersonalizable
     *
     * @param boolean $isPersonalizable
     * @return self
     */
    public function setIsPersonalizable($isPersonalizable)
    {
        $this->isPersonalizable = $isPersonalizable;
        return $this;
    }

    /**
     * Get isPersonalizable
     *
     * @return boolean $isPersonalizable
     */
    public function getIsinPersonalizable()
    {
        return $this->isPersonalizable;
    }
	
	
	
	
}
