<?php
namespace yimaAdminor\Auth;

use yimaAdminor\Auth\Authorize\PermResource;
use yimaAuthorize\Auth\AbstractAuthGuard;
use yimaAuthorize\Auth\Interfaces\GuardInterface;
use yimaAuthorize\Exception\AuthException;
use yimaBase\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;

class AuthServiceGuard extends AbstractAuthGuard
    implements GuardInterface
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE
            , array($this, 'onRoute')
            , -1000
        );

        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ERROR
            , array($this, 'onError')
            , 10001
        );
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @param MvcEvent $event
     */
    public function onRoute(MvcEvent $event)
    {
        $authService = $this->authService;

        $match = $event->getRouteMatch();
        if (!$authService->isAllowed(new PermResource(['route_match' => $match]), null))
            $authService->riseException();
    }

    function onError(MvcEvent $event)
    {
        $error = $event->getError();

        if (!$error instanceof AuthException)
            // no error, we're ok
            return ;

        if (get_class($error->getAuthService()) !== get_class($this->authService))
            // we are only handle error rised from this service
            return ;

        // Save Current Request, To Redirect User After Login:
        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $event->getRequest();
        $this->storeRedirectUrl($request->getRequestUri());

        ## $res can be Response, ViewModel Instance or an array feed for Default ViewModel
        $url      = $event->getRouter()->assemble(['action' => 'login'], ['name' => 'yima_adminor_auth']);
        $response = $event->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);
        $event->setResult($response);
    }
}
