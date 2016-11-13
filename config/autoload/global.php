<?php

return [
    'navigation' => [
        'default' => [
            [
                'label' => 'Home',
                'route' => 'home',
                'class' => 'nav-item',
            ],
        ],
        'guest' => [
            [
                'label' => 'Authentication',
                'route' => 'zfbuser/authentication',
                'class' => 'nav-item',
            ],
            [
                'label' => 'Registration',
                'route' => 'zfbuser/registration',
                'class' => 'nav-item',
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'navigation' => \Zend\Navigation\Service\DefaultNavigationFactory::class,
            'abstract_factories' => [
                \Zend\Navigation\Service\NavigationAbstractServiceFactory::class,
            ],

            'repository_factory' => function($sm){
                /** \Zend\ServiceManager\ServiceManager */
                return new \App\Repository\DefaultRepositoryFactory($sm);
            },

            'GoogleMaps' => function($sm) {
                /** @var \Zend\ServiceManager\ServiceManager $sm */
                $config = $sm->get('Config');
                $key = $config['google']['maps_api']['key'];
                $language = $config['google']['maps_api']['language'];
                $region = $config['google']['maps_api']['region'];
                $gm = new \App\GoogleMaps($key, $language, $region);
                return $gm;
            }
        ],
    ],
];
