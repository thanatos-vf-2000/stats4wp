<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 */
namespace STATS4WP\Core;

class Args
{
    /**
     * @param $key
     * @param $default
     * @param null|callable $valid
     *
     * @return mixed
     */
    public static function getARGValue($key, $default, $valid = null)
    {
        return (!empty($_GET[$key]) && ($valid == null || $valid($_GET[$key]))) ? $_GET[$key] : $default;
    }
}
