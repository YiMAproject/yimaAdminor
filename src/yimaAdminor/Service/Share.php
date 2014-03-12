<?php
namespace yimaAdminor\Service;

/**
 * Class Share
 *
 * @package yimaAdminor\Service
 */
class Share extends Parental
{
    /**
     * Is Route Match On Admin?
     *
     * @return bool
     */
    public static function isOnAdmin()
    {
        return self::$isOnAdmin;
    }
}
