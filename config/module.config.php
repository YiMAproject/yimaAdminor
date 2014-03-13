<?php
return array(
    'yima_adminor' => array(
        # invokable class as a service for route plugin manager
        'router' => 'yimaAdminor\Mvc\Router\Http\Crypto',
        # auto add invokable class into ControllerManager for admin controllers that not exists
        'auto_set_controllers' => true,
    ),

    'controllers' => array(
        'invokables' => array(
            # also this is automaticaly added by AdminRouteListener as invokable
            'Admin\yimaAdminor\Index' => 'yimaAdminor\Controller\Admin\IndexController'
        ),
    ),

    'view_manager' => array(
        'mvc_strategies' => array(
            # If we are on admin set admin prefix to viewModel layout on render
            'yimaAdminor.MvcView.AdminTemplateListener',
        ),

        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'yima_widgetator' => array(
        'invokables' => array(
            'uitools.navigation' => 'yimaAdminor\Widget\NavigationMenu\Widget',
        ),
    ),

    # this options here want to say Authentication strategy can be replacement
	'service_manager' => array(
		'invokables' => array(
            # If we are on admin set admin prefix to viewModel layout on render
            'yimaAdminor.MvcView.AdminTemplateListener' => 'yimaAdminor\Mvc\AdminTemplateListener',

			'yimaAdminor\Authentication\Storage' => 'Zend\Authentication\Storage\NonPersistent',
		),
		'factories' => array(
			'yimaAdminor\Authentication\Adapter' => function ($sm)
			{
				$digestResolver = new \Zend\Authentication\Adapter\Http\FileResolver(__DIR__.DS.'htpsswds');
				#$basicResolver  = new \Zend\Authentication\Adapter\Http\FileResolver(__DIR__.DS.'htpsswds');
				
				$config = array(
					'accept_schemes' => 'digest',
					'realm'          => 'Admin Panel',
					// TODO: benzazr miresad ke baseurl baayad injaa ezaafe shavad
					'digest_domains' => '/'.yimaAdminor\Module::ADMIN_ROUTE_SEGMENT,// behtar ast tavasote url(admin) saakhte shavad
					'nonce_timeout'  => 3600,
				);
				$authAdapter = new \Zend\Authentication\Adapter\Http($config);
				
				$authAdapter->setDigestResolver($digestResolver);
				#$authAdapter->setBasicResolver($basicResolver);
				$authAdapter->setRequest($sm->get('request'));
				$authAdapter->setResponse($sm->get('response'));
				
				return $authAdapter;
			},
		),
	),
	
	'navigation' => array(
		#admin navigation
		'admin' => array(
			'Application' => array(
				'label' 	 => 'Application',
				'id'	 	 => 'admin_nav_application',
				'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME,
				'order' 	 => -10000,
				'pages' 	 => array(
					'dashboard'  => array(
						'label' 	 => 'Dashboard',
						'icon'		 => 'icon-flatscreen',
						'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME,
						'order' 	 => -10000,
					),
					'website'  => array(
						'label' 	 => 'Web Site View',
						'module'	 => 'Application',
						#'controller' => 'Index', // as default
						#'action'	 =>	'dashboard', // as default
						'params'	 => array('this' => 'that'),
						'icon'		 => 'icon-flatscreen',	
					),
					'rayaOnline' => array(
						'label'  => 'Raya Online',
						'id' 	 => 'raya-online',
						'uri' 	 => 'http://www.raya-media.com/cms',
						'target' => '_blank',
						'icon'	 => 'icon-speech',
						'order'  => 10000,
					),
				),
			),
			'Modules' => array(
				'label'  => 'Modules',
				'uri'	 => '#',
				'id'	 => 'admin_nav_modules',
				'pages'  => array(
				),
			),
		),
	),
	
	/* It's not neccessery to change options below except of good reason to change. */
	'authorize' => array(
		'protected' => array(
			'admin' => array( // admin is authorization name, can access later by serviceLocator exp. authorize\admin
				'mode'     => 'route', # this mode looking for matched route name
				'options'  => array('route' => 'admin/*'), 
				'service'  => 'yimaAdminor\Authentication\Service',
				'isValid'  => null, 
				'notValid' => array ('module'=>'yimaAdminor', 'controller'=>'Account', 'action'=>'forbidden' ),
				'account-navigation' => array(
					'logout'  => array(
						'label' 	 => 'Logout',
						'module'	 => 'yimaAdminor',
						'controller' => 'Account',
						'action'	 =>	'logout',
						# this way you can use navigation outside the admin route, exp. on frontend
						'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME.'/default',
					),
				),
			),
		),
	),
);
