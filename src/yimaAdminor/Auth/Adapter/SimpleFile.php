<?php
namespace yimaAdminor\Auth\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;

/**
 * Class SimpleFile
 *
 * @package yimaAdminor\Auth\Adapter
 */
class SimpleFile extends AbstractAdapter
{
    /**
     * Performs an authentication attempt
     *
     * @return \Zend\Authentication\Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        return false;
    }
}
