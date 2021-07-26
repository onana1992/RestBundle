<?php
namespace RestBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;


 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class CommandBWMProduct{
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $name;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $quantity;
	
	
	/**
     * @MongoDB\Field(type="float")
     */
    protected $price;
	
	/**
     * Set quantity
     *
     * @param int $quantity
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idCurrentBWM;

    /**
     * Get type
     *
     * @return int $quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
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
     * Set idCurrentBWM
     *
     * @param string $idCurrentBWM
     * @return self
     */
    public function setIdCurrentBWM($idCurrentBWM)
    {
        $this->idCurrentBWM= $idCurrentBWM;
        return $this;
    }

    /**
     * Get idCurrentBWM
     *
     * @return string $idCurrentBWM
     */
    public function getIdCurrentBWM()
    {
        return $this->idCurrentBWM;
    }
}
