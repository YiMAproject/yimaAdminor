<?php
return array(
	'admin' => array(
		'auto_set_controllers' => true,
		'template_folder' 	   => realpath(__DIR__.'/../template'),
		'template_name' 	   => 'amanda',
	),
		
	# this options here want to say Authentication strategy can be replacement
	'service_manager' => array(
		'invokables' => array(
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
				'route'		 => self::ADMIN_ROUTE_NAME,
				'order' 	 => -10000,
				'pages' 	 => array(
					'dashboard'  => array(
						'label' 	 => 'Dashboard',
						'icon'		 => 'icon-flatscreen',
						'route'		 => self::ADMIN_ROUTE_NAME,	
						'order' 	 => -10000,
					),
					'website'  => array(
						'label' 	 => 'Web Site View',
						'module'	 => 'cApplication',
						#'controller' => 'Index', // as default
						#'action'	 =>	'dashboard', // as default
						'params'	 => array('this'=>'that'),
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
						'route'		 => self::ADMIN_ROUTE_NAME.'/default',
					),
				),
			),
		),
	),
	
	# zamaani ke dar admin hastim template haa az
	# exp. template/*admin/*yimaadminor/index/dashboard.phtml khaande mishavad
	'controllers' => array(
		'invokables' => array(
			# also this is automaticaly added by AdminRouteListener as invokable 
			//'admin:yimaadminor\index' => 'yimaAdminor\Controller\Admin\IndexController'
		),
	),
		
    'router' => array(
        'routes' => array(
        	# *admin* route name
            self::ADMIN_ROUTE_NAME => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/'.self::ADMIN_ROUTE_SEGMENT,# /admin
                    'defaults' => array(
                    	'module' 	   => 'yimaAdminor', // also you can use zend approach __NAMESPACE__ as alternate
                        'controller'   => 'Index',
                        'action'       => 'dashboard',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                	# pas az in route baayad emkaan e ersaal parameter ham daashte baashim /order/a-z or /id/4
                    'default' => array(
                        'type'    => 'Crypto',
                        'options' => array(
                        	'route' => '/', #route prefix for determine this		
                            'defaults' 	 => array(
                            	'controller' => 'Index',
                            	'action'     => 'dashboard',
                            ),
                        ),
                    	'may_terminate' => true,
                    ),
                ),
            ),
        ),
    ),// end of router
);
