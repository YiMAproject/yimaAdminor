<?php
return [
    'routes' => [
        # Authentication
        'yima_adminor_auth' => [
            'type'    => 'Literal',
            'options' => [
                'route'    => '/adminor',
                'defaults' => [
                    'controller'   => 'yimaAdminor\Controller\Account',
                    'action'       => 'login',
                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'logout' => [
                    'type'    => 'Literal',
                    'options' => [
                        'route' => '/logout',
                        'defaults' 	 => [
                            'action'     => 'logout',
                        ],
                    ],
                    'may_terminate' => true,
                ],
            ],
        ],
        # Admin Area Routes
        \yimaAdminor\Module::ADMIN_ROUTE_NAME => [
            'type'    => 'Literal',
            'options' => [
                # /admin
                'route'    => '/'.\yimaAdminor\Module::ADMIN_ROUTE_SEGMENT,
                'defaults' => [
                    'module' 	   => 'yimaAdminor', // also you can use zend approach __NAMESPACE__ as alternate
                    'controller'   => 'Index',
                    'action'       => 'dashboard',
                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'default' => [
                    'type'    => 'yimaAdminor\Mvc\Router\Http\Crypto', // use class exists
                    'options' => [
                        # we can use any word after /admin[/word]/ to browse child routes
                        'route' => '/', //'/browse/',
                        'defaults' 	 => [
                            'controller' => 'Index',
                            'action'     => 'dashboard',
                        ],
                        # we can pass any options to Router
                        // 'option' => 'value',

                        # encrypt and decrypt class name or object() to de/encode queries for Crypto router
                        # 'cryption' => '\yimaAdminor\Mvc\Router\Http\Crypto\CryptionBase64', // by default
                        # 'cryption' => '\yimaAdminor\Mvc\Router\Http\Crypto\CryptionDebuging', // Debug Mode(None Encoded)
                    ],
                    'may_terminate' => true,
                ],
            ],
        ],
    ],
];
