<?php
return array(
    'admin' => array(
        array(
            'label' 	 => 'Adminor',
            'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME,
            'order' 	 => -100000,
            'pages' 	 => array(
                array(
                    'label' 	 => 'Dashboard',
                    'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME,
                    'order' 	 => -10000,
                ),
                array(
                    'label' 	 => 'Logout',
                    'route'		 => 'yima_adminor_auth/logout',
                    'order' 	 => -10000,
                ),
            ),
        ),
    ),
);
