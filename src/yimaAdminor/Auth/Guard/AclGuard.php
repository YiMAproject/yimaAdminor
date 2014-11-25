<?php
namespace yimaAdminor\Auth\Guard;

use yimaAdminor\Service\Share;
use yimaAuthorize\Auth\Guard\GuardInterface;
use yimaAuthorize\Auth\Permission\PermissionInterface;
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

        $service = $this->getPermission();
        if (!$service->getIdentity()) {
            // --- User Must Sign in ---

            // Redirect to admin login page -- {
            /** @var $response \Zend\Http\PhpEnvironment\Response */
            $response = $event->getResponse();

            /** @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
            $router = $event->getRouter();
            $url    = $router->assemble(array(), array('name' => 'yima_adminor_auth'));
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            $event->setResult($response);

            /* @var $app \Zend\Mvc\Application */
            $event->stopPropagation(true);
            $app = $event->getTarget();
            $app->getEventManager()->trigger(MvcEvent::EVENT_RENDER, $event);
            // -- }
        }

        // Check For Privileges

        // extract r:[module] p:[controler.action] from route
        // ...

        /** @var $event \Zend\Mvc\MvcEvent */
        /*$matchRoute = $event->getRouteMatch();

        $role      = null;
        $module    = null;
        $privilege = null;*/

        /*!$service->isAllowed($role, $module, $privilege)*/
    }

    /**
     * Set Permission
     *
     * @param \yimaAuthorize\Auth\Permission\PermissionInterface $permission
     *
     * @return $this
     */
    public function setPermission(PermissionInterface $permission)
    {
        $this->permission = $permission;

        return $this;
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
