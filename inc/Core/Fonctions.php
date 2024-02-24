<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.9
 */

/**
 * @param $string
 *
 * @return string
 */
function filter_string_polyfill( string $string ): string {
	$str = preg_replace( '/\x00|<[^>]*>?/', '', $string );
	return str_replace( array( "'", '"' ), array( '&#39;', '&#34;' ), $str );
}
