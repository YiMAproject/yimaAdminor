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
use Zend\Mvc\Router\SimpleRouteStack as HttpSimpleRouteStack;
use Zend\Stdlib\ArrayUtils;

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
	const ADMIN_ROUTE_NAME	  = 'admin';
	const ADMIN_ROUTE_SEGMENT = 'admin'; // /[admin]

    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     * @return void
     */
    public function init(ModuleManagerInterface $manager)
    {
        // we need this module up and runing
        $manager->loadModule('yimaAuthorize');
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
        /** @var $r \Zend\Mvc\Router\Http\TreeRouteStack */
        $r = $e->getRouter();
        if ($r instanceof HttpSimpleRouteStack) {
            // Add Admin Specific Router From Config into RoutePlugin Manager **********************\

            /** @var $routePluginManager \Zend\Mvc\Router\RoutePluginManager */
            $routePluginManager = $r->getRoutePluginManager();
            if ($routePluginManager->has('yimaAdminorRouter') ){ //full name for easy search within codes
                throw new \Exception(
                    sprintf(
                        'Router "yimaAdminor" is not registered on RouterPlugin(%s).',
                        get_class($routePluginManager)
                    )
                );
            }

            // Set RouteInterface from config {
            $router = null;
            $sm     = $e->getApplication()->getServiceManager();
            $config = $sm->get('config');
            if (isset($config['yima_adminor']) && is_array($config['yima_adminor'])
                && isset($config['yima_adminor']['router'])
            ) {
                $router = $config['yima_adminor']['router'];
            }

            // plugin manager will validate router (isValid)
            $routePluginManager->setInvokableClass(
                'yimaAdminorRouter',
                $router
            );
            // ... }
        } // end of if HttpRouteStack


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
    	return array(
            'invokables' => array(
                # If we are on admin set admin prefix to viewModel layout on render
                'yimaAdminor.MvcView.AdminTemplateListener' => 'yimaAdminor\Mvc\AdminTemplateListener',
            ),
    		'factories' => array (
                # Admin Navigation Menu
    			'yimaAdminor.Navigation' => 'yimaAdminor\Service\NavigationFactory',
    		),
    	);
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        $confDir = realpath(__DIR__ . '/../../config');

        $config = include_once $confDir.'/module.config.php';

        return ArrayUtils::merge(
            include_once $confDir.'/module.route.config.php',
            $config
        );
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
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
