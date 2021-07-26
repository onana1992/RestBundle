<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class Product   
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
	protected $description;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $nameCategory;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $nameScategory;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	protected $nameSScategory;

	
	/**
	 * @MongoDB\Field(type="collection") 
	 */
	protected $asc_caracteristics_ids = array();
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="Caracteristic")
	 */
    private $productCaracteristics = array();
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="Caracteristic")
	 */
    private $modelCaracteristics = array();
	
	/** 
	 * @MongoDB\EmbedOne(targetDocument="MarqueEmbedded")
	 */
    private $marque;
	
	
    public function __construct()
    {
        $this->productCaracteristics = new \Doctrine\Common\Collections\ArrayCollection();
        $this->modelCaracteristics = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set nameCategory
     *
     * @param string $nameCategory
     * @return self
     */
    public function setNameCategory($nameCategory)
    {
        $this->nameCategory = $nameCategory;
        return $this;
    }

    /**
     * Get nameCategory
     *
     * @return string $nameCategory
     */
    public function getNameCategory()
    {
        return $this->nameCategory;
    }

    /**
     * Set nameScategory
     *
     * @param string $nameScategory
     * @return self
     */
    public function setNameScategory($nameScategory)
    {
        $this->nameScategory = $nameScategory;
        return $this;
    }

    /**
     * Get idScategory
     *
     * @return string $nameScategory
     */
    public function getNameScategory()
    {
        return $this->nameScategory;
    }

    /**
     * Set nameSScategory
     *
     * @param string $nameSScategory
     * @return self
     */
    public function setNameSScategory($nameSScategory)
    {
        $this->nameSScategory = $nameSScategory;
        return $this;
    }

    /**
     * Get nameSScategory
     *
     * @return string $nameSScategory
     */
    public function getNameSScategory()
    {
        return $this->nameSScategory;
    }

    /**
     * Add productCaracteristic
     *
     * @param RestBundle\Document\Caracteristic $productCaracteristic
     */
    public function addProductCaracteristic(\RestBundle\Document\Caracteristic $productCaracteristic)
    {
        $this->productCaracteristics[] = $productCaracteristic;
    }

    /**
     * Remove productCaracteristic
     *
     * @param RestBundle\Document\Caracteristic $productCaracteristic
     */
    public function removeProductCaracteristic(\RestBundle\Document\Caracteristic $productCaracteristic)
    {
        $this->productCaracteristics->removeElement($productCaracteristic);
    }

    /**
     * Get productCaracteristics
     *
     * @return \Doctrine\Common\Collections\Collection $productCaracteristics
     */
    public function getProductCaracteristics()
    {
        return $this->productCaracteristics;
    }

    /**
     * Add modelCaracteristic
     *
     * @param RestBundle\Document\Caracteristic $modelCaracteristic
     */
    public function addModelCaracteristic(\RestBundle\Document\Caracteristic $modelCaracteristic)
    {
        $this->modelCaracteristics[] = $modelCaracteristic;
    }

    /**
     * Remove modelCaracteristic
     *
     * @param RestBundle\Document\Caracteristic $modelCaracteristic
     */
    public function removeModelCaracteristic(\RestBundle\Document\Caracteristic $modelCaracteristic)
    {
        $this->modelCaracteristics->removeElement($modelCaracteristic);
    }

    /**
     * Get modelCaracteristics
     *
     * @return \Doctrine\Common\Collections\Collection $modelCaracteristics
     */
    public function getModelCaracteristics()
    {
        return $this->modelCaracteristics;
    }

    /**
     * Set ascCaracteristicsIds
     *
     * @param collection $ascCaracteristicsIds
     * @return self
     */
    public function setAscCaracteristicsIds($ascCaracteristicsIds)
    {
        $this->asc_caracteristics_ids = $ascCaracteristicsIds;
        return $this;
    }

    /**
     * Get ascCaracteristicsIds
     *
     * @return collection $ascCaracteristicsIds
     */
    public function getAscCaracteristicsIds()
    {
        return $this->asc_caracteristics_ids;
    }
	
	/**
     * Set marque
     *
     * @param RestBundle\Document\MarqueEmbedded $marque
     * @return self
     */
    public function setMarque(\RestBundle\Document\MarqueEmbedded $marque)
    {
        $this->marque = $marque;
        return $this;
    }

    /**
     * Get marque
     *
     * @return RestBundle\Document\MarqueEmbedded $marque
     */
    public function getMarque()
    {
        return $this->marque;
    }

    
}
