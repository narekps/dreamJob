<?php

namespace App\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Response;
use ZfbUser\Controller\Plugin\ZfbAuthentication;
use Zend\Form\Annotation\AnnotationBuilder;
use Interop\Container\ContainerInterface;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * @method ZfbAuthentication zfbAuthentication
 * @method Response|array prg(string $redirect = null, bool $redirectToUrl = false)
 * Class BaseController
 * @package App\Controller
 */
class BaseController extends AbstractActionController
{

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getEvent()->getApplication()->getServiceManager()->get('Doctrine\ORM\EntityManager');
    }

    /**
     * @return \Zend\Stdlib\RequestInterface|\Zend\Http\PhpEnvironment\Request
     */
    public function getRequest()
    {
        $request = parent::getRequest();

        return $request;
    }

    /**
     * @param      $entityName
     * @param      $prg
     * @param null $entity
     *
     * @return \Zend\Form\Form
     */
    public function buildEntityForm($entityName, array $prg = null, $entity = null)
    {
        $builder = new AnnotationBuilder($this->getEntityManager());
        $form    = $builder->createForm($entityName);
        $form->setAttribute('class', $form->getAttribute('class') . ' ' . 'form-horizontal');

        $form->add([
            'type'    => 'Zend\Form\Element\Csrf',
            'name'    => 'csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 600
                ]
            ]
        ]);

        $form->add([
            'type'       => 'Zend\Form\Element\Button',
            'name'       => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'class' => 'btn btn-primary',
            ],
            'options'    => [
                'label' => "Save",
            ],
        ]);

        if (!is_null($prg)) {
            $form->setData($prg);
        } elseif (is_object($entity)) {
            $form->bind($entity);
        }

        return $form;
    }
}
