<?php
namespace cAdmin\Mvc\Router\Http;

use Traversable;
use Zend\Mvc\Router\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Session\Container as SessionContainer;

class Crypto implements RouteInterface
{
	/**
	 * Default session namespace
	 */
	const SESSION_NAMESPACE = 'cAdmin_Route_Crypto';
	
	/**
	 * Default session object member name
	 */
	const SESSION_SECURITY_KEY = 'security_key';
	
	/**
	 * RouteInterface to match.
	 *
	 * @var string
	 */
	protected $route;
	
	
	protected $params;
	
    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;
    
    /**
     * Object to proxy $_SESSION storage
     *
     * @var SessionContainer
     */
    protected $session;

    public function __construct($route, array $options = array())
    {
    	$this->route    = $route;
    	
        $this->defaults = $options['defaults'];
        unset($options['defaults']);
        
        if (!is_array($options)) {
        	$options = array();
        }
        
        $this->params = $options;
        
        $this->session   = new SessionContainer(self::SESSION_NAMESPACE);
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    Route::factory()
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     * @return Literal
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
        
        
        // Last: check from queries to match
        
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
        parse_str($query,$queries);
        
        /*
         * Route default factory options 
         */
        $params = array_merge($this->defaults,$this->params);
        $params = array_merge($params,$queries);
        
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
     * getAssembledParams(): defined by RouteInterface interface.
     *
     * @see    Route::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
    
    protected function decodeQuery($query)
    {
    	$key = $this->getSecurityKey();
    	\Datasecurity_Ancryptst::setKey($key);
    
    	return urldecode(\Datasecurity_Ancryptst::decrypt($query));
    }
    
    protected function encodeQuery($query)
    {
    	$key = $this->getSecurityKey();
    	\Datasecurity_Ancryptst::setKey($key);
    
    	return urlencode(\Datasecurity_Ancryptst::encrypt($query));
    }
    
    /**
     * security key raaa baraaaie encode va decode kardan e Query bar migardaanad
     *
     */
    protected function getSecurityKey()
    {
    	if (!isset($this->session->{self::SESSION_SECURITY_KEY})) {
    		$key = uniqid();
    		$this->session->{self::SESSION_SECURITY_KEY} = $key;
    	} 
    	
    	return $this->session->{self::SESSION_SECURITY_KEY};
    }
}
