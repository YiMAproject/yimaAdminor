<?php
namespace yimaAdminor\Mvc\Router\Http\Crypto;

/**
 * Interface CryptionInterface
 *
 * @package yimaAdminor\Mvc\Router\Http\Crypto
 */
interface CryptionInterface
{
    /**
     * Encode query
     *
     * @param string $query String to encode
     *
     * @return string
     */
    public function encode($query);

    /**
     * Decode encoded query
     *
     * @param string $query Encoded string to decode
     *
     * @return mixed
     */
    public function decode($query);
}
