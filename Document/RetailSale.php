<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use RestBundle\Document\AuthToken;

 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class RetailSale{

    /**
     * @MongoDB\Id
     */
    private $id;
	
	
	/**
     * @MongoDB\Field(type="float")
     */
    protected $price;
	
	/**
     * @MongoDB\Field(type="float")
     */
    protected $PromotionalPrice;
	
	/**
     * @MongoDB\Field(type="boolean")
     */
    protected $IsinPromotion;
	
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
}
