<?php

namespace App\Entity;

use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Zend\Hydrator\ExtractionInterface;

/**
 * @ORM\Entity(repositoryClass="App\Factory\Repository\File")
 * @ORM\Table(name="files")
 * @Annotation\Hydrator("Zend\Hydrator\ArraySerializable")
 */
class File
{

    /**
     * @var int
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * Оригинальное имя файла
     *
     * @var string
     * @ORM\Column(name="name", type="string", length=255, unique=false, nullable=false, options={"comment":"Оригинальное имя файла"})
     * @Annotation\Exclude()
     */
    private $name;

    /**
     * Путь до папки с файлом
     *
     * @var string
     * @ORM\Column(name="folder", type="string", length=255, unique=false, nullable=false, options={"comment":"Путь до папки с файлом"})
     * @Annotation\Exclude()
     */
    private $folder;

    /**
     * Дата и время загрузки файла
     *
     * @var \DateTime
     * @ORM\Column(name="uploaded_date", type="datetime", nullable=false, options={"comment":"Дата и время загрузки файла"})
     * @Annotation\Exclude()
     */
    private $uploadedDate;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\FileSize", mappedBy="file")
     */
    protected $sizes;

    public function __construct()
    {
        $this->uploadedDate = new \DateTime();
        $this->sizes        = new ArrayCollection();
    }

    /**
     * @param array $data
     *
     * @return File
     */
    public function exchangeArray(array $data)
    {
        $this->setName($data['name']);
        $this->setFolder($data['folder']);

        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'id'     => $this->getId(),
            'name'   => $this->getName(),
            'folder' => $this->getFolder(),
        ];
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
     * @return File
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
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param string $folder
     *
     * @return File
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUploadedDate()
    {
        return $this->uploadedDate;
    }

    /**
     * @param \DateTime $uploadedDate
     *
     * @return File
     */
    public function setUploadedDate($uploadedDate)
    {
        $this->uploadedDate = $uploadedDate;

        return $this;
    }

    /**
     * @return array
     */
    public function getSizes()
    {
        return $this->sizes->toArray();
    }

    /**
     * @param array $sizes
     *
     * @return File
     */
    public function setSizes(array $sizes)
    {
        $this->sizes->clear();
        foreach ($sizes as $size) {
            $this->addSize($size);
        }

        return $this;
    }

    /**
     * @param FileSize $fileSize
     *
     * @return File
     */
    public function addSize($fileSize)
    {
        if (!$this->sizes->contains($fileSize)) {
            $this->sizes->add($fileSize);
        }

        return $this;
    }

}
