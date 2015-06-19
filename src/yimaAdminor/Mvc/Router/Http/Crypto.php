<?php
namespace yimaAdminor\Mvc\Router\Http;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;

use yimaAdminor\Mvc\Router\Http\Crypto\CryptionInterface;

/**
 * Class Crypto
 *
 * @package yimaAdminor\Mvc\Router\Http
 */
class Crypto implements RouteInterface
{
    /**
     * @var int Maximum Priority for Admin Detection Route
     */
    protected $priority = 100000;

	/**
	 * RouteInterface to match.
     * exp. /browse/
	 *
	 * @var string
	 */
	protected $route;
	
    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Any optional Route options that may have used
     *
     * @var array
     */
    protected $params;

    /**
     * Object to encrypt and decrypt queries
     *
     * @var CryptionInterface
     */
    protected $cryptionObject;

    /**
     * Construct
     *
     * @param $route
     * @param array $options
     */
    public function __construct($route, array $options = array())
    {
    	$this->route    = $route;

        /* 'child_routes' => array(
        'default' => array(
            ...
            'options' => array(
                'route' => '/',
                'defaults' 	 => array(
                    'controller' => 'Index',
                    ...
                ),
                'cryption' => '\yimaAdminor\Mvc\Router\Http\Crypto\CryptionBase64'
            ),
        ),
        */

        // default options of route, exp. [controller] => 'Index'
        $this->defaults = $options['defaults'];
        unset($options['defaults']);

        // route options
        $options = (!is_array($options)) ? array() : $options;
        if (isset($options['cryption'])) {
            // set crypto from router config
            $cryption = $options['cryption'];
            $cryption = (is_string($cryption))
                ? (class_exists($cryption)) ? new $cryption() : $cryption
                : $cryption;

            $this->setCryption($cryption);
            unset($options['cryption']);
        }

        $this->params  = $options;
    }

    /**
     * Create a new route with given options.
     * factory(): defined by RouteInterface interface.
     *
     * @param  array|\Traversable $options
     *
     * @throws \Exception
     * @return Crypto
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new \Exception(__METHOD__ . ' expects an array or Traversable set of options');
        }
        
        if (!isset($options['route']) || empty($options['route'])) {
        	$options['route'] = '/'; 
        }
        
        $route = (string) $options['route'];
        unset($options['route']);

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new static($route, $options);
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    Route::match()
     * @param  Request  $request
     * @param  int|null $pathOffset
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null)
    {
        if (!method_exists($request, 'getQuery') && !method_exists($request, 'getUri')) {
            return false;
        }
        
        // determine that we have on correct route matching
        /** @var $uri \Zend\Uri\Http */
        /** @var $request \Zend\Http\PhpEnvironment\Request */
        $uri  = $request->getUri();
        $path = $uri->getPath();

        if ($pathOffset !== null) {
        	if ($pathOffset >= 0 && strlen($path) >= $pathOffset) {
        		if ($this->route !== substr($path, $pathOffset)) {
                    // we are not in admin child routes
        			return null;
        		}
        	}
        }

        # get route params
        // get request query string (?qD2D32#es...EncODed | ?module=Application&encoded=none)
        $reqUri      = $request->getRequestUri();
        if (($qstack = strpos($reqUri, '?')) === false) {
            // we dont have any parameter to match
            return false;
        }

        $queryString = substr($reqUri, $qstack+1);
        $queryString = $this->decodeQuery($queryString);

        $routeParams = array();
        parse_str($queryString, $routeParams);

        /*
         * Route default factory options 
         */
        $params = array_merge($this->defaults, array()/*$this->params*/); // we don't want options as default values
        $params = array_merge($params, $routeParams);

       	return new RouteMatch($params, strlen($this->route));
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
    	$this->assembledParams = $params;

        array_walk($params, function(&$val, $key){
            $val = \Poirot\Core\sanitize_camelcase($val);
        });

    	$query = http_build_query($params);

        return $this->route.'?'.$this->encodeQuery($query);
    }

    /**
     * Get Encrypt Decrypt Object
     *
     * @return CryptionInterface|Crypto\CryptionBase64
     */
    public function getCryption()
    {
        if (!$this->cryptionObject) {
            $this->cryptionObject = new Crypto\CryptionBase64();
        }

        return $this->cryptionObject;
    }

    /**
     * Set Encrypt Decrypt Object
     *
     * @param CryptionInterface $crypt
     *
     * @return $this
     */
    public function setCryption(CryptionInterface $crypt)
    {
        $this->cryptionObject = $crypt;

        return $this;
    }

    /**
     * Decode Query
     * - to match uri
     *
     * @param $query
     *
     * @return string
     */
    protected function decodeQuery($query)
    {
        $decoded = $this->getCryption()->decode($query);

    	return $decoded;
    }

    /**
     * Encode Query
     * - to assemble uri
     *
     * @param $query
     *
     * @return string
     */
    protected function encodeQuery($query)
    {
        $encoded = $this->getCryption()->encode($query);

    	return $encoded;
    }

    /**
     * Get a list of parameters used while assembling.
     *
     * @return array
     */
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
}
