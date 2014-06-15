<?php
namespace yimaAdminor\Mvc;

use yimaAdminor\Service\Share;
use yimaTheme\Resolvers\LocatorResolverAwareInterface;
use yimaTheme\Resolvers\ResolverInterface;
use yimaTheme\Theme\LocatorDefaultInterface;

/**
 * Class AdminThemeResolver
 * : serve the admin theme when we are on admin area
 *
 * @package yimaAdminor\Mvc
 */
class AdminThemeResolver implements
    ResolverInterface,
    LocatorResolverAwareInterface
{
    /**
     * @var \yimaTheme\Theme\Locator
     */
    protected $themeLocator;

    /**
     * Get default admin template name from merged config
     *
     * @return bool
     */
    public function getName()
    {
        $name = false;

        if (Share::isOnAdmin()) {
            // - we are on admin
            $config = $this->themeLocator->getServiceLocator();
            $config = $config->get('config');
            if (isset($config['yima_adminor']) && is_array($config['yima_adminor'])) {

                $name = (isset($config['yima_adminor']['default_theme']))
                    ? $config['yima_adminor']['default_theme']
                    : false;
            }
        }

        return $name;
    }

    /**
     * Set theme locator
     *
     * @param LocatorDefaultInterface $l
     *
     * @return $this
     */
    public function setThemeLocator(LocatorDefaultInterface $l)
    {
        $this->themeLocator = $l;

        return $this;
    }
}
