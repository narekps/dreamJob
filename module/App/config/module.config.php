<?php

namespace App;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use App\Controller\ProfileController;
use ZfbUser\Authentication\Service as AuthenticationService;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'profile' => [ // Работа с профилем пользователя
                'type'    => Segment::class,
                'options' => [
                    'route' => '/profile[/:action[/:section]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'section' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action' => 'index',
                        'section' => 'main',
                    ],
                ],
            ],
            'file' => [ // Работа с файлами
                'type'    => Segment::class,
                'options' => [
                    'route' => '/file[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\FileController::class,
                        'action' => 'index',
                        'id' => 0,
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
            'zfb-user' => __DIR__ . '/../view', // Переопределяем виды модуля ZfbUser
        ],
    ],

    'translator' => [
        'locale' => 'ru_RU',//en_US
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'doctrine' => [
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    \Gedmo\Tree\TreeListener::class,
                    \Gedmo\Timestampable\TimestampableListener::class,
                    \Gedmo\Sluggable\SluggableListener::class,
                    \Gedmo\Loggable\LoggableListener::class,
                    \Gedmo\Sortable\SortableListener::class
                ],
            ],
        ],
        'driver' => [
            'app_entity' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'paths' => [__DIR__ . '/../src/Entity'],
            ],
            'orm_default' => [
                'drivers' => [
                    'App\Entity' => 'app_entity'
                ]
            ]
        ]
    ]
];
