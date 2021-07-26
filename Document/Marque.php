<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class Marque   
{

	/**
     * @MongoDB\Id
     */
    protected $id;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $name;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $idLogo;
	
	/**
	 * @MongoDB\Field(type="int") 
	 */
	protected $popularity;
	

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
     * Set idLogo
     *
     * @param string $idLogo
     * @return self
     */
    public function setIdLogo($idLogo)
    {
        $this->idLogo = $idLogo;
        return $this;
    }

    /**
     * Get idLogo
     *
     * @return string $idLogo
     */
    public function getIdLogo()
    {
        return $this->idLogo;
    }
	
	/**
     * Set popularity
     *
     * @param int $popularity
     * @return self
     */
    public function setPopularity($popularity)
    {
        $this->popularity= $popularity;
        return $this;
    }

    /**
     * Get popularity
     *
     * @return int $popularity
     */
    public function getPopularity()
    {
        return $this->popularity;
    }
}
