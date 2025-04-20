<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */
namespace STATS4WP\Core;

class Args {


	/**
	 * @param $key
	 * @param $default
	 * @param null|callable $valid
	 *
	 * @return mixed
	 */
	public static function get_arg_value( $key, $default, $valid = null ) {
		return ( ! empty( $_GET[ $key ] ) && ( null === $valid || $valid( sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : $default;
	}
}
