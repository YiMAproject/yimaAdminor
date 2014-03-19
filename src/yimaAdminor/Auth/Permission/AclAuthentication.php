<?php
namespace yimaAdminor\Auth\Permission;

use yimaAdminor\Auth\Guard\AclGuard;
use yimaAdminor\Auth\Permission\AclAuthentication\UserLoginAssertion;
use yimaAuthorize\Guard\GuardInterface;
use yimaAuthorize\Permission\PermissionInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;

/**
 * Class AclAuthentication
 *
 * @package yimaAdminor\Permission
 */
class AclAuthentication implements PermissionInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * Construct
     *
     * @param $authService
     */
    public function __construct($authService)
    {
        $this->setAuthService($authService);
    }

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
        return $this->getAcl()->isAllowed($role, $resource, $privilege);
    }

    /**
     * Get Identity data for current authenticated role
     *
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->getAuthService()->getIdentity();
    }

    /**
     * Get guard
     *
     * @return GuardInterface
     */
    public function getGuard()
    {
        return new AclGuard($this);
    }

    /**
     * @return \Zend\Permissions\Acl\Acl
     */
    public function getAcl()
    {
        if ($this->acl == null) {
            $acl = new Acl();
            $acl->addResource('yimaAdminor');

            $this->setAcl($acl);
        }

        return $this->acl;
    }

    /**
     * @param \Zend\Permissions\Acl\Acl $acl
     */
    public function setAcl($acl)
    {
        // user must have logged in to access for all system resources
        $assertion = new UserLoginAssertion($this->getAuthService());
        $acl->allow(null, null, null, $assertion);

        $this->acl = $acl;
    }

    /**
     * Get Authentication Service
     *
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * Set Authentication Service
     *
     * @param \Zend\Authentication\AuthenticationService $authService
     */
    public function setAuthService($authService)
    {
        $this->authService = $authService;
    }
}
