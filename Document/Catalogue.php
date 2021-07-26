<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
  * @MongoDB\EmbeddedDocument 
  */
class  Catalogue  
{

	/**
     * @MongoDB\Id
     */
     private $id;
	 
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idProduct;
	
	/** 
	 * @MongoDB\EmbedOne(targetDocument="RetailSale")
	 */
    public $retailSale;	
	

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
     * Set retailSale
     *
     * @param RestBundle\Document\RetailSale $retailSale
     * @return self
     */
    public function setRetailSale(\RestBundle\Document\RetailSale $retailSale)
    {
        $this->retailSale = $retailSale;
        return $this;
    }

    /**
     * Get retailSale
     *
     * @return RestBundle\Document\RetailSale $retailSale
     */
    public function getRetailSale()
    {
        return $this->retailSale;
    }
}
