<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
  * @MongoDB\EmbeddedDocument 
  */ 
class Notation   
{

	/**
     * @MongoDB\Field(type="int")
     */
    protected $level1;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $level2;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $level3;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $level4;
	
	/**
     * @MongoDB\Field(type="int")
     */
    protected $level5;
	
	/**
     * Set level1
     *
     * @param int $level1
     * @return self
     */
    public function setLevel1($level1)
    {
        $this->level1 = $level1;
        return $this;
    }

    /**
     * Get $level1
     *
     * @return int $level1
     */
    public function getLevel1()
    {
        return $this->level1;
    }
	
	/**
     * Set level2
     *
     * @param int $level2
     * @return self
     */
    public function setLevel2($level2)
    {
        $this->level2 = $level2;
        return $this;
    }

    /**
     * Get $level2
     *
     * @return int $level2
     */
    public function getLevel2()
    {
        return $this->level2;
    }
	
	/**
     * Set level3
     *
     * @param int $level3
     * @return self
     */
    public function setLevel3($level3)
    {
        $this->level3 = $level3;
        return $this;
    }

    /**
     * Get $level3
     *
     * @return int $level3
     */
    public function getLevel3()
    {
        return $this->level3;
    }
	
	/**
     * Set level4
     *
     * @param int $level4
     * @return self
     */
    public function setLevel4($level4)
    {
        $this->level4 = $level4;
        return $this;
    }

    /**
     * Get $level4
     *
     * @return int $level4
     */
    public function getLevel4()
    {
        return $this->level4;
    }
	
	/**
     * Set level5
     *
     * @param int $level5
     * @return self
     */
    public function setLevel5($level5)
    {
        $this->level5 = $level5;
        return $this;
    }

    /**
     * Get $level5
     *
     * @return int $level5
     */
    public function getLevel5()
    {
        return $this->level5;
    }


}
