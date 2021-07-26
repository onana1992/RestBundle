<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;


/** 
 * @MongoDB\EmbeddedDocument
 */ 
class filterCaracteristic   
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
	private $value;
	

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
     * Set value
     *
     * @param collection $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return collection $value
     */
    public function getValue()
    {
        return $this->value;
    }
}
