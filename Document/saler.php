<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;


/** 
 * @MongoDB\Document
 */ 
class Saler
{	
	/**
     * @MongoDB\Id
     */
    private $id;

	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $nom;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $tel1;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $tel2;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $adresse;
	
	/**
     * Set nom
     *
     * @param string $nom
     * @return self
     */
    public function setNom($nom)
    {
        $this->nom= $nom;
        return $this;
    }


    /**
     * Get nom
     *
     * @return string $nom
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set tel1
     *
     * @param string $tel1
     * @return self
     */
    public function setTel1($tel1)
    {
        $this->tel1 = $tel1;
        return $this;
    }

    /**
     * Get tel1
     *
     * @return string $tel1
     */
    public function getTel1()
    {
        return $this->tel1;
    }
	
	/**
     * Set tel2
     *
     * @param string $tel2
     * @return self
     */
    public function setTel2($tel2)
    {
        $this->tel2 = $tel1;
        return $this;
    }

    /**
     * Get tel2
     *
     * @return string $tel2
     */
    public function getTel2()
    {
        return $this->tel2;
    }
	
	

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return self
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * Get adresse
     *
     * @return string $adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }
}
