<?php
namespace yimaAdminor\Mvc\Router\Http;

use Traversable;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Session\Container as SessionContainer;

/**
 * Class Crypto
 *
 * @package yimaAdminor\Mvc\Router\Http
 */
class Crypto implements RouteInterface
{
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

        // default options of route, exp. [controller] => 'Index'
        $this->defaults = $options['defaults'];
        unset($options['defaults']);

        // route options
        $options = (!is_array($options)) ? array() : $options;
        $this->params  = $options;
    }

    /**
     * Create a new route with given options.
     *
     * @param  array|\Traversable $options
     * @return void
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
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
            return null;
        }
        
        // First: determine that we have on correct route matching 
        $uri  = $request->getUri();
        $path = $uri->getPath();
        
        if ($pathOffset !== null) {
        	if ($pathOffset >= 0 && strlen($path) >= $pathOffset) {
        		if ($this->route !== substr($path,$pathOffset)) {
        			return null;
        		}
        	}
        }
        
        # get encoded query
        $encodQuery = $request->getQuery()->toArray();
        
        // decode query
        $queries = array();
        list($encodQuery) = array_keys($encodQuery);
        
        /* TODO: decodeQuery() bar rooie string haaii ke dast kaari shode ast meghdaar e naa moshakhas bar
         * 		 migardaanad ke ghaabele tashkhis nist ke hengaame parse_str mojeb mishavad $queries meghdaar e
         *       khaali va pas az aan be maraateb parameter haa ham meghdaar e khaali begirand
         */
        $query  = $this->decodeQuery($encodQuery);
        parse_str($query, $queries);
        
        /*
         * Route default factory options 
         */
        $params = array_merge($this->defaults, $this->params);
        $params = array_merge($params, $queries);

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

    	return urldecode($decoded);
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

    	return urlencode($encoded);
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
