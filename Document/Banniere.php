<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
  * @MongoDB\Document
  */
class  Banniere 
{

	/**
     * @MongoDB\Id
     */
     private $id;
	 
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idImage;

    /**
     * @MongoDB\Field(type="string") 
     */
    private $page;

    /**
     * @MongoDB\Field(type="int") 
     */
    private $priority;
	
	
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
     * Set idImage
     *
     * @param string $idImage
     * @return self
     */
    public function setIdImage($idImage)
    {
        $this->idImage= $idImage;
        return $this;
    }

    /**
     * Get idImage
     *
     * @return string $idImage
     */
    public function getImage()
    {
        return $this->idImage;
    }

    /**
     * Set page
     *
     * @param string $page
     * @return self
     */
    public function setPage($page)
    {
        $this->page= $page;
        return $this;
    }

    /**
     * Get page
     *
     * @return string $page
     */
    public function getPage()
    {
        return $this->page;
    }


    /**
     * Set priority
     *
     * @param int $priority
     * @return self
     */
    public function setPriority($priority)
    {
        $this->priority= $priority;
        return $this;
    }

    /**
     * Get priority
     *
     * @return int $priority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    
    
    
}
