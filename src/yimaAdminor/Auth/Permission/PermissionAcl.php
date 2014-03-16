<?php
namespace yimaAdminor\Auth\Permission;

use yimaAdminor\Auth\Guard\AclGuard;
use yimaAuthorize\Guard\GuardInterface;
use yimaAuthorize\Permission\PermissionInterface;

/**
 * Class PermissionSample
 *
 * @package yimaAdminor\Permission
 */
class PermissionAcl implements PermissionInterface
{
    /**
     * Get name of this permission section
     * - access from registry
     *
     * @return string
     */
    public function getName()
    {
        return 'yima_adminor';
    }

    /**
     * Is allowed to features?
     *
     * @param null|string $role
     * @param null|string $resource
     * @param null|string $privilege
     *
     * @return boolean
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        // TODO: Implement isAllowed() method.
    }

    /**
     * Get identity name for current role
     * exp. guest for site members
     *
     * @return mixed
     */
    public function getRoleIdentity()
    {
        // TODO: Implement getRoleIdentity() method.
    }

    /**
     * Get Identity data about current role
     *
     * @return mixed
     */
    public function getStorageIdentity()
    {
        // TODO: Implement getStorageIdentity() method.
    }

    /**
     * Factory from array
     *
     * @param array $options
     *
     * @return self
     */
    public function factoryFromArray(array $options)
    {
        // TODO: Implement factoryFromArray() method.
    }

    /**
     * Get guard
     *
     * @return GuardInterface
     */
    public function getGuard()
    {
        return new AclGuard();
    }
}
