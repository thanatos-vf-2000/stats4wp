<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */
namespace STATS4WP\Api;

use STATS4WP\Core\Options;

class UserAgent {



	/**
	 * Get User Agent
	 *
	 * @return mixed
	 */
	public static function get_http_user_agent() {
		return apply_filters( 'stats4wp_user_http_agent', ( isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '' ) );
	}

	/**
	 * Calls the user agent parsing code.
	 *
	 * @return array|\string[]
	 */
	public static function get_user_agent() {

		// Get Http User Agent
		$user_agent = self::get_http_user_agent();

		// Get WhichBrowser Browser
		$result = new \WhichBrowser\Parser( $user_agent );
		$agent  = array(
			'browser'             => ( isset( $result->browser->name ) ) ? $result->browser->name : _x( 'Unknown', 'Browser', 'stats4wp' ),
			'b-version'           => ( isset( $result->browser->version->value ) ) ? $result->browser->version->value : _x( 'Unknown', 'Version', 'stats4wp' ),
			'platform'            => ( isset( $result->os->name ) ) ? $result->os->name : _x( 'Unknown', 'Platform', 'stats4wp' ),
			'p-version'           => ( isset( $result->os->version->value ) ) ? $result->os->version->value : _x( 'Unknown', 'Version', 'stats4wp' ),
			'engine'              => ( isset( $result->engine->name ) ) ? $result->engine->name : _x( 'Unknown', 'Platform', 'stats4wp' ),
			'e-version'           => ( isset( $result->engine->version->value ) ) ? $result->engine->version->value : _x( 'Unknown', 'Version', 'stats4wp' ),
			'device'              => ( isset( $result->device->type ) ) ? $result->device->type : _x( 'Unknown', 'Device', 'stats4wp' ),
			'device-manufacturer' => ( isset( $result->device->manufacturer ) ) ? $result->device->manufacturer : _x( 'Unknown', 'Device', 'stats4wp' ),
			'device-model'        => ( isset( $result->device->model ) ) ? $result->device->model : _x( 'Unknown', 'Device', 'stats4wp' ),
		);

		return apply_filters( 'stats4wp_user_agent', $agent );
	}
}
