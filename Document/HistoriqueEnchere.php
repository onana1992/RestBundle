<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
  * @MongoDB\EmbeddedDocument 
  */ 
class HistoriqueEnchere   
{

	
	/**
	 * @MongoDB\Field(type="int") 
	 */
	protected $price;


    /**
     * @MongoDB\Field(type="date") 
     */
    private $date;

	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $idUser;


    /**
     * Set price
     *
     * @param int $price
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
     * @return int $price
     */
    public function getPrice()
    {
        return $this->price;
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
     * Get $date
     *
     * @return date $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get idUser
     *
     * @return string $idUser
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idUser
     *
     * @param string $idUser
     * @return self
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }
   
}
