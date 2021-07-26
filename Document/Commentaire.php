<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
  * @MongoDB\EmbeddedDocument 
  */ 
class Commentaire   
{

	/**
     * @MongoDB\Field(type="string")
     */
    protected $name;
	
	/**
     * @MongoDB\Field(type="string")
     */
    protected $firstName;
	
	/**
     * @MongoDB\Field(type="string")
     */
    protected $login;
	
	/**
     * @MongoDB\Field(type="string")
     */
    protected $valeur;
	
	/**
     * @MongoDB\Field(type="date")
     */
    protected $date;
	
	
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
     * Set firstName
     *
     * @param string $firstName
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string $firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
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
     * Set valeur
     *
     * @param string $valeur
     * @return self
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;
        return $this;
    }

    /**
     * Get valeur
     *
     * @return string $valeur
     */
    public function getValeur()
    {
        return $this->valeur;
    }
	
	
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
        return $this->date;
		
    }


}
