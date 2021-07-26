<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class ProductModel extends Product
{	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $idProduit;

    /**
     * @MongoDB\Field(type="string") 
     */
    protected $idSeller;

    /**
     * @MongoDB\Field(type="string") 
     */
    private $taille;


	/**
     * @MongoDB\Field(type="int")
     */
    protected $quantity;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $popularity;
	
	/**
	 * @MongoDB\Field(type="date") 
	 */
	private $insertionDate;	
	
	/**
     * @MongoDB\Field(type="float")
     */
    protected $actualPrice;
	
	/**
     * @MongoDB\Field(type="float")
     */
    protected $weight;
	
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
	 * @MongoDB\EmbedMany(targetDocument="Detail")
	 */
    private $details = array();
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="Commentaire")
	 */
    private $commentaires = array();
	
	/** 
	 * @MongoDB\EmbedOne(targetDocument="RetailSale")
	 */
    private $retailSale;
	
	/** 
	 * @MongoDB\EmbedOne(targetDocument="WholeSale")
	 */
    private $wholeSale;
	
	/** 
	 * @MongoDB\EmbedOne(targetDocument="BuyWithMeSale")
	 */
    private $buyWithMeSale;
	
	/** 
	 * @MongoDB\EmbedOne(targetDocument="Notation")
	 */
    private $notation;
	
	/**
     * @MongoDB\Field(type="boolean")
     */
    protected $isActivated;

    /**
     * @MongoDB\Field(type="boolean")
     */
    protected $isVirtual;
	
	
	
    /**
     * Set idProduit
     *
     * @param string $idProduit
     * @return self
     */
    public function setIdProduit($idProduit)
    {
        $this->idProduit = $idProduit;
        return $this;
    }

    /**
     * Get idProduit
     *
     * @return string $idProduit
     */
    public function getIdProduit()
    {
        return $this->idProduit;
    }

    /**
     * Set taille
     *
     * @param string $taille
     * @return self
     */
    public function setTaille($taille)
    {
        $this->taille = $taille;
        return $this;
    }

    /**
     * Get taille
     *
     * @return string $taille
     */
    public function getTaille()
    {
        return $this->taille;
    }


    /**
     * Set id seller 
     *
     * @param string $idSeller
     * @return self
     */
    public function setIdSeller($idSeller)
    {
        $this->idSeller = $idSeller;
        return $this;
    }

    /**
     * Get idSeller
     *
     * @return string $idSeller
     */
    public function getIdSeller()
    {
        return $this->idSeller;
    }

    /**
     * Set insertionDate
     *
     * @param date $insertionDate
     * @return self
     */
    public function setInsertionDate($insertionDate)
    {
        $this->insertionDate = $insertionDate;
        return $this;
    }

    /**
     * Get insertionDate
     *
     * @return date $insertionDate
     */
    public function getInsertionDate()
    {
        return $this->insertionDate;
    }

    /**
     * Set actualPrice
     *
     * @param float $actualPrice
     * @return self
     */
    public function setActualPrice($actualPrice)
    {
        $this->actualPrice = $actualPrice;
        return $this;
    }

    /**
     * Get actualPrice
     *
     * @return float $actualPrice
     */
    public function getActualPrice()
    {
        return $this->actualPrice;
    }
	
	
	/**
     * Set weight
     *
     * @param float $weight
     * @return self
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return float $weight
     */
    public function getWeight()
    {
        return $this->weight;
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
     * Set isActivated
     *
     * @param boolean $isActivated
     * @return self
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;
        return $this;
    }

    /**
     * Get isActivated
     *
     * @return boolean $isActivated
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * Set $virtual
     *
     * @param boolean $isVirtual
     * @return self
     */
    public function setIsVirtual($isVirtual)
    {
        $this->isVirtual = $isVirtual;
        return $this;
    }

    /**
     * Get $isVirtual
     *
     * @return boolean $isVirtual
     */
    public function getIsVirtual()
    {
        return $this->isVirtual;
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
     * Set retailSale
     *
     * @param RestBundle\Document\RetailSale $retailSale
     * @return self
     */
    public function setRetailSale(\RestBundle\Document\RetailSale $retailSale)
    {
        $this->retailSale = $retailSale;
        return $this;
    }

    /**
     * Get retailSale
     *
     * @return RestBundle\Document\RetailSale $retailSale
     */
    public function getRetailSale()
    {
        return $this->retailSale;
    }

    /**
     * Set wholeSale
     *
     * @param RestBundle\Document\WholeSale $wholeSale
     * @return self
     */
    public function setWholeSale(\RestBundle\Document\WholeSale $wholeSale)
    {
        $this->wholeSale = $wholeSale;
        return $this;
    }

    /**
     * Get wholeSale
     *
     * @return RestBundle\Document\WholeSale $wholeSale
     */
    public function getWholeSale()
    {
        return $this->wholeSale;
    }
	
	/**
     * Set buyWithMeSale
     *
     * @param RestBundle\Document\BuyWithMeSale $buyWithMeSale
     * @return self
     */
    public function setBuyWithMeSale(\RestBundle\Document\BuyWithMeSale $buyWithMeSale)
    {
        $this->buyWithMeSale = $buyWithMeSale;
        return $this;
    }

    /**
     * Get buyWithMesale
     *
     * @return RestBundle\Document\BuyWithMeSale $buyWithMeSale
     */
    public function getBuyWithMeSale()
    {
        return $this->buyWithMeSale;
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
     * Set populatity
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
	
	/**
     * Set notation
     *
     * @param RestBundle\Document\Notation $notation
     * @return self
     */
    public function setNotation(\RestBundle\Document\Notation $notation)
    {
        $this->notation = $notation;
        return $this;
    }

    /**
     * Get notations
     *
     * @return RestBundle\Document\Notation $notation
     */
    public function getNotation()
    {
        return $this->notation;
    }
	
	/**
     * Add commentaire
     *
     * @param RestBundle\Document\Commentaire $commentaire
     */
    public function addCommentaire(\RestBundle\Document\Commentaire $commentaire)
    {
        $this->commentaires[] = $commentaire;
    }

    /**
     * Remove commentaire
     *
     * @param RestBundle\Document\Commentaire $commentaire
     */
    public function removeCommentaire(\RestBundle\Document\Commentaire $commentaire)
    {
        $this->commentaires->removeElement($commentaire);
    }

    /**
     * Get commentaires
     *
     * @return \Doctrine\Common\Collections\Collection $commentaires
     */
    public function getCommentaires()
    {
        return $this->$commentaires;
    }
}
