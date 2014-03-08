<?php
namespace yimaAdminor\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;

use Zend\Http\Header\Authorization as AuthorizationHeader;
use Zend\Authentication\Adapter\Http as HttpAuthenticate;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class AccountController extends AbstractActionController
{
    public function logoutAction()
    {
    	$sl = $this->getServiceLocator();
    	
    	$authService = $sl->get('yimaAdminor\Authentication\Service');
    	$authAdapter = $authService->getAdapter();
    	if (! $authAdapter instanceof HttpAuthenticate) {
    		throw new Exception(__CLASS__.'::'.__FUNCTION__.' only work for Http Authentication Service Adapter.');
    	}
    	
    	$request = $authAdapter->getRequest();
    	$headers = $request->getHeaders();
    	
    	if (! ($headers->has('Authorization') xor $headers->has('Proxy-Authorization')) ) {
    		// we are not authorized yet 
    		$this->redirect()->toRoute(\yimaAdminor\Module::ADMIN_ROUTE_NAME);
    		return;
    	}
    	
    	// /\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
    	
    	// enabling logout session
    	$sessionManager   = new SessionManager();
    	$sessionContainer = new Container('yimaAdminorLogout',$sessionManager);
    	if (!isset($sessionContainer->logoutFlag)) {
    		$sessionContainer->setExpirationHops(1, null, true);
    		$sessionContainer->logoutFlag = true;
    		
    		// remove "Proxy-Authorization" or "Authorization" Header from request, it will chalange user again
    		foreach ($headers as $h) {
    			if ($h instanceof AuthorizationHeader) {
    				$headers->removeHeader($h);
    			}
    		}
    		
    		$request->setHeaders($headers);
    		$authAdapter->setRequest($request);
    		
    		// chalengin user to ask a new login
    		$result = $authService->authenticate();
    		
    		// clear storage if exists anyway
    		$authService->clearIdentity();
    		
    		return array('logged_out' => true);
    	} 
    	else {
    		$result = $authService->authenticate();
    		/* if (! $result->isValid()) {
    			// successfully logout
    			$this->redirect()->toRoute(\yimaAdminor\Module::ADMIN_ROUTE_NAME);
    			return;
    		} */
    	}
    	
    	return array('logged_out' => false);
    }
    
    /**
     * Forbidden Access
     */
    public function forbiddenAction()
    {
    	
    }
    
}
