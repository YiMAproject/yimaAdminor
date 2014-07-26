<?php
return array(
    'yima_adminor' => array(
        # default adminor template
        'default_theme' => 'adminstrap', /* @TODO: easily change by optional module setting */
        # invokable class as a service for route plugin manager
        'router' => 'yimaAdminor\Mvc\Router\Http\Crypto',
        # auto add invokable class into ControllerManager for admin controllers that not exists
        'auto_set_controllers' => true,
    ),

    // using specific theme for admin panel ... {
    'yima-theme' => array(
        'theme_locator' => array(
            'resolver_adapter_service' => array(
                # change template for admin panel
                'yimaAdminor\Mvc\AdminThemeResolver' => 10000, // high priority for admin
            ),
        ),

        'themes' => array(
            # default adminor template
            'adminstrap' => array(
                'dir_path' => __DIR__ .DS. '..' .DS. 'themes',
            ),
        ),
    ),
    # ... }

    # yima authorize module config
    'yima_authorize' => array(
        'permissions' => array(
            'factories' => array(
                'yima_adminor' => 'yimaAdminor\Auth\Permission\AclAuthenticationFactory'
            ),
        ),
    ),

    'controllers' => array(
        'invokables' => array(
            # also this is automaticaly added by AdminRouteListener as invokable
            'Admin\yimaAdminor\Index' => 'yimaAdminor\Controller\Admin\IndexController',

            'yimaAdminor\Controller\Account' => 'yimaAdminor\Controller\AccountController'
        ),
    ),

    'view_manager' => array(
        'mvc_strategies' => array(
            # If we are on admin set admin prefix to viewModel layout on render (registered AggregateListener Service)
            'yimaAdminor.MvcView.AdminTemplateListener',
        ),

        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

	'navigation' => include_once 'module.navigation.config.php',

    'router'     => include_once 'module.route.config.php',
);
