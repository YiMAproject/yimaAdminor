<?php
namespace yimaAdminor\Auth\Guard;

use yimaAuthorize\Guard\GuardInterface;
use yimaAuthorize\Service\PermissionsRegistry;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class AclGuard implements GuardInterface
{
    protected $listeners = array();

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
        // TODO: Implement attach() method.
    }

    /**
     * Get permission name
     *
     * @return string
     */
    public function getPermissionName()
    {
        // TODO: Implement getPermissionName() method.
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
        // TODO: Implement detach() method.
    }
}
