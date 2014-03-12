<?php
return array(
    'router' => array(
        'routes' => array(
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
                        'type'    => 'yimaAdminorRouter',
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
    ),// end of router
);
