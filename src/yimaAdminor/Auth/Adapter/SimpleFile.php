<?php
namespace yimaAdminor\Auth\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result;

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
        $iden = $this->getIdentity();
        $cred = $this->getCredential();

        if ($iden == 'naderi.payam@gmail.com' && $cred == '123456') {
            $code = Result::SUCCESS;
        } else {
            $code = Result::FAILURE;
        }

        return new Result($code, $iden);
    }
}
