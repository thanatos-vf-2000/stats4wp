<?php
/**
 * @package STATS4WPPlugin
 * @version 1.5.0
 */
namespace STATS4WP\Core;

use STATS4WP\Api\UserAgent;

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

	/**
	 * Central place deciding whether the current request must NOT be
	 * counted by stats4wp: known bots/crawlers, excluded user roles, and
	 * (optionally) visitors sending a "Do Not Track" header.
	 *
	 * Used by both Stats\Visitor and Stats\Page so the two tables stay
	 * consistent with each other.
	 *
	 * @return bool
	 */
	public static function should_skip_tracking() {
		return apply_filters(
			'stats4wp_skip_tracking',
			( self::is_excluded_bot() || self::is_excluded_role() || self::is_dnt_visitor() )
		);
	}

	/**
	 * Is the current visitor a known bot/crawler?
	 *
	 * Relies on the device type already detected by the WhichBrowser parser
	 * used to fill the "agent" fields, so no extra parsing cost is added.
	 *
	 * @return bool
	 */
	public static function is_excluded_bot() {
		if ( true !== Options::get_option( 'exclude_bots' ) ) {
			return false;
		}

		$user_agent = UserAgent::get_user_agent();

		return ! empty( $user_agent['is_bot'] );
	}

	/**
	 * Is the current logged-in user part of a role excluded from tracking?
	 *
	 * Configured as a comma-separated list of role slugs, e.g.
	 * "administrator,editor", so site owners/editors can browse their own
	 * site without polluting their own statistics.
	 *
	 * @return bool
	 */
	public static function is_excluded_role() {
		$excluded_roles = Options::get_option( 'exclude_roles' );

		if ( empty( $excluded_roles ) || ! is_user_logged_in() ) {
			return false;
		}

		$excluded_roles = array_filter( array_map( 'trim', explode( ',', $excluded_roles ) ) );

		if ( empty( $excluded_roles ) ) {
			return false;
		}

		$user = wp_get_current_user();

		return (bool) array_intersect( $excluded_roles, (array) $user->roles );
	}

	/**
	 * Should we respect the "Do Not Track" browser header?
	 *
	 * Only skips tracking when the site owner explicitly enabled the
	 * "respect_dnt" option, since DNT is off by default in every browser
	 * that still supports it.
	 *
	 * @return bool
	 */
	public static function is_dnt_visitor() {
		if ( true !== Options::get_option( 'respect_dnt' ) ) {
			return false;
		}

		return isset( $_SERVER['HTTP_DNT'] ) && '1' === $_SERVER['HTTP_DNT'];
	}
}
