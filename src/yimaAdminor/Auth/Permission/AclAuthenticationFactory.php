<?php
namespace yimaAdminor\Auth\Permission;

use yimaAdminor\Auth\Adapter\SimpleFile;
use yimaAuthorize\Permission\PermissionInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager;

/**
 * Class AclAuthenticationFactory
 *
 * @package yimaAdminor\Auth\Permission
 */
class AclAuthenticationFactory implements ServiceManager\FactoryInterface
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
        $authService = new AuthenticationService(
            null, // use default session storage
            new SimpleFile()
        );

        $permissions = new AclAuthentication($authService);

        return $permissions;
    }
}
