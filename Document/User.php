<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;


/** 
 * @MongoDB\Document
 */ 
class User  implements UserInterface
{	
	/**
     * @MongoDB\Id
     */
    private $id;

	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $login;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $password;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $name;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $firstName;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $pseudo;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $userName;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $phone;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $sex;
	
	/**
	 * @MongoDB\Field(type="date") 
	 */
	private $birthDate;
	
	/**
	 * @MongoDB\Field(type="boolean") 
	 */
	private $isActivated;
	
	/**
	 * @MongoDB\Field(type="string") 
	 */
	private $activationNumber;
	
	/**
	 * @MongoDB\EmbedOne(targetDocument="AuthToken")
	 */
	private $token;
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="Favorite")
	 */
    private $favorites = array();

    /** 
     * @MongoDB\EmbedMany(targetDocument="EnchereEmbedded")
     */
    private $encheres = array();
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="PanierProduct")
	 */
    private $panierProducts = array();
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="PanierBWMProduct")
	 */
    private $panierBWMProducts = array();
	
	/** 
	 * @MongoDB\EmbedMany(targetDocument="PanierProductGros")
	 */
    private $panierProductGros = array();
	
	/**
	 * @MongoDB\EmbedOne(targetDocument="LivraisonAdress")
	 */
	private $livraisonAdress;
	
	
	
	private $plainPassword;
    /**
     * Set login
     *
     * @param string $login
     * @return self
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Get login
     *
     * @return string $login
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }
	
	public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
	
	public function getPlainPassword()
    {
        return $this->plainPassword;
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
	
	
	public function getRoles()
    {
        return [];
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->userName;
    }

    public function eraseCredentials()
    {
        // Suppression des donn??es sensibles
        $this->plainPassword = null;
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     * @return self
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string $pseudo
     */
    public function getPseudo()
    {
        return $this->pseudo;
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
     * Set firstName
     *
     * @param string $firstName
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string $firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    

   

    /**
     * Set token
     *
     * @param RestBundle\Document\AuthToken $token
     * @return self
     */
    public function setToken(\RestBundle\Document\AuthToken $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get token
     *
     * @return RestBundle\Document\AuthToken $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return self
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set sex
     *
     * @param string $sex
     * @return self
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
        return $this;
    }

    /**
     * Get sex
     *
     * @return string $sex
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Set birthDate
     *
     * @param date $birthDate
     * @return self
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return date $birthDate
     */
    public function getBirthDate()
    {
        return $this->birthDate;
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
     * Set activationNumber
     *
     * @param string $activationNumber
     * @return self
     */
    public function setActivationNumber($activationNumber)
    {
        $this->activationNumber = $activationNumber;
        return $this;
    }

    /**
     * Get activationNumber
     *
     * @return string $activationNumber
     */
    public function getActivationNumber()
    {
        return $this->activationNumber;
    }


    public function __construct()
    {
        $this->favorites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->encheres = new \Doctrine\Common\Collections\ArrayCollection();
    }

    
    /**
     * Add favorite
     *
     * @param RestBundle\Document\Favorite $favorite
     */
    public function addFavorite(\RestBundle\Document\Favorite $favorite)
    {
        $this->favorites[] = $favorite;
    }

    /**
     * Remove favorite
     *
     * @param RestBundle\Document\Favorite $favorite
     */
    public function removeFavorite(\RestBundle\Document\Favorite $favorite)
    {
        $this->favorites->removeElement($favorite);
    }

    /**
     * Get favorites
     *
     * @return \Doctrine\Common\Collections\Collection $favorites
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * Add enchere
     *
     * @param RestBundle\Document\EnchereEmbedded $enchere
     */
    public function addEnchere(\RestBundle\Document\EnchereEmbedded $enchere)
    {
        $this->encheres[] = $enchere;
    }


    /**
     * Remove enchere
     *
     * @param RestBundle\Document\EnchereEmbedded $enchere
     */
    public function removeEncheres(\RestBundle\Document\EnchereEmbedded $enchere)
    {
        $this->encheres->removeElement($enchere);
      
    }

    /**
     * Get encheres
     *
     * @return \Doctrine\Common\Collections\Collection $encheres
     */
    public function getEncheres()
    {
        return $this->encheres;
    }


    /**
     * Add panierProduct
     *
     * @param RestBundle\Document\PanierProduct $panierProduct
     */
    public function addPanierProduct(\RestBundle\Document\PanierProduct $panierProduct)
    {
        $this->panierProducts[] = $panierProduct;
    }


	
	/**
     * Add panierBWMProduct
     *
     * @param RestBundle\Document\PanierBWMProduct $panierBWMProduct
     */
    public function addPanierBWMProduct(\RestBundle\Document\PanierBWMProduct $panierBWMProduct)
    {
        $this->panierBWMProducts[] = $panierBWMProduct;
    }

    /**
     * Remove panierProduct
     *
     * @param RestBundle\Document\PanierProduct $panierProduct
     */
    public function removePanierProduct(\RestBundle\Document\PanierProduct $panierProduct)
    {
        $this->panierProducts->removeElement($panierProduct);
    }
	
	/**
     * Remove panierBWMProduct
     *
     * @param RestBundle\Document\PanierBWMProduct $panierBWMProduct
     */
    public function removePanierBWMProduct(\RestBundle\Document\PanierBWMProduct $panierBWMProduct)
    {
        $this->panierBWMProducts->removeElement($panierBWMProduct);
    }

    /**
     * Get panierProducts
     *
     * @return \Doctrine\Common\Collections\Collection $panierProducts
     */
    public function getPanierProducts()
    {
        return $this->panierProducts;
    }
	
	/**
     * Get panierBWMProducts
     *
     * @return \Doctrine\Common\Collections\Collection $panierBWMProducts
     */
    public function getPanierBWMProducts()
    {
        return $this->panierBWMProducts;
    }
	
	
	/**
     * Add panierProductGros
     *
     * @param RestBundle\Document\PanierProduct $panierProductGros
     */
    public function addPanierProductGros(\RestBundle\Document\PanierProductGros $panierProductGros)
    {
        $this->panierProductGros[] = $panierProductGros;
    }

    /**
     * Remove panierProduct
     *
     * @param RestBundle\Document\PanierProduct $panierProductGros
     */
    public function removePanierProductGros(\RestBundle\Document\PanierProductGros $panierProductGros)
    {
        $this->panierProductGros->removeElement($panierProductGros);
    }

    /**
     * Get panierProductGros
     *
     * @return \Doctrine\Common\Collections\Collection $panierProductGros
     */
    public function getPanierProductGros()
    {
        return $this->panierProductGros;
    }

    /**
     * Set livraisonAdress
     *
     * @param RestBundle\Document\LivraisonAdress $livraisonAdress
     * @return self
     */
    public function setLivraisonAdress(\RestBundle\Document\LivraisonAdress $livraisonAdress)
    {
        $this->livraisonAdress = $livraisonAdress;
        return $this;
    }

    /**
     * Get livraisonAdress
     *
     * @return RestBundle\Document\LivraisonAdress $livraisonAdress
     */
    public function getLivraisonAdress()
    {
        return $this->livraisonAdress;
    }
}
