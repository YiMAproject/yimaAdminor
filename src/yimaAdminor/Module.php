<?php
namespace yimaAdminor;

use yimaAdminor\Mvc\AdminRouteListener;
use Zend\Authentication;

use Zend\Console\Console;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package yimaAdminor
 */
class Module implements
    InitProviderInterface,
    BootstrapListenerInterface,
    ServiceProviderInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    LocatorRegisteredInterface
{
	const ADMIN_ROUTE_NAME	  = 'adminorRouter';
	const ADMIN_DEFAULT_ROUTE_NAME  = 'adminorRouter/default';
	const ADMIN_ROUTE_SEGMENT = 'admin'; // /[admin]

    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $moduleManager
     * @throws \Exception
     * @return void
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        $loadedModules   = array_keys($moduleManager->getLoadedModules(false));
        $requiredModules = [
            'Application', 'AssetManager', 'yimaTheme', 'yimaAuthorize',
        ];
        if(array_intersect($requiredModules, $loadedModules) != $requiredModules)
            throw new \Exception(
                'Adminor Module Require These Modules: '
                . implode(', ', $requiredModules)
            );
    }

    /**
     * Listen to the bootstrap MvcEvent
     *
     * this will setup and run admin features
     *
     * @param EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        if (Console::isConsole()) {
            // Admin Panel is Only Available for HTTP Request
            return false;
        }

        /** @var $e MvcEvent */
        // Attach default Events to event manager         **********************\
        $events	= $e->getApplication()->getEventManager();

        // Set is on admin? flag Service
        // If we are on admin set specific admin controller to router
        $events->attach(new AdminRouteListener());
        // If we are on admin set admin prefix to viewModel layout on render
        // this moved to merged config [view_manager][mvc_strategies]
        // $events->attach(new AdminTemplateListener());
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
    	return [
            'invokables' => [
                # If we are on admin set admin prefix to viewModel layout on render
                'yimaAdminor.MvcView.AdminMvcRenderStrategies' => 'yimaAdminor\Mvc\AdminMvcRenderStrategies',
            ],
    		'factories' => [
                # Admin Navigation Menu
    			'yimaAdminor.Navigation' => 'yimaAdminor\Service\NavigationFactory',
    		],
    	];
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        $conf = [];
        foreach (glob(__DIR__.'/../../config/*.config.php') as $conFile) {
            $conFile = include_once $conFile;
            if (is_array($conFile))
                $conf = \Poirot\Core\array_merge($conf, $conFile);
        }

        // usually return like this
        return $conf;
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        ];
    }
}
