<?php

namespace App\Entity;

use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ZfbRbac\Identity\IdentityInterface;
use ZfbUser\Entity\UserInterface;
use Zend\Hydrator\ExtractionInterface;

/**
 * @ORM\Entity(repositoryClass="App\Factory\Repository\User")
 * @ORM\Table(name="users")
 * @Annotation\Hydrator("Zend\Hydrator\ArraySerializable")
 */
class User implements UserInterface, IdentityInterface
{

    const GENDER_EMPTY  = 0;
    const GENDER_MALE   = 1; // Мужской
    const GENDER_FEMALE = 2; // Женский

    const AVATAR_SIZES
        = [
            'large' => ['w' => 230, 'h' => 230],
            'big'   => ['w' => 120, 'h' => 120],
            'small' => ['w' => 60, 'h' => 60],
        ];

    /**
     * @var int
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * Имя пользователя
     *
     * @var string
     * @ORM\Column(name="name", type="string", length=30, unique=false, nullable=false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Validator({"name":"StringLength", "options":{"min":2, "max":30}})
     * @Annotation\Attributes({"placeholder":"First name"})
     * @Annotation\Options({"label":"First name", "label_floating":false, "helpText":"Enter your real name"})
     */
    private $name;

    /**
     * Фамилия
     *
     * @var string
     * @ORM\Column(name="surname", type="string", length=30, unique=false, nullable=false)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Validator({"name":"StringLength", "options":{"min":2, "max":30}})
     * @Annotation\Attributes({"placeholder":"Last name"})
     * @Annotation\Options({"label":"Last name", "label_floating":false, "helpText":"Enter your real name"})
     */
    private $surname = '';

    /**
     * Пол
     *
     * @var int
     * @ORM\Column(name="gender", type="integer", nullable=false, options={"default"=0})
     * @Annotation\Type("Zend\Form\Element\Radio")
     * @Annotation\Options({"label":"Sex", "label_floating":false, "value_options"={"1"="Male", "2"="Female"}})
     */
    private $gender = self::GENDER_EMPTY;

    /**
     * Дата рождения
     *
     * @var \DateTime
     * @ORM\Column(name="birthday", type="date", nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"type":"datetime", "class":"datepicker"})
     * @Annotation\Validator({"name":"Date", "options":{"format":"d.m.Y"}})
     * @Annotation\Options({"label":"Birthday", "label_floating":false})
     */
    protected $birthday;

    /**
     * Адрес пользователя (Google maps Place ID)
     *
     * @var string
     * @ORM\Column(name="address", type="string", nullable=true)
     * @Annotation\Type("Zend\Form\Element\Hidden")
     */
    private $address;

    /**
     * Поле для ввода адреса
     *
     * @var string
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Attributes({"type":"text", "autocomplete":"off", "onfocus": "geolocate();", "data-target":"address"})
     * @Annotation\Options({"label":"Address", "label_floating":false,
     *   "after_button":{"class":"btn btn-primary btn-xs address-clear", "text": "Clear", "data-target":"address"}
     * })
     */
    private $addressRaw;

    /**
     * E-mail - Идентификатор
     *
     * @var string
     * @ORM\Column(name="email", type="string", unique=true, length=50)
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":50}})
     * @Annotation\Attributes({"type":"email", "name":"identity", "autocomplete":"off"})
     * @Annotation\Options({"label":"E-mail", "label_floating":false})
     *
     * @Annotation\Exclude()
     */
    private $identity;

    /**
     * Пароль
     *
     * @var string
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     * @Annotation\Exclude()
     */
    private $password;

    /**
     * Подтвержден E-mail?
     *
     * @var bool
     * @ORM\Column(name="email_confirm", type="boolean", nullable=false, options={"default"=0})
     * @Annotation\Exclude()
     */
    private $emailConfirm = 0;

    /**
     * @var File
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="avatar", referencedColumnName="id")
     * @Annotation\Exclude()
     */
    private $avatar;

    /**
     * Контактная информация пользователя
     *
     * @var UserContact
     * @ORM\OneToOne(targetEntity="App\Entity\UserContact", mappedBy="user")
     * @Annotation\Exclude()
     */
    private $contact;

    /**
     * User constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @return UserInterface
     */
    public function exchangeArray(array $data) : UserInterface
    {
        $this->setName($data['name']);
        $this->setSurname($data['surname']);
        if (isset($data['birthday'])) {
            $this->setBirthday(\DateTime::createFromFormat('d.m.Y', $data['birthday']));
        }
        $this->setGender($data['gender']);
        $this->setAddress($data['address']);
        //$this->setIdentity($data['identity']);
        if (isset($data['avatar'])) {
            $this->setAvatar($data['avatar']);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy() : array
    {
        return [
            'name'     => $this->getName(),
            'surname'  => $this->getSurname(),
            'gender'   => $this->getGender(),
            'birthday' => $this->getBirthday() ? $this->getBirthday()->format('d.m.Y') : null,
            'address'  => $this->getAddress(),
            'identity' => $this->getIdentity(),
            'avatar'   => $this->getAvatar(),
        ];
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return UserInterface
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param string $identity
     *
     * @return UserInterface
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    /**
     * @return string
     */
    public function getCredential()
    {
        return $this->password;
    }

    /**
     * @param string $credential
     *
     * @return UserInterface
     */
    public function setCredential($credential)
    {
        $this->password = $credential;

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
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает отображаемое имя пользователя
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->getName() . ' ' . $this->getSurname();
    }

    /**
     * @return bool
     */
    public function getIdentityConfirmed()
    {
        return $this->emailConfirm;
    }

    /**
     * @param bool $confirmed
     *
     * @return UserInterface
     */
    public function setIdentityConfirmed($confirmed)
    {
        $this->emailConfirm = (bool)$confirmed;

        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = (int)$gender;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime|null $birthday
     *
     * @return User
     */
    public function setBirthday($birthday = null)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEmailConfirm()
    {
        return $this->emailConfirm;
    }

    /**
     * @param boolean $emailConfirm
     *
     * @return User
     */
    public function setEmailConfirm($emailConfirm)
    {
        $this->emailConfirm = $emailConfirm;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param File $avatar
     *
     * @return User
     */
    public function setAvatar($avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return UserContact
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param UserContact $contact
     *
     * @return User
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    public function getRoles()
    {
        return ['user'];
    }
}
