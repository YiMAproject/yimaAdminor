<?php
namespace yimaAdminor\Mvc\Router\Http\Crypto;

/**
 * Class CryptionDebuging
 *
 * @package yimaAdminor\Mvc\Router\Http\Crypto
 */
class CryptionDebuging implements CryptionInterface
{
    /**
     * Encode query
     *
     * @param string $query String to encode
     *
     * @return string
     */
    public function encode($query)
    {
        return $query;
    }

    /**
     * Decode encoded query
     *
     * @param string $query Encoded string to decode
     *
     * @return mixed
     */
    public function decode($query)
    {
        return $query;
    }
}
