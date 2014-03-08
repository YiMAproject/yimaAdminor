<?php
namespace cAdmin\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ModelInterface as ViewModel;

class AdminTemplateListener implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'injectViewModelLayout'), -95);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'injectViewModelLayout'), -95);
    }

    public function injectViewModelLayout(MvcEvent $e)
    {
        $model = $e->getResult();
        if (! $model instanceof ViewModel) {
            // Can't do anything 
            return;
        }
        
        $serviceLocator = $e->getApplication()->getServiceManager();
        if (false == $serviceLocator->get('cAdminModule')->isOnAdmin()) {
        	// we are not on admin
        	return;
        }
        
        // template prefix from config, this is admin template name
        $tn = '';
        $config = $serviceLocator->get('config');
        if (isset($config['admin']) && is_array($config['admin'])) {
        	if (isset($config['admin']['template_name']))
        	{
        		$tn = $config['admin']['template_name'];
        	}
        }
        
        $response = $e->getResponse();
        if ($response->getStatusCode() != 400 && $response->getStatusCode() != 500) {
        	// set template prefix
        	$template = $model->getTemplate();
        	//$template = (! empty($tn)) ? $tn.'/'.$template : $template;
        	$template = 'admin'.'/'.$template;
        	$model->setTemplate($template);
        }
               
        // set root template prefix
        // TODO: I think this is not necessery.
        /*
        $rootModel = $e->getViewModel();
        $template  = $rootModel->getTemplate();
        $template  = (! empty($tn)) ? $tn.'/'.$template : $template;// root ViewModel Template  
        $rootModel->setTemplate($template);
        */
        
        // add template pathstack
        if (isset($config['admin']) && is_array($config['admin'])) {
        	if (! isset($config['admin']['template_folder']) || empty($config['admin']['template_folder']))
        	{
        		return;
        	}
        	
        	$templatePath = $config['admin']['template_folder'];
        }
        
        $viewResolver   = $serviceLocator->get('ViewTemplatePathStack');
        
        /* 
        * note: $viewResolver->getPaths() yek object Zend\Stdlib\SplStack ast ke
        * 		 hengaame itterate az aakharin ozv shoroo mikonad, baraaie hamin
        * 		 dar injaa path e admin ro be aval ezaafe kardam
        */
        $paths   = $viewResolver->getPaths()->toArray();
        $paths   = array_reverse($paths);
        
        // aval view e samte template e konooni e admin dar stack gharaar migirad,  
        // be in shekl dar admin mitavan khorooji e masalan widget haa yaa .. digar raa rewrite kard 
        $paths[] = $templatePath; 
        $paths[] = $templatePath.'/'.$tn;
        
        $viewResolver->setPaths($paths);
    }
    
    
    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
    	foreach ($this->listeners as $index => $listener) {
    		if ($events->detach($listener)) {
    			unset($this->listeners[$index]);
    		}
    	}
    }
    
}
