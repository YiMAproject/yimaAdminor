<?php
namespace yimaAdminor\Mvc\Router\Http\Crypto;

/**
 * Class Crypto
 *
 * @package yimaAdminor\Mvc\Router\Http\Crypto
 */
class CryptionBase64 implements CryptionInterface
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
        return base64_encode($query);
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
        return base64_decode($query);
    }
}
