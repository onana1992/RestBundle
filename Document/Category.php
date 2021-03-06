<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;


/** 
 * @MongoDB\Document
 */ 
class Category   
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
     * @MongoDB\Field(type="string") 
     */
    private $urlIcone;
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="SCategory")
	 */
    private $categories = array();
	
	

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
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set urlIcone
     *
     * @param string $urlIcone
     * @return self
     */
    public function setUrlIcone($urlIcone)
    {
        $this->urlIcone = $urlIcone;
        return $this;
    }

    /**
     * Get urlIcone
     *
     * @return string $urlIcone
     */
    public function getUrlIcone()
    {
        return $this->urlIcone;
    }
    


    
    /**
     * Add category
     *
     * @param RestBundle\Document\SCategory $category
     */
    public function addCategory(\RestBundle\Document\SCategory $category)
    {
        $this->categories[] = $category;
    }

    /**
     * Remove category
     *
     * @param RestBundle\Document\SCategory $category
     */
    public function removeCategory(\RestBundle\Document\SCategory $category)
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
