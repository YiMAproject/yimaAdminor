<?php
namespace yimaAdminor\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    public function dashboardAction()
    {
    	/*
    	$viewModel = new ViewModel();
    	$viewModel->setTerminal(true);
    	
    	return $viewModel;
    	*/
    }
    
    /**
     * Forbidden Access
     */
    public function forbiddenAction()
    {
    	
    }
    
}
