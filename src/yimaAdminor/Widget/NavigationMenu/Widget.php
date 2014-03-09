<?php
namespace yimaAdminor\Widget\NavigationMenu;

use yimaWidgetator\Widget\AbstractMvcWidget;

use Zend\View\Model\ViewModel;
use Zend\Navigation\AbstractContainer;
use Zend\View\Helper\Navigation as NavigationHelper;

class Widget extends AbstractMvcWidget
{
	/**
	 * Navigation Container
	 * 
	 * @var Zend\Navigation\AbstractContainer
	 */
	protected $container;
	
	public function render()
	{
        return $this->getView()->render(
            $this->getLayout(),
            array(
                'container' => $this->getContainer()
            )
        );
	}
	
	/**
	 * Proxy to container
	 * 
	 * findMagicBy[Property]('value') will find container by this attrib value
	 * and replace current container on match
	 * 
	 * @param  $method
	 * @param  $arguments
	 */
	public function __call($method, $arguments)
	{
		try {
			// method inside AbstractWidget such as methodAction calls
			$return = parent::__call($method, $arguments);
		} catch (Exception\NotFoundMethodException $e) {
			// proxy to container
			/* dastoorat e shaamel e find*By[*] mojeb mishavand ke meghdaar
			 * e baazgashti e aan baa container e fe'li jaaigozin shavad
			 */
			$container = $this->getContainer();
			
			$result = preg_match('/(magic(?:Find)?By)(.+)/', $method, $match);
			if ($result) {
				$method    = str_replace('magicFindBy','findOneBy',$method);
				$container = call_user_func_array(array($container,$method),$arguments);
				if ($container) {
					$this->setContainer($container);
				} else {
					$this->setContainer(new \Zend\Navigation\Navigation());
				}

				$return    = $this;
			} else {
				/* baaghi e dastoorat meghdaar method i ke az container ejraa shode ra return mikonad */
				$return = call_user_func_array(array($container,$method),$arguments);
			}
		}
		
		return $return;
	}
	
	/**
	 * proxy to setContainer
	 */
	public function setNavigation($container)
	{
		return $this->setContainer($container);
	}
	
	public function setContainer($container)
	{
		$this->container = $container;
		
		return $this;
	}
	
	/**
	 * proxy to getContainer
	 */
	public function getNavigation()
	{
		return $this->getContainer();
	}
	
	/**
	 * code moved here becuase of serviceLocator is
	 * available afte instantiate through initializers
	 * 
	 * @throws Exception\InvalidArgumentException
	 */
	public function getContainer()
	{
		$container = $this->container;
		if ($container instanceof AbstractContainer) {
			return $container;
		}
		
		if (is_object($container)) {
			if ($container instanceof NavigationHelper) {
				$container = $container->getContainer();
			}
		}
		elseif (is_string($container)) {
			$sl = $this->getServiceLocator()->getServiceLocator();
			$container = $sl->get($container);
		}
		
		if (!$container instanceof AbstractContainer) {
			throw new Exception\InvalidArgumentException(
					'Container must be a string alias or an instance of ' .
					'Zend\Navigation\AbstractContainer or Zend\View\Helper\Navigation'
			);
		}
		
		$this->container = $container;

		return $this->container;
	}
}
