<?php
namespace yimaAdminor;

use yimaAdminor\Mvc\AdminRouteListener;
use yimaAdminor\Mvc\AdminTemplateListener;
use Zend\Authentication;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\SimpleRouteStack as HttpSimpleRouteStack;
use Zend\Stdlib\ArrayUtils;

/**
 * Class Module
 *
 * @package yimaAdminor
 */
class Module implements
    BootstrapListenerInterface,
    ServiceProviderInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface,
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
        /** @var $r \Zend\Mvc\Router\Http\TreeRouteStack */
        $r = $e->getRouter();
        if ($r instanceof HttpSimpleRouteStack) {
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


    	// .........................................................................
        $events	= $e->getApplication()->getEventManager();
        
        // determine if we are on admin set specific admin controller to router
        $events->attach(new AdminRouteListener());
        
        // determine if we are on admin add pathStack to yimaAdminor template folder and
        // set admin prefix to viewModel layout on render
        $events->attach(new AdminTemplateListener());
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

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
    	return array(
    		'factories' => array (
    			'yimaAdminor.navigation' => 'yimaAdminor\Navigation\Service\NavigationFactory',
    			'yimaAdminor\Authentication\Service' => function ($sm) {
    				return new Authentication\AuthenticationService(
    					$sm->get('yimaAdminor\Authentication\Storage'),
    					$sm->get('yimaAdminor\Authentication\Adapter')
    				);
    			},
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
        $config = include __DIR__ . '/../../config/module.config.php';

        return ArrayUtils::merge(
            array(
                'router' => array(
                    'routes' => array(
                        \yimaAdminor\Module::ADMIN_ROUTE_NAME => array(
                            'type'    => 'Literal',
                            'options' => array(
                                # /admin
                                'route'    => '/'.\yimaAdminor\Module::ADMIN_ROUTE_SEGMENT,
                                'defaults' => array(
                                    'module' 	   => 'yimaAdminor', // also you can use zend approach __NAMESPACE__ as alternate
                                    'controller'   => 'Index',
                                    'action'       => 'dashboard',
                                ),
                            ),
                            'may_terminate' => true,
                            'child_routes' => array(
                                'default' => array(
                                    'type'    => 'yimaAdminorRouter',
                                    'options' => array(
                                        # we can use any word after /admin[/word]/ to browse child routes
                                        'route' => '/', //'/browse/',
                                        'defaults' 	 => array(
                                            'controller' => 'Index',
                                            'action'     => 'dashboard',
                                        ),
                                        # we can pass any options to Router
                                        // 'option' => 'value',

                                        # encrypt and decrypt class name or object() to de/encode queries for Crypto router
                                        //'cryption' => '\yimaAdminor\Mvc\Router\Http\Crypto\CryptionBase64'
                                    ),
                                    'may_terminate' => true,
                                ),
                            ),
                        ),
                    ),
                ),// end of router
            ),
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
