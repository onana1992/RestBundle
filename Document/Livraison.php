<?php
namespace RestBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;


 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class Livraison{
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $type;
		
	/**
	 * @MongoDB\Field(type="date") 
	 */
	private $delais;
	
	/**
     * @MongoDB\Field(type="float")
     */
    protected $price;
	
	/**
     * Set type
     *
     * @param int $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return int $type
     */
    public function getType()
    {
        return $this->type;
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
     * Set delais
     *
     * @param date $delais
     * @return self
     */
    public function setDelais($delais)
    {
        $this->delais = $delais;
        return $this;
    }

    /**
     * Get delais
     *
     * @return date $insertionDate
     */
    public function getDelais()
    {
        return $this->delais;
    }
}
