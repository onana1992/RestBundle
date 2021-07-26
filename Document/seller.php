<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class Seller  
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
    protected $tel1;

    /**
     * @MongoDB\Field(type="string") 
     */
    protected $tel2;

    /**
     * @MongoDB\Field(type="string") 
     */
    protected $adresse;

    /**
     * @MongoDB\Field(type="string") 
     */
    protected $idImage;
    
    
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
     * Set tel1
     *
     * @param string tel1
     * @return self
     */
    public function setTel1($tel1)
    {
        $this->tel1 = $tel1;
        return $this;
    }

    /**
     * Get tel1
     *
     * @return string $tel1
     */
    public function getTel1()
    {
        return $this->tel1;
    }

    /**
     * Set tel2
     *
     * @param string tel2
     * @return self
     */
    public function setTel2($tel2)
    {
        $this->tel2 = $tel2;
        return $this;
    }

    /**
     * Get tel2
     *
     * @return string $tel2
     */
    public function getTel2()
    {
        return $this->tel2;
    }

    /**
     * Set adresse
     *
     * @param string adresse
     * @return self
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * Get adresse
     *
     * @return string $adresse
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

     /**
     * Set idImage
     *
     * @param string idImage
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
}
