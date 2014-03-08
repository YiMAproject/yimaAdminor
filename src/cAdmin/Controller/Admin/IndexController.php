<?php
namespace cAdmin\Controller\Admin;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
