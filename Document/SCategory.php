<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;



/** 
 * @MongoDB\EmbeddedDocument
 */ 
class SCategory   
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
	 * @MongoDB\Field(type="string") 
	 */
	private $urlBaniere;
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="SSCategory")
	 */
    private $categories = array();
	

    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set urlBaniere
     *
     * @param string $urlBaniere
     * @return self
     */
    public function setUrlBaniere($urlBaniere)
    {
        $this->urlBaniere = $urlBaniere;
        return $this;
    }

    /**
     * Get urlBaniere
     *
     * @return string $urlBaniere
     */
    public function getUrlBaniere()
    {
        return $this->urlBaniere;
    }

    /**
     * Add category
     *
     * @param RestBundle\Document\SSCategory $category
     */
    public function addCategory(\RestBundle\Document\SSCategory $category)
    {
        $this->categories[] = $category;
    }

    /**
     * Remove category
     *
     * @param RestBundle\Document\SSCategory $category
     */
    public function removeCategory(\RestBundle\Document\SSCategory $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
