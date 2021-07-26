<?php
namespace RestBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use RestBundle\Document\AuthToken;

 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class PanierBWMProduct{

    /**
     * @MongoDB\Id
     */
    private $id;
	
	/**
     * @MongoDB\Field(type="string")
     */
		protected $idProduct;
	
	/**
     * @MongoDB\Field(type="int")
     */
     protected $number;
	 

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
     * Set number
     *
     * @param int $number
     * @return self
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Get number
     *
     * @return int $number
     */
    public function getNumber()
    {
        return $this->number;
    }

}

