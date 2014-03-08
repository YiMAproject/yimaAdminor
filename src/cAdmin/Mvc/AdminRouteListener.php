<?php
namespace cAdmin\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router;
use Zend\Mvc\ModuleRouteListener;

class AdminRouteListener implements ListenerAggregateInterface
{
    const MODULE_NAMESPACE    = 'module';
    
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkIsOnAdmin'), -10);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'makeDefaultRouteController'), -100000);
    }
    
    public function checkIsOnAdmin(MvcEvent $e)
    {
    	$matches = $e->getRouteMatch();
    	if (!$matches instanceof Router\RouteMatch) {
    		// Can't do anything without a route match
    		return;
    	}
    	
    	$matchedRoute = $matches->getMatchedRouteName();
    	if (strstr($matchedRoute,'/')) {
    		$matchedRoute = substr($matchedRoute,0,strpos($matchedRoute,'/'));
    	}
    	
    	// we are not in admin route
    	if ($matchedRoute !== \cAdmin\Module::ADMIN_ROUTE_NAME) {
    		return;
    	}
    	
    	/*
    	 * Set cAdmin\Module::isOnAdmin to true
    	*/
    	$serviceLocator = $e->getApplication()->getServiceManager();
    	$serviceLocator->get('cAdminModule')->setOnAdmin();
    }

    /**
     * Agar dar route e admin baashim:
     *    **dar soorati ke route haavie parameter e 'module' baashad**,
     *    controller raa bar asaase aan misaazad. exp. admin:modulename\controller
     *    agar config['admin']['auto_set_controllers'] mojood bood 'admin:modulename\controller'
     *    raa be onvaane inokables controllerLoader misaazad :
     *    $module .'\\'. 'Controller' .'\\'. 'Admin' .'\\'. $controller.'Controller'
     * 
     * @param MvcEvent $e
     */
    public function makeDefaultRouteController(MvcEvent $e)
    {
    	$serviceLocator = $e->getApplication()->getServiceManager();
    	if (! $serviceLocator->get('cAdminModule')->isOnAdmin() ) {
    		return;
    	}
    	
    	$matches = $e->getRouteMatch();
        $matchedRoute = $matches->getMatchedRouteName();
        if (strstr($matchedRoute,'/') && $matchedRoute != \cAdmin\Module::ADMIN_ROUTE_NAME.'/default'){
        	// we don't do anything on none default route scheme
        	return;
        }
        
        // get module\controller from route
        $module 	= $matches->getParam('module',$matches->getParam(ModuleRouteListener::MODULE_NAMESPACE));
        $orginController = $matches->getParam('controller');
        
        // Keep the originally matched controller name around
        // masalan dar MvcPage::isActive() estefaade darad, in hamaan controller i ast ke dar route ta'rif shode
        $matches->setParam(ModuleRouteListener::ORIGINAL_CONTROLLER, $orginController);
        
        $controller 	= \cAdmin\Module::ADMIN_ROUTE_SEGMENT . ':' . $module . '\\' . $orginController;
        $controller 	= strtolower($controller);
        
        /* get serviceLocator and retrieve ControllerLoader if $controller registered return it
         * if not return absolute controller namespace path
         */
        $application = $e->getApplication();
        $config      = $application->getServiceManager()->get('Config');
        
        if (isset($config['admin']) && is_array($config['admin'])) {
        	if (isset($config['admin']['auto_set_controllers']) && $config['admin']['auto_set_controllers'] ) 
        	{
        		$controllerLoader = $application->getServiceManager()->get('ControllerLoader');
        		
        		if (false === $controllerLoader->has($controller)) {
        			// Prepend the controllername with the module, and replace it in the
        			// matches
        			$controllerPath	= $module .'\\'. 'Controller' .'\\'. 'Admin' .'\\'. $orginController.'Controller';
        			
        			// register controller if not exists
        			if (class_exists($controllerPath,true)) {
        				$controllerLoader->setInvokableClass($controller,$controllerPath);
        			}
        		}
        	}
        }
        
        $matches->setParam('controller', $controller);
    }
    
    
    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
    	foreach ($this->listeners as $index => $listener) {
    		if ($events->detach($listener)) {
    			unset($this->listeners[$index]);
    		}
    	}
    }
    
}
