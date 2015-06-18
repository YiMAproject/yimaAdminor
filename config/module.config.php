<?php
return [
    'yima_adminor' => [
        # default adminor template
        'default_theme' => 'adminstrap', /* @TODO: can change by setting module  */
        # auto add invokable class into ControllerManager for admin controllers that not exists
        'auto_set_controllers' => true,
    ],

    // using specific theme for admin panel ... {
    'yima-theme' => [
        'theme_locator' => [
            'resolver_adapter_service' => [
                # change template for admin panel
                'yimaAdminor\Mvc\OffCanvasAdminThemeResolver' => 10000, // high priority for admin
            ],
        ],

        'themes' => [
            # default adminor template
            'adminstrap' => [
                'dir_path' => __DIR__ .DS. '..' .DS. 'themes',
            ],
        ],
    ],
    # ... }

    # yima authorize module config
    'yima_authorize' => [
        'services' => [
            'factories' => [
                'yima_adminor' => 'yimaAdminor\Auth\Permission\AclAuthenticationFactory'
            ],
        ],
    ],

    'controllers' => [
        'invokables' => [
            # also this is automaticaly added by AdminRouteListener as invokable
            'Admin\yimaAdminor\Index' => 'yimaAdminor\Controller\Admin\IndexController',

            'yimaAdminor\Controller\Account' => 'yimaAdminor\Controller\AccountController'
        ],
    ],

    'view_manager' => [
        'mvc_strategies' => [
            # If we are on admin set admin prefix to viewModel layout on render (registered AggregateListener Service)
            'yimaAdminor.MvcView.AdminMvcRenderStrategies',
        ],

        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
