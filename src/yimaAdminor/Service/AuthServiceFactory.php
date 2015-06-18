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

        if (isset($opts['auth_adapter']) && $srvAdapter = $opts['auth_adapter']) {
            if (is_string($srvAdapter)) {
                if (!class_exists($srvAdapter))
                    // it's a registered service
                    $opts['auth_adapter'] = $serviceLocator->get($srvAdapter);
                else
                    $opts['auth_adapter'] = $serviceLocator->get($srvAdapter);
            }
        }

        $authService = new AuthService($opts);
        return $authService;
    }
}
