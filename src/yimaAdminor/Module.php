<?php
namespace yimaAdminor;

use yimaAdminor\Mvc\AdminRouteListener;
use yimaAdminor\Mvc\AdminTemplateListener;
use Zend\Authentication;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package yimaAdminor
 */
class Module implements
    BootstrapListenerInterface,
    LocatorRegisteredInterface
{
	const ADMIN_ROUTE_NAME	  = 'admin';
	const ADMIN_ROUTE_SEGMENT = 'admin';
	
	protected $isOnAdmin = false;

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        /** @var $e MvcEvent */
        $routePluginManager = $e->getRouter()->getRoutePluginManager();
    	if (! $routePluginManager->has('crypto') ) {
    		$routePluginManager->setInvokableClass('crypto','yimaAdminor\Mvc\Router\Http\Crypto');
    	}
    	
    	// .........................................................................
        $events	= $e->getApplication()->getEventManager();
        
        // determine if we are on admin set specific admin controller to router
        $events->attach( new AdminRouteListener() );
        
        // determine if we are on admin add pathStack to yimaAdminor template folder and
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
    			'admin\navigation' => 'yimaAdminor\Navigation\Service\NavigationFactory',
    			#
    			'yimaAdminor\Authentication\Service' => function ($sm) {
    				return new Authentication\AuthenticationService(
    					$sm->get('yimaAdminor\Authentication\Storage'),
    					$sm->get('yimaAdminor\Authentication\Adapter')
    				);
    			},
    		), 		
    	);
    }
    
    public function getConfig()
    {
         $config = include __DIR__ . '/../../config/module.config.php';
        
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
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
}
