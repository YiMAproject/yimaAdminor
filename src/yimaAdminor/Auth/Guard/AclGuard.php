<?php
namespace yimaAdminor\Auth\Guard;

use yimaAdminor\Service\Share;
use yimaAuthorize\Guard\GuardInterface;
use yimaAuthorize\Permission\PermissionInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class AclGuard
 *
 * @package yimaAdminor\Auth\Guard
 */
class AclGuard implements GuardInterface
{
    protected $listeners = array();

    /**
     * @var PermissionInterface
     */
    protected $permission;

    /**
     * Construct
     *
     * @param PermissionInterface $permission
     */
    public function __construct(PermissionInterface $permission)
    {
        $this->setPermission($permission);
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -100000);
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @param MvcEvent $event
     *
     * @return void
     */
    public function onRoute(MvcEvent $event)
    {
        if (!Share::isOnAdmin()) {
            // we are not in admin area
            return;
        }

        // extract r:[module] p:[controler.action] from route
        // ...

        $matchRoute = $event->getRouteMatch();

        $role      = null;
        $module    = null;
        $privilege = null;

        $service = $this->getPermission();
        if (!$service->isAllowed($role, $module, $privilege)) {
            // Deny Access To Admin

            // Redirect to admin login page
            $matchRoute->setParam('module', 'yimaAdminor');
            $matchRoute->setParam('controller', 'Account');
            $matchRoute->setParam('action', 'login');

            $event->setError('You have not authorized to access');
            $event->setParam('route', $matchRoute);
            $event->setParam('identity', $service->getIdentity());
            $event->setParam('exception', new \Exception('You are not authorized to access ' . $matchRoute->getMatchedRouteName()));


            /* @var $app \Zend\Mvc\Application */
            $app = $event->getTarget();

            $app->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
        }
    }

    /**
     * Set Permission
     *
     * @param \yimaAuthorize\Permission\PermissionInterface $permission
     */
    public function setPermission(PermissionInterface $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Get permission name
     *
     * @return PermissionInterface
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     *
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
