<?php
namespace yimaAdminor\Service;

use Poirot\AuthSystem\Authenticate\Interfaces\iAuthenticateAdapter;
use yimaAdminor\Auth\AuthService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthServiceFactory implements FactoryInterface
{
    /**
     * Create the Authentication Service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     *
     * @return iAuthenticateAdapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator->getServiceLocator();

        $config = $serviceLocator->get('Config');
        $config = (isset($config['yima_adminor']) && is_array($config['yima_adminor']))
            ? $config['yima_adminor']
            : [];

        $opts = [];
        if (isset($config['auth_service'])
            && is_array($config['auth_service'])
        )
            $opts = $config['auth_service'];

        $authService = new AuthService($opts);
        return $authService;
    }
}
