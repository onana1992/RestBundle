<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use RestBundle\Document\AuthToken;

 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class WholeSale{

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
     * @MongoDB\Field(type="float")
     */
    protected $PromotionalPrice;
	
	/**
     * @MongoDB\Field(type="boolean")
     */
    protected $IsinPromotion;
	
	/**
     * @MongoDB\Field(type="boolean")
     */
    protected $isPersonalizable;
	
	/**
     * @MongoDB\Field(type="date")
     */
    protected $endPromotionDate;
	

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
     * Set promotionalPrice
     *
     * @param float $promotionalPrice
     * @return self
     */
    public function setPromotionalPrice($promotionalPrice)
    {
        $this->PromotionalPrice = $promotionalPrice;
        return $this;
    }

    /**
     * Get promotionalPrice
     *
     * @return float $promotionalPrice
     */
    public function getPromotionalPrice()
    {
        return $this->PromotionalPrice;
    }

    /**
     * Set isinPromotion
     *
     * @param boolean $isinPromotion
     * @return self
     */
    public function setIsinPromotion($isinPromotion)
    {
        $this->IsinPromotion = $isinPromotion;
        return $this;
    }

    /**
     * Get isinPromotion
     *
     * @return boolean $isinPromotion
     */
    public function getIsinPromotion()
    {
        return $this->IsinPromotion;
    }

    /**
     * Set endPromotionDate
     *
     * @param date $endPromotionDate
     * @return self
     */
    public function setEndPromotionDate($endPromotionDate)
    {
        $this->endPromotionDate = $endPromotionDate;
        return $this;
    }

    /**
     * Get endPromotionDate
     *
     * @return date $endPromotionDate
     */
    public function getEndPromotionDate()
    {
        return $this->endPromotionDate;
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
