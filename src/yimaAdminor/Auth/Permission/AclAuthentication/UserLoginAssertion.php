<?php
namespace yimaAdminor\Auth\Permission\AclAuthentication;

use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class UserLoginAssertion
 *
 * @package yimaAdminor\Auth\Permission
 */
class UserLoginAssertion implements AssertionInterface
{
    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * Construct
     *
     * @param AuthenticationService $authService
     */
    public function __construct(AuthenticationService $authService)
    {
        $this->setAuthService($authService);
    }

    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        return $this->getAuthService()->hasIdentity();
    }

    /**
     * @return \Zend\Authentication\AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * @param \Zend\Authentication\AuthenticationService $authService
     */
    public function setAuthService($authService)
    {
        $this->authService = $authService;
    }
}
