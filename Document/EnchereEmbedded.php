<?php
namespace RestBundle\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use RestBundle\Document\AuthToken;

 /**
  * @MongoDB\EmbeddedDocument 
  */ 
class EnchereEmbedded{

    /**
     * @MongoDB\Id
     */
    private $id;
	
	/**
     * @MongoDB\Field(type="string")
     */
		protected $numEnchere;
	
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
     * Set numEnchere
     *
     * @param string $numEnchere
     * @return self
     */
    public function setNumEnchere($numEnchere)
    {
        $this->numEnchere = $numEnchere;
        return $this;
    }

    /**
     * Get numEnchere
     *
     * @return string $numEnchere
     */
    public function getNumEnchere()
    {
        return $this->numEnchere;
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
