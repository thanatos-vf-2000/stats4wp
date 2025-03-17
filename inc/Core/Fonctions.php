<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.15
 */

/**
 * @param $string
 *
 * @return string
 */
function stats4wp_filter_string_polyfill( string $string ): string
{
    $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
    return str_replace(array( "'", '"' ), array( '&#39;', '&#34;' ), $str);
}
