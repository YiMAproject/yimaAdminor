<?php
namespace yimaAdminor\Mvc;

use yimaAdminor\Service\Share;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ModelInterface as ViewModel;

/**
 * Class AdminTemplateListener
 *
 * @package yimaAdminor\Mvc
 */
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

    /**
     * Set admin prefix to viewModel Layouts
     *
     * @param MvcEvent $e
     *
     * @return bool
     */
    public function injectViewModelLayout(MvcEvent $e)
    {
        $model = $e->getResult();
        if (!Share::isOnAdmin() && !$model instanceof ViewModel){
            // - we are not on admin
            // - none of my business
            return false;
        }

        /** @var $response \Zend\Http\PhpEnvironment\Response */
        $response = $e->getResponse();
        if ($response->getStatusCode() != 400 && $response->getStatusCode() != 500) {
        	// set template prefix
        	$template = $model->getTemplate();
        	//$template = (! empty($tn)) ? $tn.'/'.$template : $template;
        	$template = 'admin'.'/'.$template;
        	$model->setTemplate($template);
        }
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
