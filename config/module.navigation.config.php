<?php
return [
    'admin' => [
        [
            'label' 	 => 'Adminor',
            'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME,
            'order' 	 => -100000,
            'pages' 	 => [
                [
                    'label' 	 => 'Dashboard',
                    'route'		 => \yimaAdminor\Module::ADMIN_ROUTE_NAME,
                    'order' 	 => -10000,
                ],
                [
                    'label' 	 => 'Logout',
                    'route'		 => 'yima_adminor_auth/logout',
                    'order' 	 => -10000,
                ],
            ],
        ],
    ],
];
