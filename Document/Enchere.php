<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class Enchere
{	

    /**
     * @MongoDB\Id
     */
    protected $id;


    /**
     * @MongoDB\Field(type="string") 
     */
    private $numEnchere;

    /**
     * @MongoDB\Field(type="string") 
     */
    private $name;

    /**
     * @MongoDB\Field(type="string") 
     */
    protected $etat;

    /**
     * @MongoDB\Field(type="string") 
     */
    private $statut;

	
	/**
	 * @MongoDB\Field(type="date") 
	 */
	private $initDate;	

    /**
     * @MongoDB\Field(type="date") 
     */
    private $closeDate;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $initPrice;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $quantity;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $time;

    /**
     * @MongoDB\Field(type="string") 
     */
    protected $description;
	
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idImage;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idBigImage1;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idBigImage2;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idBigImage3;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idBigImage4;

    /**
     * @MongoDB\Field(type="string") 
     */
    private $category;
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="Detail")
	 */
    private $details = array();


    /** 
     * @MongoDB\EmbedMany(targetDocument="HistoriqueEnchere")
     */
    private $historiques = array();
	

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
     * Set etat 
     *
     * @param string $etat
     * @return self
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
        return $this;
    }


    /**
     * Get etat
     *
     * @return string $etat
     */
    public function getEtat()
    {
        return $this->etat;
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
     * Set statut
     *
     * @param string $statut
     * @return self
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
        return $this;
    }

    /**
     * Get statut
     *
     * @return string $statut
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return string $category
     */
    public function getCategory()
    {
        return $this->category;
    }


    /**
     * Set initDate
     *
     * @param date $initDate
     * @return self
     */
    public function setInitDate($initDate)
    {
        $this->initDate = $initDate;
        return $this;
    }


    /**
     * Get $initDate
     *
     * @return date $initDate
     */
    public function getInitDate()
    {
        return $this->initDate;
    }




    /**
     * Set closeDate
     *
     * @param date $closeDate
     * @return self
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;
        return $this;
    }

    /**
     * Get $closeDate
     *
     * @return date $closeDate
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }


	/**
     * Set initPrice
     *
     * @param int $initPrice
     * @return self
     */
    public function setInitPrice($initPrice)
    {
        $this->initPrice = $initPrice;
        return $this;
    }

    /**
     * Get initPrice
     *
     * @return int $initPrice
     */
    public function getInitPrice()
    {
        return $this->initPrice;
    }


    /**
     * Set quantity
     *
     * @param int $quantity
     * @return self
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get quantity
     *
     * @return int $quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }


    /**
     * Set time
     *
     * @param int $time
     * @return self
     */
    public function setTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * Get time
     *
     * @return int $time
     */
    public function getTime()
    {
        return $this->time;
    }


    /**
     * Set idImage
     *
     * @param string $idImage
     * @return self
     */
    public function setIdImage($idImage)
    {
        $this->idImage = $idImage;
        return $this;
    }

    /**
     * Get idImage
     *
     * @return string $idImage
     */
    public function getIdImage()
    {
        return $this->idImage;
    }

    

    /**
     * Set idBigImage1
     *
     * @param string $idBigImage1
     * @return self
     */
    public function setIdBigImage1($idBigImage1)
    {
        $this->idBigImage1 = $idBigImage1;
        return $this;
    }

    /**
     * Get idBigImage1
     *
     * @return string $idBigImage1
     */
    public function getIdBigImage1()
    {
        return $this->idBigImage1;
    }

    /**
     * Set idBigImage2
     *
     * @param string $idBigImage2
     * @return self
     */
    public function setIdBigImage2($idBigImage2)
    {
        $this->idBigImage2 = $idBigImage2;
        return $this;
    }

    /**
     * Get idBigImage2
     *
     * @return string $idBigImage2
     */
    public function getIdBigImage2()
    {
        return $this->idBigImage2;
    }

    /**
     * Set idBigImage3
     *
     * @param string $idBigImage3
     * @return self
     */
    public function setIdBigImage3($idBigImage3)
    {
        $this->idBigImage3 = $idBigImage3;
        return $this;
    }

    /**
     * Get idBigImage3
     *
     * @return string $idBigImage3
     */
    public function getIdBigImage3()
    {
        return $this->idBigImage3;
    }

    /**
     * Set idBigImage4
     *
     * @param string $idBigImage4
     * @return self
     */
    public function setIdBigImage4($idBigImage4)
    {
        $this->idBigImage4 = $idBigImage4;
        return $this;
    }

    /**
     * Get idBigImage4
     *
     * @return string $idBigImage4
     */
    public function getIdBigImage4()
    {
        return $this->idBigImage4;
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
     * Add detail
     *
     * @param RestBundle\Document\Detail $detail
     */
    public function addDetail(\RestBundle\Document\Detail $detail)
    {
        $this->details[] = $detail;
    }


    /**
     * Remove detail
     *
     * @param RestBundle\Document\Detail $detail
     */
    public function removeDetail(\RestBundle\Document\Detail $detail)
    {
        $this->details->removeElement($detail);
    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection $details
     */
    public function getDetails()
    {
        return $this->details;
    }


    /**
     * Add historiques
     *
     * @param RestBundle\Document\HistoriqueEnchere $historique
     */
    public function addHistoriques(\RestBundle\Document\HistoriqueEnchere $historique)
    {
        $this->historiques[] = $historique;

    }


    /**
     * Remove historique
     *
     * @param RestBundle\Document\HistoriqueEnchere $historique
     */
    public function removeHistoriques(\RestBundle\Document\HistoriqueEnchere $historique)
    {
        $this->historiques->removeElement($historique);
    }

    /**
     * Get historiques
     *
     * @return \Doctrine\Common\Collections\HistoriqueEnchere $historiques
     */
    public function getHistoriques()
    {
        return $this->historiques;
    }


}
