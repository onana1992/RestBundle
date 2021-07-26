<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class Paiement
{	
	/**
     * @MongoDB\Id
     */
    private $id;

    /**
     * @MongoDB\Field(type="date") 
     */
    private $date;
	
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $montant;


    /**
     * @MongoDB\Field(type="string") 
     */
    private $numeroCmd;


    /**
     * @MongoDB\Field(type="string") 
     */
    private $devise;


    /**
     * @MongoDB\Field(type="string") 
     */
    private $refTransaction;


    /**
     * Set date
     *
     * @param date $date
     * @return self
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }


    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->Date;
        
    }
	

    /**
     * Set numeroCmd
     *
     * @param string $numeroCmd
     * @return self
     */
    public function setNumeroCmd($numeroCmd)
    {
        $this->numeroCmd = $numeroCmd;
        return $this;
    }


    /**
     * Get numeroCmd
     *
     * @return string $numeroCmd
     */
    public function getNumeroCmd()
    {
        return $this->numeroCmd;
    }


     /**
     * Set montant
     *
     * @param string $montant
     * @return self
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
        return $this;
    }


    /**
     * Get montant
     *
     * @return string $montant
     */
    public function getMontant()
    {
        return $this->montant;
    }

     /**
     * Set refTransaction
     *
     * @param string $refTransaction
     * @return self
     */
    public function setRefTransaction($refTransaction)
    {
        $this->refTransaction = $refTransaction;
        return $this;
    }


    /**
     * Get refTransaction
     *
     * @return string $refTransaction
     */
    public function getRefTransaction()
    {
        return $this->refTransaction;
    }

     /**
     * Set devise
     *
     * @param string $devise
     * @return self
     */
    public function setDevise($devise)
    {
        $this->devise = $devise;
        return $this;
    }


    /**
     * Get devise
     *
     * @return string $devise
     */
    public function getDevise()
    {
        return $this->devise;
    }

}
