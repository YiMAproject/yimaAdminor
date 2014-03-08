<?php
namespace cAdmin;

use cAdmin\Mvc\AdminRouteListener;
use cAdmin\Mvc\AdminTemplateListener;
use Zend\Authentication;

use Zend\ModuleManager\Feature\LocatorRegisteredInterface;

class Module implements LocatorRegisteredInterface
{
	const ADMIN_ROUTE_NAME	  = 'admin';
	const ADMIN_ROUTE_SEGMENT = 'admin';
	
	protected $isOnAdmin = false;
		
    public function onBootstrap($e)
    {
    	// add routerPlugin to RoutePluginManager
    	$routePluginManager = $e->getRouter()->getRoutePluginManager();
    	if (! $routePluginManager->has('crypto') ) {
    		$routePluginManager->setInvokableClass('crypto','cAdmin\Mvc\Router\Http\Crypto');
    	}
    	
    	// .........................................................................
        $events	= $e->getApplication()->getEventManager();
        
        // determine if we are on admin set specific admin controller to router
        $events->attach( new AdminRouteListener() );
        
        // determine if we are on admin add pathStack to cAdmin template folder and 
        // set admin prefix to viewModel layout on render
        $events->attach( new AdminTemplateListener() );
    }
    
    
    /**
     * When we are on admin after route (admin) detected this is set to true
     * elsewhere we can find that admin route detected and we are on it.
     * 
     */
    public function setOnAdmin($bool = true)
    {
    	$this->isOnAdmin = (boolean) $bool;
    	
    	return $this;
    }

    public function isOnAdmin()
    {
    	return $this->isOnAdmin;
    }
    
    // ................................................................................................
    
    public function getServiceConfig()
    {
    	return array(
    		'factories' => array (
    			'admin\navigation' => 'cAdmin\Navigation\Service\NavigationFactory',
    			#
    			'cAdmin\Authentication\Service' => function ($sm) {
    				return new Authentication\AuthenticationService(
    					$sm->get('cAdmin\Authentication\Storage'),
    					$sm->get('cAdmin\Authentication\Adapter')
    				);
    			},
    		), 		
    	);
    }
    
    public function getConfig()
    {
         $config = include __DIR__ . '/config/module.config.php';
        
         // set asset manager config, related to template name and folder
         if (isset($config['admin']) && is_array($config['admin'])) {
         	if ( isset($config['admin']['template_folder']) && !empty($config['admin']['template_folder']) ) {
         		$path = $config['admin']['template_folder'].
         				(
         					(isset($config['admin']['template_name']) && !empty($config['admin']['template_name']))
         						? DS.$config['admin']['template_name'] : ''
         				)
         				.DS.'www';
         	}
         }
         
        $config = array_merge_recursive($config,
        	array(
        		'asset_manager' => array(
        			'resolver_configs' => array(
        				'paths' => array(
        					$path,
        				),
        			),
        		)
        	)		
        );
        
        return $config;
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
