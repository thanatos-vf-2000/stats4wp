<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.5
 */
namespace STATS4WP\Stats;

use STATS4WP\Api\TimeZone;
use STATS4WP\Api\IP;
use STATS4WP\Api\UserAgent;
use STATS4WP\Api\Referred;
use STATS4WP\Api\GeoIP;
use STATS4WP\Api\User;

use STATS4WP\Core\DB;
use STATS4WP\Core\Options;

class UserOnline {

	/**
	 * Default User Reset Time User Online
	 *
	 * @var int
	 */
	public static $reset_user_time = 120; // Second


	public function register() {
		if ( Options::get_option( 'version' ) === STATS4WP_VERSION ) {
			// Reset User Online Count
			add_action( 'wp_loaded', array( $this, 'reset_user_online' ) );
			add_action( 'init', array( $this, 'useronline' ) );
		}
	}

	/**
	 * Reset Online User Process By Option time
	 *
	 * @return string
	 */
	public function reset_user_online() {
		global $wpdb;

		// Get Not timestamp
		$now = TimeZone::get_current_timestamp();

		// Get the user set value for seconds to check for users online.
		if ( Options::get_option( 'check_online' ) ) {
			$reset_time = Options::get_option( 'check_online' );
		} else {
			// Set the default seconds a user needs to visit the site before they are considered offline.
			$reset_time = self::$reset_user_time;
		}

		// We want to delete users that are over the number of seconds set by the admin.
		$time_diff = $now - $reset_time;

		if ( ! isset( $wpdb->stats4wp_useronline ) ) {
			$wpdb->stats4wp_useronline = DB::table( 'useronline' );}
		// Call the deletion query.
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->stats4wp_useronline WHERE timestamp < %d", $time_diff ) );
	}

	/**
	 * Record Users Online
	 *
	 * @param  array $args
	 * @throws \Exception
	 */
	public static function useronline() {
		// Get User IP
		$user_ip = IP::store_ip();

		// Check Current Use Exist online list
		$user_online = self::is_ip_online( $user_ip );

		// Check Users Exist in Online list
		if ( false === $user_online ) {
			// Added New Online User
			self::add_user_online();
		} else {
			// Update current User Time
			self::update_user_online();
		}
	}

	/**
	 * Check IP is online
	 *
	 * @param  bool $user_ip
	 * @return bool
	 */
	public static function is_ip_online( $user_ip = false ) {
		global $wpdb;
		if ( ! isset( $wpdb->stats4wp_useronline ) ) {
			$wpdb->stats4wp_useronline = DB::table( 'useronline' );}
		$user_online = $wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->stats4wp_useronline WHERE `ip` = %s", $user_ip ) );
		return ( ! $user_online ? false : $user_online );
	}


	/**
	 * Add User Online to Database
	 *
	 * @param  array $args
	 * @throws \Exception
	 */
	public static function add_user_online( $args = array() ) {
		global $wpdb;

		// Get Current Page
		// $current_page = Page::get_page_type();
		$current_page = self::get_page_info();

		// Get User Agent
		$user_agent = UserAgent::get_user_agent();

		// Prepare User online Data
		$user_online = array(
			'ip'        => IP::store_ip(),
			'timestamp' => TimeZone::get_current_timestamp(),
			'created'   => TimeZone::get_current_timestamp(),
			'date'      => TimeZone::get_current_date(),
			'referred'  => Referred::get(),
			'agent'     => $user_agent['browser'],
			'platform'  => $user_agent['platform'],
			'version'   => $user_agent['b-version'],
			'location'  => GeoIP::get_country( IP::get_ip() ),
			'user_id'   => User::get_user_id(),
			'page_id'   => $current_page['id'],
			'type'      => $current_page['type'],
		);
		$user_online = apply_filters( 'stats4wp_user_online_information', $user_online );

		// Insert the user in to the database.
		$insert = $wpdb->insert(
			DB::table( 'useronline' ),
			$user_online,
			array( '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' )
		);
		if ( ! $insert ) {
			if ( ! empty( $wpdb->last_error ) && WP_DEBUG ) {
				error_log( $wpdb->last_error );
			}
		}

		// Get User Online ID
		$user_online_id = $wpdb->insert_id;
	}

	/**
	 * Update User Online
	 */
	public static function update_user_online() {
		global $wpdb;

		// Get Current Page
		// $current_page = Page::get_page_type();
		$current_page = self::get_page_info();

		// Get Current User ID
		$user_id = User::get_user_id();

		// Prepare User online Update data
		$user_online = array(
			'timestamp' => TimeZone::get_current_timestamp(),
			'date'      => TimeZone::get_current_date(),
			'referred'  => Referred::get(),
			'user_id'   => $user_id,
			'page_id'   => $current_page['id'],
			'type'      => $current_page['type'],
		);
		$user_online = apply_filters( 'stats4wp_update_user_online_data', $user_online );

		// Update the database with the new information.
		$wpdb->update(
			DB::table( 'useronline' ),
			$user_online,
			array( 'ip' => IP::store_ip() ),
			array( '%d', '%s', '%s', '%d', '%d', '%s' ),
			array( '%s' )
		);
	}

	/**
	 * Search Page info
	 */
	public static function get_page_info() {
		global $wpdb;

		$current_page = array(
			'type' => 'unknown',
			'id'   => 0,
		);

		if ( ! isset( $wpdb->stats4wp_pages ) ) {
			$wpdb->stats4wp_pages = DB::table( 'pages' );}
		$day                  = TimeZone::get_current_date( 'Y-m-d' );
		$page_url             = esc_url( wp_parse_url( Page::get_page_uri(), PHP_URL_PATH ) );
		$page_info            = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT page_id, type  
				FROM $wpdb->stats4wp_pages
				WHERE date=%s 
				AND uri=%s",
				array(
					$day,
					$page_url,
				)
			)
		);
		$current_page['type'] = ( isset( $page_info->type ) ) ? $page_info->type : $current_page['type'];
		$current_page['id']   = ( isset( $page_info->page_id ) ) ? $page_info->page_id : $current_page['id'];
		return $current_page;
	}
}
