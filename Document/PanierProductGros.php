<?php
namespace RestBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use RestBundle\Document\AuthToken;

 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class PanierProductGros{

    /**
     * @MongoDB\Id
     */
    private $id;
	
	/**
     * @MongoDB\Field(type="string")
     */
		protected $name;
		
    /**
     * @MongoDB\Field(type="string")
     */
		protected $typeVente;
		
	/**
     * @MongoDB\Field(type="int")
     */
     protected $number;
	 
	/**
     * @MongoDB\Field(type="int")
     */
     protected $tailleLot;
	 
	/**
     * @MongoDB\Field(type="boolean")
     */
    protected $isCustomized;
		
	/** 
	 * @MongoDB\EmbedMany(targetDocument="PanierProduct")
	 */
		private $products = array();
	

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
     * @return string $idProduct
     */
    public function getName()
    {
        return $this->name;
    }
	
	/**
     * Set typeVente
     *
     * @param string $typeVente
     * @return self
     */
    public function setTypeVente($typeVente)
    {
        $this->typeVente = $typeVente;
        return $this;
    }

    /**
     * Get typeVente
     *
     * @return string $typeVente
     */
    public function getTypeVente()
    {
        return $this->typeVente;
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
	
	/**
     * Set tailleLot
     *
     * @param int $tailleLot
     * @return self
     */
    public function setTailleLot($tailleLot)
    {
        $this->tailleLot = $tailleLot;
        return $this;
    }

    /**
     * Get tailleLot
     *
     * @return int $tailleLot
     */
    public function getTailleLot()
    {
        return $this->tailleLot;
    }

	
	
    /**
     * Add products
     *
     * @param RestBundle\Document\PanierProduct $panierProduct
     */
    public function addProduct(\RestBundle\Document\PanierProduct $panierProduct)
    {
        $this->products[] = $panierProduct;
    }

    /**
     * Remove products
     *
     * @param RestBundle\Document\PanierProduct $panierProduct
     */
    public function removePanierProduct(\RestBundle\Document\PanierProduct $panierProduct)
    {
        $this->products->removeElement($panierProduct);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection $panierProducts
     */
    public function getPanierProducts()
    {
        return $this->products;
    }
	
	 /**
     * Set isCustomized 
     *
     * @param boolean $isCustomized
     * @return self
     */
    public function setIsCustomized($isCustomized)
    {
        $this->isCustomized = $isCustomized;
        return $this;
    }

    /**
     * Get isCustomized
     *
     * @return boolean $isCustomized
     */
    public function getIsCustomized()
    {
        return $this->isCustomized;
    }


}
