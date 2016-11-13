<?php

namespace App\Entity;

use Zend\Form\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ZfbUser\Entity\UserInterface;
use Zend\Hydrator\ExtractionInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_contacts")
 * @Annotation\Hydrator("Zend\Hydrator\ArraySerializable")
 */
class UserContact
{

    /**
     * @var int
     * @ORM\Id @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Exclude()
     */
    private $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="contact")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @Annotation\Exclude()
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="mobile", type="string", length=50, unique=true, nullable=false)
     * @Annotation\Type("Zend\Form\Element\Tel")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Required({"required":"true" })
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":50}})
     * @Annotation\Attributes({"type":"tel", "autocomplete":"off"})
     * @Annotation\Options({"label":"Mobile", "label_floating":false})
     */
    private $mobile;

    /**
     * @var string
     * @ORM\Column(name="skype", type="string", length=50, unique=true, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":50}})
     * @Annotation\Attributes({"type":"text", "autocomplete":"off"})
     * @Annotation\Options({"label":"Skype", "label_floating":false})
     */
    private $skype;

    /**
     * @var string
     * @ORM\Column(name="website", type="string", length=50, unique=true, nullable=true)
     * @Annotation\Type("Zend\Form\Element\Url")
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\AllowEmpty(true)
     * @Annotation\Validator({"name":"StringLength", "options":{"min":3, "max":50}})
     * @Annotation\Attributes({"type":"url", "autocomplete":"off"})
     * @Annotation\Options({"label":"Website", "label_floating":false, "helpText":"e.g. http://mysite.com"})
     */
    private $website;

    /**
     * @param array $data
     *
     * @return UserContact
     */
    public function exchangeArray(array $data)
    {
        $this->setMobile($data['mobile']);

        if (!empty($data['skype'])) {
            $this->setSkype($data['skype']);
        }

        if (!empty($data['website'])) {
            $this->setWebsite($data['website']);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            //'user_id' => $this->getUser()->getIdentity(),
            'mobile'  => $this->getMobile(),
            'skype'   => $this->getSkype(),
            'website' => $this->getWebsite(),
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
     * @return UserContact
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return UserContact
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     *
     * @return UserContact
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param string|null $skype
     *
     * @return UserContact
     */
    public function setSkype($skype = null)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string|null $website
     *
     * @return UserContact
     */
    public function setWebsite($website = null)
    {
        $this->website = $website;

        return $this;
    }

}
