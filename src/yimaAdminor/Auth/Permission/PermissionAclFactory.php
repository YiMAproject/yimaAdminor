<?php
namespace yimaAdminor\Auth\Permission;

use yimaAuthorize\Permission\PermissionInterface;
use Zend\ServiceManager;

class PermissionAclFactory implements ServiceManager\FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return PermissionInterface
     */
    public function createService(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $permissions = new PermissionAcl();

        return $permissions;
    }
}
