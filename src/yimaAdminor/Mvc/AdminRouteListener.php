<?php
namespace yimaAdminor\Mvc;

use yimaAdminor\Service\Parental;
use yimaAdminor\Service\Share;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router;
use Zend\Mvc\ModuleRouteListener;

/**
 * Class AdminRouteListener
 *
 * @package yimaAdminor\Mvc
 */
class AdminRouteListener extends Parental
    implements ListenerAggregateInterface
{
    const MODULE_NAMESPACE = 'module';
    
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
        // is onAdmin must first after routeMatch detection
    	$this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'checkIsOnAdmin'), -1);

        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'makeDefaultRouteController'), -100000);
    }

    /**
     * Check is Matched Route from Admin and Set
     * - Service\Share flag
     *
     * @param MvcEvent $e
     * @return bool
     * @throws \Exception
     */
    public function checkIsOnAdmin(MvcEvent $e)
    {
        /** @var $matches \Zend\Mvc\Router\Http\RouteMatch */
        $matches = $e->getRouteMatch();
    	if (!$matches instanceof Router\RouteMatch){
            throw new \Exception('No Route Matched Found, Make sure for the event priority!');
    	}
    	
    	$matchedRouteName = $matches->getMatchedRouteName();
        if (($slashOcurr = strpos($matchedRouteName, '/')) !== false) {
            // we are on one of child roots of admin
            $matchedRouteName = substr($matchedRouteName, 0, $slashOcurr);
        }

    	if ($matchedRouteName !== \yimaAdminor\Module::ADMIN_ROUTE_NAME) {
            // we are not in admin route
    		return false;
    	}

        // set Share Services flag
        self::$isOnAdmin = true;

        return true;
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
    	if (!Share::isOnAdmin()) {
    		return false;
    	}

    	$matches = $e->getRouteMatch();

        // get module\controller from route
        $module 	= $matches->getParam('module');
        $controller = $matches->getParam('controller');

        // Keep the originally matched controller name around
        // masalan dar MvcPage::isActive() estefaade darad, in hamaan controller i ast ke dar route ta'rif shode
        $matches->setParam(ModuleRouteListener::ORIGINAL_CONTROLLER, $controller);

        $controllerService = ucfirst(strtolower(\yimaAdminor\Module::ADMIN_ROUTE_NAME)).'\\'.$module.'\\'.$controller;
//        $controller = strtolower($controller);

        /* get serviceLocator and retrieve $controller from ControllerLoader if registered
         * elsewhere return absolute controller namespace path
         */
        $application = $e->getApplication();
        $config      = $application->getServiceManager()->get('Config');

        if (isset($config['yima_adminor']) && is_array($config['yima_adminor'])) {
        	if (isset($config['yima_adminor']['auto_set_controllers'])
                && $config['yima_adminor']['auto_set_controllers'] )
        	{
        		$controllerLoader = $application->getServiceManager()->get('ControllerLoader');

        		if (!$controllerLoader->has($controllerService)) {
                    // register controller if not exists *********************************\
        			$controllerPath	= $module .'\\'. 'Controller' .'\\'. 'Admin' .'\\'. $controller.'Controller';
        			$controllerLoader->setInvokableClass($controllerService, $controllerPath);
        		}
        	}
        }

        $matches->setParam('controller', $controllerService);
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
