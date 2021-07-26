<?php
namespace RestBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use RestBundle\Document\AuthToken;

 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class Favorite{

    /**
     * @MongoDB\Id
     */
    private $id;
	
	/**
     * @MongoDB\Field(type="string")
     */
		protected $idProduct;
	
	/**
     * @MongoDB\Field(type="date")
     */
     protected $addDate;

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
     * Set addDate
     *
     * @param date $addDate
     * @return self
     */
    public function setAddDate($addDate)
    {
        $this->addDate = $addDate;
        return $this;
    }

    /**
     * Get addDate
     *
     * @return date $addDate
     */
    public function getAddDate()
    {
        return $this->addDate;
    }
}
