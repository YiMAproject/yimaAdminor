<?php
namespace yimaAdminor\Service;

use yimaAdminor;
use Zend\Navigation\Service\DefaultNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\RouteStackInterface as Router;
use Zend\Navigation\Exception;
use Zend\Mvc\ModuleRouteListener;

/**
 * Class NavigationFactory
 *
 * @package yimaAdminor\Service
 */
class NavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'admin';
    }
    
    /**
     * @param array $pages
     * @param RouteMatch $routeMatch
     * @param Router $router
     *
     * @return mixed
     */
    protected function injectComponents(array $pages, RouteMatch $routeMatch = null, Router $router = null)
    {
    	foreach ($pages as &$page) {
    		$hasMvc = isset($page['action']) || isset($page['controller']) || isset($page['module']) || isset($page['route']);
    		if ($hasMvc) {
    			// add default admin route name to navigation
    			if (!isset($page['route'])) {
    				$page['route'] = yimaAdminor\Module::ADMIN_ROUTE_NAME.'/default';
    			}
    			
    			// check mikonim agar dar route e "admin/default" boodim ... default apporach {
    			// we need 'module' inside 'params' parameter key to use inside router, navigation ..
    			$routeName = $page['route'];
    			if (strstr($routeName,'/')) {
    				$routeName = substr($routeName, 0, strpos($routeName,'/'));
    			}
    			
    			if ($routeName == yimaAdminor\Module::ADMIN_ROUTE_NAME
    				&& ($page['route'] == yimaAdminor\Module::ADMIN_ROUTE_NAME.'/default'
    					|| $page['route'] == yimaAdminor\Module::ADMIN_ROUTE_NAME.'/default/query'
    				   )
    			) {
    				// we must put 'module' inside 'params'
    				if (!isset($page['params']) || !is_array($page['params'])) {
    					$page['params'] = array(); 
    				}
    				
    				$moduleKey = isset($page['module']) ? 'module' : ModuleRouteListener::MODULE_NAMESPACE;
    				if (!isset($page[$moduleKey])) {
    					throw new Exception\InvalidArgumentException('You are using default admin route and we can\'t not find "module" configuration key');
    				}
    				
    				$module = $page[$moduleKey];
    				$page['params'] = array_merge($page['params'], array('module' => $module));
    			}
    			// }
    			
    			if (!isset($page['routeMatch']) && $routeMatch) {
    				$page['routeMatch'] = $routeMatch;
    			}
    			if (!isset($page['router'])) {
    				$page['router'] = $router;
    			}
    		}
    
    		if (isset($page['pages'])) {
    			$page['pages'] = $this->injectComponents($page['pages'], $routeMatch, $router);
    		}
    	}
    
    	return $pages;
    }
}
