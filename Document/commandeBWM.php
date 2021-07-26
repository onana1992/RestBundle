<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class CommandeBWM 
{	
	/**
     * @MongoDB\Id
     */
    private $id;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $reference;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $login;
	
	
	/**
	 * @MongoDB\Field(type="date") 
	 */
	private $commandDate;
	
	/**
	 * @MongoDB\Field(type="date") 
	 */
	private $endBWMDate;

	/** 
	 * @MongoDB\EmbedOne(targetDocument="LivraisonAdress")
	 */
    private $livraisonAdress;
	
	/** 
	 * @MongoDB\EmbedOne(targetDocument="Livraison")
	 */
    private $livraison;
	
	/**
	 * @MongoDB\Field(type="boolean") 
	 */
	private $isPaid;
	
	/**
	 * @MongoDB\Field(type="boolean") 
	 */
	private $isShipped;
	
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="CommandBWMProduct")
	 */
    private $commandProduct = array();
	
	
    /**
     * Set reference
     *
     * @param string $reference
     * @return self
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Get reference
     *
     * @return string $reference
     */
    public function getReference()
    {
        return $this->reference;
    }
	
	/**
     * Set login
     *
     * @param string $login
     * @return self
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Get login
     *
     * @return string $login
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set commandDate
     *
     * @param date $commandDate
     * @return self
     */
    public function setCommandDate($commandDate)
    {
        $this->commandDate = $commandDate;
        return $this;
    }

    /**
     * Get commandDate
     *
     * @return date $commandDate
     */
    public function getCommandDate()
    {
        return $this->commandDate;
		
    }
	
	/**
     * Set endBWMDate
     *
     * @param date $endBWMDate
     * @return self
     */
    public function setEndBWMDate($endBWMDate)
    {
        $this->endBWMDate = $endBWMDate;
        return $this;
    }

    /**
     * Get $endBWMDate
     *
     * @return date $endBWMDate
     */
    public function getEndBWMDate()
    {
        return $this->endBWMDate;
		
    }
	
	
	/**
     * Set $livraisonAdress
     *
     * @param RestBundle\Document\LivraisonAdress $livraisonAdress
     * @return self
     */
    public function setLivraisonAdress(\RestBundle\Document\LivraisonAdress $livraisonAdress)
    {
        $this->livraisonAdress = $livraisonAdress;
        return $this;
    }

    /**
     * Get livraisonAdress
     *
     * @return RestBundle\Document\LivraisonAdress $livraisonAdress
     */
    public function getLivraisonAdress()
    {
        return $this->livraisonAdress;
    }
	
	/**
     * Set isPaid
     *
     * @param boolean $isPaid
     * @return self
     */
    public function setIsPaid($isPaid)
    {
        $this->isPaid = $isPaid;
        return $this;
    }

    /**
     * Get isPaid
     *
     * @return boolean $isPaid
     */
    public function getIsPaid()
    {
        return $this->isPaid;
    }
	
	/**
     * Set isShipped
     *
     * @param boolean $isShipped
     * @return self
     */
    public function setIsShipped($isShipped)
    {
        $this->isShipped = $isShipped;
        return $this;
    }

    /**
     * Get isShipped
     *
     * @return boolean $isShipped
     */
    public function getIsShipped()
    {
        return $this->isShipped;
    }
	
	/**
     * Set $livraison
     *
     * @param RestBundle\Document\Livraison $livraison
     * @return self
     */
    public function setLivraison(\RestBundle\Document\Livraison $livraison)
    {
        $this->livraison = $livraison;
        return $this;
    }

    /**
     * Get livraisonAdress
     *
     * @return RestBundle\Document\Livraison $livraison
     */
    public function getLivraison()
    {
        return $this->livraison;
    }

   

    /**
     * Add commandProduct
     *
     * @param RestBundle\Document\CommandBWMProduct $commandProduct
     */
    public function addCommandProduct(\RestBundle\Document\CommandBWMProduct $commandProduct)
    {
        $this->commandProduct[] = $commandProduct;
    }

    /**
     * Remove commandProduct
     *
     * @param RestBundle\Document\CommandBWMProduct $commandProduct
     */
    public function removeCommandProduct(\RestBundle\Document\CommandBWMProduct $commandProduct)
    {
        $this->details->removeElement($commandProduct);
    }

    /**
     * Get commandProduct
     *
     * @return \Doctrine\Common\Collections\Collection $commandProduct
     */
    public function getCommandProduct()
    {
        return $this->commandProduct;
    }

}
