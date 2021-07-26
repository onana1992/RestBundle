<?php
namespace RestBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/** 
 * @MongoDB\Document
 */ 
class Image    
{
	/** @MongoDB\Id */
    private $id;

    /** @MongoDB\File */
    private $file;

    /** @MongoDB\String */
    private $filename;
	
	/** @MongoDB\Boolean */
    private $isUsed;

    /** @MongoDB\String */
    private $mimeType;

    /** @MongoDB\Date */
    private $uploadDate;

    /** @MongoDB\Int */
    private $length;

    /** @MongoDB\Int */
    private $chunkSize;

    /** @MongoDB\String */
    private $md5;

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
     * Set file
     *
     * @param file $file
     * @return self
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Get file
     *
     * @return file $file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get filename
     *
     * @return string $filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     * @return self
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string $mimeType
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set uploadDate
     *
     * @param date $uploadDate
     * @return self
     */
    public function setUploadDate($uploadDate)
    {
        $this->uploadDate = $uploadDate;
        return $this;
    }

    /**
     * Get uploadDate
     *
     * @return date $uploadDate
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    /**
     * Set length
     *
     * @param int $length
     * @return self
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Get length
     *
     * @return int $length
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set chunkSize
     *
     * @param int $chunkSize
     * @return self
     */
    public function setChunkSize($chunkSize)
    {
        $this->chunkSize = $chunkSize;
        return $this;
    }

    /**
     * Get chunkSize
     *
     * @return int $chunkSize
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * Set md5
     *
     * @param string $md5
     * @return self
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;
        return $this;
    }

    /**
     * Get md5
     *
     * @return string $md5
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * Set isUsed
     *
     * @param boolean $isUsed
     * @return self
     */
    public function setIsUsed($isUsed)
    {
        $this->isUsed = $isUsed;
        return $this;
    }

    /**
     * Get isUsed
     *
     * @return boolean $isUsed
     */
    public function getIsUsed()
    {
        return $this->isUsed;
    }
}
