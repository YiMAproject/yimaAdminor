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
	const ADMIN_ROUTE_NAME	  = 'adminorRouter';
	const ADMIN_DEFAULT_ROUTE_NAME  = 'adminorRouter/default';
	const ADMIN_ROUTE_SEGMENT = 'admin'; // /[admin]

    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $moduleManager
     * @return void
     */
    public function init(ModuleManagerInterface $moduleManager)
    {
        // we need this module up and runing
        /** @var $moduleManager \Zend\ModuleManager\ModuleManager */
        $moduleManager->loadModule('yimaAuthorize');

        $loadedModules = $moduleManager->getLoadedModules();
        if (!in_array('yimaTheme', array_keys($loadedModules))) {
            throw new \Exception(
                'YimaTheme Module Not Loaded Yet!! Adminor need yimaTheme Loaded Before.'.
                'You can put YimaTheme higher than yimaAdminor in your application config'
            );
        }
        // $manager->loadModule('yimaTheme');
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

        return include_once $confDir.'/module.config.php';
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
