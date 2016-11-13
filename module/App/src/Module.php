<?php

namespace App;

use App\View\Helper\MaterialForm as MaterialFormHelper;
use App\View\Helper\SaveResult as SaveResultHelper;
use App\View\Helper\UserAvatar as UserAvatarHelper;
use App\Factory\Repository\User as UserRepositoryFactory;
use Zend\Mvc\MvcEvent;
use App\View\Helper\Navigation as NavigationHelper;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\AbstractValidator;

class Module
{

    const VERSION = '0.0.0dev';

    public function onBootstrap(MvcEvent $e)
    {
        AbstractValidator::setDefaultTranslator($e->getApplication()->getServiceManager()->get('MvcTranslator'));

        $application = $e->getApplication();
        $sm          = $application->getServiceManager();

        $this->initNavigationHelpers($sm);
    }

    /**
     * @param ServiceLocatorInterface $sm
     */
    protected function initNavigationHelpers($sm)
    {
        /** @var \Zend\View\Helper\Navigation\PluginManager $pm */
        $pm = $sm->get('ViewHelperManager')->get('Navigation')->getPluginManager();
        $pm->setAlias('bootstrap', NavigationHelper\Bootstrap::class);
        $pm->setFactory(NavigationHelper\Bootstrap::class, InvokableFactory::class);
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return [
            'invokables' => [
                'materialForm' => MaterialFormHelper::class,
                'saveResult'   => SaveResultHelper::class,
                'userAvatar'   => UserAvatarHelper::class,
            ],
        ];

    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/../config/service.config.php';
    }

    public function getControllerConfig()
    {
        return include __DIR__ . '/../config/controller.config.php';
    }
}
