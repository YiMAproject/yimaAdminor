<?php
return array(
    'yima_adminor' => array(
        # invokable class as a service for route plugin manager
        'router' => 'yimaAdminor\Mvc\Router\Http\Crypto',
        # auto add invokable class into ControllerManager for admin controllers that not exists
        'auto_set_controllers' => true,
    ),

    # yima authorize module config
    'yima_authorize' => array(
        'permissions' => array(
            'yima_adminor' => 'yimaAdminor.Permission.Acl'
        ),
    ),

    'service_manager' => array(
        'factories' => array(
            'yimaAdminor.Permission.Acl' => 'yimaAdminor\Auth\Permission\PermissionAclFactory'
        ),
    ),


    'controllers' => array(
        'invokables' => array(
            # also this is automaticaly added by AdminRouteListener as invokable
            'Admin\yimaAdminor\Index' => 'yimaAdminor\Controller\Admin\IndexController'
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

	'navigation' => include_once 'module.navigation.config.php'
);
