<?php
return array(
    'admin' => array(
        'Application' => array(
            'label' 	 => 'Application',
            'id'	 	 => 'admin_nav_application',
            'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME,
            'order' 	 => -10000,
            'pages' 	 => array(
                'dashboard'  => array(
                    'label' 	 => 'Adminor Dashboard',
                    'icon'		 => 'icon-flatscreen',
                    'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME,
                    'order' 	 => -10000,
                ),
                'website'  => array(
                    'label' 	 => 'Application Admin',
                    'module'	 => 'Application',
                    #'controller' => 'Index', // as default
                    #'action'	 =>	'dashboard', // as default
                    'params'	 => array('this' => 'that'),
                    'icon'		 => 'icon-flatscreen',
                ),
                'Home'  => array(
                    'label' 	 => 'Web Site Home',
                    'module'	 => 'Application',
                    'route'		 => 'home',
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
);
