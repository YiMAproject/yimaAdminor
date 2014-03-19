<?php
namespace yimaAdminor\Controller;

use yimaAuthorize\Service\PermissionsRegistry;
use Zend\Mvc\Controller\AbstractActionController;

use Zend\Http\Header\Authorization as AuthorizationHeader;
use Zend\Authentication\Adapter\Http as HttpAuthenticate;
use Zend\Session\SessionManager;
use Zend\Session\Container;

/**
 * Class AccountController
 *
 * @package yimaAdminor\Controller
 */
class AccountController extends AbstractActionController
{
    /**
     * Login
     */
    public function loginAction()
    {
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();
        if (! $request->isPost()) {
            return ;
        }

        $email = $this->params()->fromPost('email');
        $pass  = $this->params()->fromPost('password');

        // TODO: using service locator for modules outside of yimaAuthorize
        $ps = PermissionsRegistry::get('yima_adminor');

        /** @var $authService \Zend\Authentication\AuthenticationService */
        $authService = $ps->getAuthService();

        /** @var $authAdapter \yimaAdminor\Auth\Adapter\SimpleFile */
        $authAdapter = $authService->getAdapter();
        $authAdapter->setIdentity($email);
        $authAdapter->setCredential($pass);

        $result = $authService->authenticate();
        if ($result->isValid()) {
            $this->redirect()->toRoute(\yimaAdminor\Module::ADMIN_ROUTE_NAME);
        } else {
            $this->redirect()->refresh();
        }
    }

    /**
     * Logout
     */
    public function logoutAction()
    {
        // TODO: using service locator for modules outside of yimaAuthorize
        $ps = PermissionsRegistry::get('yima_adminor');

        $ps->getAuthService()->clearIdentity();

        $this->redirect()->toRoute('yima_adminor_auth');
    }
}
