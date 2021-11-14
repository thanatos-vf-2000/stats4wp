<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
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
    public static function getARGValue($key, $default, $valid = NULL) {
        return (!empty($_GET[$key]) && ($valid == NULL || $valid($_GET[$key]))) ? $_GET[$key] : $default;
    }
}