<?php

return [
    \ZfbRbac\Module::CONFIG_KEY => [
        'identity_provider' => \ZfbRbac\Identity\AuthenticationIdentityProvider::class,
        'guest_role' => 'guest',
        'protection_policy' => \ZfbRbac\Guard\GuardInterface::POLICY_DENY,
        'role_provider' => [
            \ZfbRbac\Role\InMemoryRoleProvider::class => [
                'admin' => [
                    'children'    => ['user'],
                ],
                'user' => [
                    'children'    => ['guest'],
                ]
            ]
        ],
        'guards' => [
            'ZfbRbac\Guard\ControllerGuard' => [
                [
                    'controller' => \DoctrineORMModule\Yuml\YumlController::class,
                    'actions'    => ['index'],
                    'roles'      => ['guest']
                ],
                [
                    'controller' => \ZfbUser\Controller\IndexController::class,
                    'actions'    => ['logout'],
                    'roles'      => ['user']
                ],
                [
                    'controller' => \ZfbUser\Controller\IndexController::class,
                    'actions'    => ['index', 'authentication', 'authenticate', 'registration'],
                    'roles'      => ['guest']
                ],
                // my app
                [
                    'controller' => \App\Controller\IndexController::class,
                    'actions'    => ['index'],
                    'roles'      => ['guest']
                ],
                [
                    'controller' => \App\Controller\ProfileController::class,
                    'actions'    => ['index', 'edit'],
                    'roles'      => ['user']
                ],
                [
                    'controller' => \App\Controller\FileController::class,
                    'actions'    => ['show'],
                    'roles'      => ['guest']
                ],
            ]
        ],
        'unauthorized_strategy' => [
            'template' => 'error/403'
        ],
        'redirect_strategy' => [
            // 'redirect_when_connected' => true,
            // 'redirect_to_route_connected' => 'home',
            // 'redirect_to_route_disconnected' => 'login',
            // 'append_previous_uri' => true,
            // 'previous_uri_query_key' => 'redirectTo'
        ],
        // 'guard_manager'               => [],
        // 'role_provider_manager'       => []
    ]
];
