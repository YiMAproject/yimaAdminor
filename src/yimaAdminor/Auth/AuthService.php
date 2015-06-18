<?php
namespace yimaAdminor\Auth;

use Poirot\AuthSystem\Authenticate\Adapter\AbstractAdapter;
use Poirot\AuthSystem\Authenticate\Adapter\DigestFileAuthAdapter;
use Poirot\AuthSystem\Authenticate\Exceptions\AccessDeniedException;
use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticateAdapter;
use Poirot\AuthSystem\Authenticate\Interfaces\iIdentity;
use Poirot\AuthSystem\Authorize\Interfaces\iAuthResource;
use Poirot\AuthSystem\BaseIdentity;
use Poirot\Core\AbstractOptions;
use yimaAdminor\Auth\Authorize\PermResource;
use yimaAuthorize\Auth\Interfaces\GuardInterface;
use yimaAuthorize\Auth\Interfaces\MvcAuthServiceInterface;
use yimaAuthorize\Exception\AuthException;

class AuthService extends AbstractOptions
    implements MvcAuthServiceInterface
{
    /**
     * @var AbstractAdapter
     */
    protected $authAdapter;

    /**
     * @var BaseIdentity
     */
    protected $identity;

    /**
     * Is allowed to features?
     *
     * @param null|iAuthResource $resource
     * @param null|iIdentity     $role
     *
     * @throws \Exception
     * @return boolean
     */
    function isAllowed(/*iAuthResource*/ $resource = null, /*iIdentity*/ $role = null)
    {
        $role = ($role) ?: $this->getAuthAdapter()->identity();

        if (!is_object($resource)
            || (!$resource instanceof PermResource || !method_exists($resource, 'getRouteMatch'))
        )
            throw new \Exception('Invalid Resource Type, Can`t Check The Permissions.');

        $return = true;

        $matchedRouteName = $resource->getRouteMatch()->getMatchedRouteName();

        // Access Admin Route Need Authorized User
        if ($matchedRouteName == \yimaAdminor\Module::ADMIN_ROUTE_NAME)
            $return = $return && $role->hasAuthenticated();

        return $return;
    }

    /**
     * Get Authorized User Identity
     *
     * - identities must inject into adapter by auth services
     *
     * @throws \Exception Not Identity Available Or Set
     * @return BaseIdentity
     */
    function identity()
    {
        if (!$this->identity)
            $this->identity = new BaseIdentity(get_class($this));

        return $this->identity;
    }

    /**
     * Get Authenticate Adapter
     *
     * @return iAuthenticateAdapter
     */
    function getAuthAdapter()
    {
        if (!$this->authAdapter)
            $this->setAuthAdapter(new DigestFileAuthAdapter);

        // always use current identity
        $this->authAdapter->setIdentity($this->identity());

        return $this->authAdapter;
    }

    /**
     * Set Authentication Adapter
     *
     * @param AbstractAdapter $adapter
     *
     * @return $this
     */
    function setAuthAdapter(AbstractAdapter $adapter)
    {
        $this->authAdapter = $adapter;

        return $this;
    }

    /**
     * Get guard
     *
     * @return GuardInterface
     */
    public function getGuard()
    {
        return new AuthServiceGuard($this);
    }

    /**
     * Throw Exception
     *
     * - usually inject $this Object argument into AuthException Class
     *   on return, so later to handle the error with guards we can
     *   response only for errors that rise from related AuthService
     *
     * - recommend exception have valid http response code as exception code
     *
     * @param AccessDeniedException|\Exception $exception
     *
     * @throws AuthException
     */
    function riseException(\Exception $exception = null)
    {
        ($exception !== null) ?: $exception = new AccessDeniedException();

        throw new AuthException($this, $exception->getCode(), $exception);
    }
}
