<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;


/** 
 * @MongoDB\Document
 */ 
class ASCCaracteristic   
{

	/**
     * @MongoDB\Id
     */
    private $id;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $name;
	
	/**
	 * @MongoDB\Field(type="collection") 
	 */
	private $unities;

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
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set unities
     *
     * @param collection $unities
     * @return self
     */
    public function setUnities($unities)
    {
        $this->unities = $unities;
        return $this;
    }

    /**
     * Get unities
     *
     * @return collection $unities
     */
    public function getUnities()
    {
        return $this->unities;
    }
}
