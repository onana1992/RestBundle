<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
  * @MongoDB\EmbeddedDocument 
  */ 
class MarqueEmbedded   
{

	
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $name;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $idLogo;


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
}
