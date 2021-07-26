<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class BuyWithMe   
{

	/**
     * @MongoDB\Id
     */
    protected $id;
	
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $nameProduct;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $size;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $actualQuantity;
	
	/**
     * @MongoDB\Field(type="date")
     */
    protected $creationDate;
	
	
	/**
     * @MongoDB\Field(type="boolean")
     */
    protected $isCurrent;
	
	
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
     * Set nameProduct
     *
     * @param string $nameProduct
     * @return self
     */
    public function setNameProduct($nameProduct)
    {
        $this->nameProduct = $nameProduct;
        return $this;
    }

    /**
     * Get nameProduct
     *
     * @return string $nameProduct
     */
    public function getNameProduct()
    {
        return $this->nameProduct;
    }
	
	/**
     * Set size
     *
     * @param int $size
     * @return self
     */
    public function setSize($initialQuantity)
    {
        $this->$size= $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return int $size
     */
    public function getSize()
    {
        return $this->$size;
    }
	
	
	/**
     * Set actualQuantity
     *
     * @param int $actualQuantity
     * @return self
     */
    public function setActualQuantity($actualQuantity)
    {
        $this->actualQuantity= $actualQuantity;
        return $this;
    }

    /**
     * Get actualQuantity
     *
     * @return int $actualQuantity
     */
    public function getActualQuantity()
    {
        return $this->$actualQuantity;
    }

    
    /**
     * Set creationDate
     *
     * @param date $creationDate
     * @return self
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }

    /**
     * Get creationDate
     *
     * @return date $creationDate
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
	
	
	/**
     * Set isCurrent
     *
     * @param boolean $isCurrent
     * @return self
     */
    public function setIsCurrent($isCurrent)
    {
        $this->isCurrent = $isCurrent;
        return $this;
    }

    /**
     * Get isCurrent
     *
     * @return boolean $isCurrent
     */
    public function getIsCurrent()
    {
        return $this->isCurrent;
    }

    
}
