<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */
namespace STATS4WP\Core;

class CoreHelper {


	/**
	 * Check is Login Page
	 *
	 * @return bool
	 */
	public static function is_login_page() {

		// Check From global WordPress
		if ( isset( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ), false ) ) {
			return true;
		}

		// Check Native php
		$protocol    = strpos( strtolower( sanitize_text_field( wp_unslash( $_SERVER['SERVER_PROTOCOL'] ) ) ), 'https' ) === false ? 'http' : 'https';
		$host        = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );
		$script      = sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_NAME'] ) );
		$current_url = $protocol . '://' . $host . $script;
		$login_url   = wp_login_url();
		if ( $current_url === $login_url ) {
			return true;
		}

		return false;
	}

	/**
	 * Remove Query String From Url
	 *
	 * @param  $url
	 * @return bool|string
	 */
	public static function remove_query_string_url( $url ) {
		return substr( $url, 0, strrpos( $url, '?' ) );
	}
}
