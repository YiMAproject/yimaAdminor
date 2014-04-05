<?php
namespace yimaAdminor\Controller;

use yimaAuthorize\Service\PermissionsRegistry;
use Zend\Mvc\Controller\AbstractActionController;

use Zend\Authentication\Adapter\Http as HttpAuthenticate;
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

        $ps = $this->getServiceLocator()->get('yimaAuthorize.PermissionsManager');
        $ps = $ps->get('yima_adminor'); // $ps::get('yima_adminor');
        /** @var $ps \yimaAdminor\Auth\Permission\AclAuthentication */

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
        $ps = $this->getServiceLocator()->get('yimaAuthorize.PermissionsManager');
        $ps = $ps->get('yima_adminor'); // $ps::get('yima_adminor');
        /** @var $ps \yimaAdminor\Auth\Permission\AclAuthentication */

        $ps->getAuthService()->clearIdentity();

        $this->redirect()->toRoute('yima_adminor_auth');
    }
}
