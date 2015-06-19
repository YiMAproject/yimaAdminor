<?php
namespace yimaAdminor\Controller;

use Poirot\AuthSystem\Authenticate\Exceptions\AuthenticationException;
use Poirot\AuthSystem\Authenticate\Exceptions\WrongCredentialException;
use yimaAdminor\Auth\AuthService;
use Zend\Mvc\Controller\AbstractActionController;

class AccountController extends AbstractActionController
{
    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * Login
     */
    public function loginAction()
    {
        /** @var AuthService $auth */
        $auth = $this->_getAuthService();

        if ($auth->identity()->hasAuthenticated()) {
            // : User is authorized

            $this->redirect()->toRoute(\yimaAdminor\Module::ADMIN_ROUTE_NAME);
            return $this->getResponse();
        }

        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $this->getRequest();

        // Get redirect-url:
        $redirectUrl = $auth->getGuard()->getStoredUrl();
        if($request->isPost() && empty($redirectUrl))
            $redirectUrl = $this->params()->fromPost('redirect_url');

        if ($request->isPost()) {
            $user = $this->params()->fromPost('login-username');
            $pass = $this->params()->fromPost('login-password');
            $rmbr = $this->params()->fromPost('login-remember-me', false);

            $authAdabter = $auth->getAuthAdapter();
            $authAdabter->credential(['username' => $user, 'password' => $pass]);

            try {
                $authAdabter->authenticate();
            }
            catch (WrongCredentialException $e) {
                // set error messages
                $this->flashMessenger('adminor.auth.message')->addErrorMessage(
                    $this->_translator()->translate('Invalid Username Or Password')
                );

                $this->redirect()->refresh();

                return $this->getResponse();
            }
            catch (AuthenticationException $e) {
                // set error messages
                $this->flashMessenger('adminor.auth.message')->addErrorMessage(
                    $this->_translator()->translate('Activation Code Was Sent To Your Mail, Please Check Your Mailbox.')
                );

                $this->redirect()->refresh();

                return $this->getResponse();
            }
            catch (\Exception $e) {
                throw $e;
            }

            $authAdabter->getIdentity()
                ->setRemember($rmbr)
                ->login();

            // Successful login redirect user:
            if (!empty($redirectUrl))
                $this->redirect()->toUrl($redirectUrl);
            else
                $this->redirect()->toRoute(\yimaAdminor\Module::ADMIN_ROUTE_NAME);

            return $this->getResponse();
        }

        // Build View:

        $errMessages = $this->flashMessenger('adminor.auth.message')->getErrorMessages();
        return [
            'messages'     => $errMessages,
            'redirect_url' => $redirectUrl,
        ];
    }

    /**
     * Logout
     */
    public function logoutAction()
    {
        $this->_getAuthService()
            ->identity()->logout();

        $this->redirect()->toRoute('yima_adminor_auth');
        return $this->getResponse();
    }

    protected function _translator()
    {
        return $this->getServiceLocator()->get('MvcTranslator');
    }

    /**
     * @return AuthService
     */
    protected function _getAuthService()
    {
        if (!$this->authService)
            $this->authService = $this->authorize('yima_adminor');

        return $this->authService;
    }
}
