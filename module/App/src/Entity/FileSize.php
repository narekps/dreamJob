<?php

namespace App\Entity;

use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator\ExtractionInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="file_sizes")
 * @Annotation\Hydrator("Zend\Hydrator\ArraySerializable")
 */
class FileSize
{

    /**
     * @var int
     *
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * Имя файла
     *
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=false, nullable=false, options={"comment":"Имя файла"})
     * @Annotation\Exclude()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, unique=false, nullable=false)
     * @Annotation\Exclude()
     */
    private $alias;

    /**
     * @var File
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\File", inversedBy="sizes", cascade={"persist"})
     * @ORM\JoinColumn(name="fileId", referencedColumnName="id", onDelete="CASCADE")
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer", nullable=false)
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer", nullable=false)
     */
    private $height;

    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return FileSize
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return FileSize
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param mixed $alias
     *
     * @return FileSize
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     *
     * @return FileSize
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return FileSize
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     *
     * @return FileSize
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }
}
