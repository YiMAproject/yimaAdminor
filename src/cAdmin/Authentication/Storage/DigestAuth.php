<?php
namespace cAdmin\Authentication\Storage;

use Zend\Authentication\Storage\StorageInterface;

class DigestAuth implements StorageInterface
{
    public function isEmpty()
    {
        return isset($_SERVER['PHP_AUTH_DIGEST']);
    }

    /**
     * Returns the contents of storage
     * Behavior is undefined when storage is empty.
     *
     * @return mixed
     */
    public function read()
    {
    	return $this->httpDigestParse($_SERVER['PHP_AUTH_DIGEST']);
    }

    public function write($contents)
    {
    }

    /**
     * Clears contents from storage
     *
     * @return void
     */
    public function clear()
    {
        $this->data = null;
    }
    
    protected function httpDigestParse($txt)
    {
    	// protect against missing data
    	$needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    	$data = array();
    	$keys = implode('|', array_keys($needed_parts));
    
    	preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);
    
    	foreach ($matches as $m) {
    		$data[$m[1]] = $m[3] ? $m[3] : $m[4];
    		unset($needed_parts[$m[1]]);
    	}
    
    	return $needed_parts ? false : $data;
    }
}
