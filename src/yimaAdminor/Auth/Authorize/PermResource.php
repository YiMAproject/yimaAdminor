<?php
namespace yimaAdminor\Auth\Authorize;

use Poirot\AuthSystem\Authorize\Interfaces\iAuthResource;
use Poirot\Core\AbstractOptions;
use Zend\Mvc\Router\Http\RouteMatch;

class PermResource extends AbstractOptions
    implements iAuthResource
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    function setRouteMatch(RouteMatch $routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    function getRouteMatch()
    {
        return $this->routeMatch;
    }
}
