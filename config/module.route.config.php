<?php
return array(
    'routes' => array(
        # Authentication
        'yima_adminor_auth' => array(
            'type'    => 'Literal',
            'options' => array(
                'route'    => '/adminor',
                'defaults' => array(
                    'controller'   => 'yimaAdminor\Controller\Account',
                    'action'       => 'login',
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'logout' => array(
                    'type'    => 'Literal',
                    'options' => array(
                        'route' => '/logout',
                        'defaults' 	 => array(
                            'action'     => 'logout',
                        ),
                    ),
                    'may_terminate' => true,
                ),
            ),
        ),
        # Admin Area Routes
        \yimaAdminor\Module::ADMIN_ROUTE_NAME => array(
            'type'    => 'Literal',
            'options' => array(
                # /admin
                'route'    => '/'.\yimaAdminor\Module::ADMIN_ROUTE_SEGMENT,
                'defaults' => array(
                    'module' 	   => 'yimaAdminor', // also you can use zend approach __NAMESPACE__ as alternate
                    'controller'   => 'Index',
                    'action'       => 'dashboard',
                ),
            ),
            'may_terminate' => true,
            'child_routes' => array(
                'default' => array(
                    'type'    => 'yimaAdminor\Mvc\Router\Http\Crypto', // use class exists
                    'options' => array(
                        # we can use any word after /admin[/word]/ to browse child routes
                        'route' => '/', //'/browse/',
                        'defaults' 	 => array(
                            'controller' => 'Index',
                            'action'     => 'dashboard',
                        ),
                        # we can pass any options to Router
                        // 'option' => 'value',

                        # encrypt and decrypt class name or object() to de/encode queries for Crypto router
                        # 'cryption' => '\yimaAdminor\Mvc\Router\Http\Crypto\CryptionBase64', // by default
                        # 'cryption' => '\yimaAdminor\Mvc\Router\Http\Crypto\CryptionDebuging', // Debug Mode(None Encoded)
                    ),
                    'may_terminate' => true,
                ),
            ),
        ),
    ),
);
