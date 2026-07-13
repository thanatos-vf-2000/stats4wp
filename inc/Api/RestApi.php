<?php
/**
 * @package STATS4WPPlugin
 * @version 1.6.0
 *
 * Description: Read-only REST API exposing stats4wp data to external tools
 * (dashboards, scripts, third-party integrations...).
 *
 * All routes live under /wp-json/stats4wp/v1/ and are disabled by default
 * (see the "rest_api_enabled" option). When enabled, requests must either
 * come from a logged-in user with `manage_options`, or provide the API key
 * generated for the site via the `X-STATS4WP-API-KEY` header or an
 * `api_key` query parameter.
 * 
 * /wp-json/stats4wp/v1/summary			: visiteurs + visites sur une période
 * /wp-json/stats4wp/v1/pages/top		: pages les plus vues
 * /wp-json/stats4wp/v1/countries/top	: répartitions
 * /wp-json/stats4wp/v1/browsers/top	: répartitions
 * /wp-json/stats4wp/v1/online			: visiteurs actuellement en ligne
 */
namespace STATS4WP\Api;

use STATS4WP\Core\DB;
use STATS4WP\Core\Options;

class RestApi {




	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	const NAMESPACE_NAME = 'stats4wp/v1';

	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Get the site's stats4wp API key, generating one on the fly if it does
	 * not exist yet (e.g. sites upgrading from an older version).
	 *
	 * @return string
	 */
	public static function get_or_create_api_key() {
		$key = Options::get_option( 'rest_api_key' );

		if ( empty( $key ) ) {
			$key = wp_generate_password( 32, false, false );
			Options::set_option( 'rest_api_key', $key );
		}

		return $key;
	}

	/**
	 * Regenerate (invalidate) the current API key.
	 *
	 * @return string The new key.
	 */
	public static function regenerate_api_key() {
		$key = wp_generate_password( 32, false, false );
		Options::set_option( 'rest_api_key', $key );

		return $key;
	}

	public function register_routes() {
		$date_range_args = array(
			'from'         => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'to'           => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'include_bots' => array(
				'type'              => 'boolean',
				'default'           => false,
			),
		);

		register_rest_route(
			self::NAMESPACE_NAME,
			'/summary',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_summary' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => $date_range_args,
			)
		);

		register_rest_route(
			self::NAMESPACE_NAME,
			'/pages/top',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_top_pages' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => array_merge(
					$date_range_args,
					array(
						'limit' => array(
							'type'              => 'integer',
							'sanitize_callback' => 'absint',
						),
					)
				),
			)
		);

		register_rest_route(
			self::NAMESPACE_NAME,
			'/countries/top',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_top_countries' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => array_merge(
					$date_range_args,
					array(
						'limit' => array(
							'type'              => 'integer',
							'sanitize_callback' => 'absint',
						),
					)
				),
			)
		);

		register_rest_route(
			self::NAMESPACE_NAME,
			'/browsers/top',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_top_browsers' ),
				'permission_callback' => array( $this, 'check_permission' ),
				'args'                => array_merge(
					$date_range_args,
					array(
						'limit' => array(
							'type'              => 'integer',
							'sanitize_callback' => 'absint',
						),
					)
				),
			)
		);

		register_rest_route(
			self::NAMESPACE_NAME,
			'/online',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_online' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);
	}

	/**
	 * Shared permission check for every stats4wp REST route.
	 *
	 * @param  \WP_REST_Request $request
	 * @return true|\WP_Error
	 */
	public function check_permission( $request ) {
		if ( true !== Options::get_option( 'rest_api_enabled' ) ) {
			return new \WP_Error(
				'stats4wp_rest_disabled',
				stats4wp_t( 'The stats4wp REST API is disabled for this site.', 'stats4wp' ),
				array( 'status' => 403 )
			);
		}

		// A logged-in administrator browsing the API (e.g. from the REST
		// API doc, or the WP admin) does not need the API key.
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$provided_key = $request->get_header( 'x-stats4wp-api-key' );
		if ( empty( $provided_key ) ) {
			$provided_key = $request->get_param( 'api_key' );
		}

		$stored_key = self::get_or_create_api_key();

		if ( empty( $provided_key ) || empty( $stored_key ) || ! hash_equals( $stored_key, (string) $provided_key ) ) {
			return new \WP_Error(
				'stats4wp_rest_forbidden',
				stats4wp_t( 'Invalid or missing API key. Pass it via the X-STATS4WP-API-KEY header or the api_key query parameter.', 'stats4wp' ),
				array( 'status' => 401 )
			);
		}

		return true;
	}

	/**
	 * Resolve and validate the from/to date range for a request, defaulting
	 * to the last 30 days when missing or malformed.
	 *
	 * @param  \WP_REST_Request $request
	 * @return array{0:string,1:string}
	 */
	private function get_date_range( $request ) {
		$is_valid_date = static function ( $value ) {
			return is_string( $value ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value );
		};

		$to   = $request->get_param( 'to' );
		$from = $request->get_param( 'from' );

		$to   = $is_valid_date( $to ) ? $to : gmdate( 'Y-m-d' );
		$from = $is_valid_date( $from ) ? $from : gmdate( 'Y-m-d', strtotime( '-30 days' ) );

		return array( $from, $to );
	}

	/**
	 * Build the "exclude bots/local visitors" SQL fragment, unless the
	 * caller explicitly asked to include them.
	 *
	 * @param  \WP_REST_Request $request
	 * @return string
	 */
	private function get_bot_filter_sql( $request ) {
		if ( $request->get_param( 'include_bots' ) ) {
			return '';
		}

		return " AND device != 'bot' AND location != 'local' ";
	}

	/**
	 * Clamp a requested "limit" parameter between 1 and 100.
	 *
	 * @param  \WP_REST_Request $request
	 * @return int
	 */
	private function get_limit( $request ) {
		$limit = $request->get_param( 'limit' );
		if ( empty( $limit ) ) {
			$limit = (int) Options::get_option( 'top_page' );
		}

		return max( 1, min( 100, absint( $limit ) ) );
	}

	/**
	 * GET /summary
	 */
	public function get_summary( $request ) {
		global $wpdb;

		list( $from, $to ) = $this->get_date_range( $request );
		$bot_filter         = $this->get_bot_filter_sql( $request );

		$wpdb->stats4wp_visitor = DB::table( 'visitor' );

		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT COUNT(ID) as visitors, COALESCE(SUM(hits),0) as visits
				FROM $wpdb->stats4wp_visitor
				WHERE last_counter BETWEEN %s AND %s $bot_filter", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$from,
				$to
			)
		);

		return rest_ensure_response(
			array(
				'from'     => $from,
				'to'       => $to,
				'visitors' => (int) $row->visitors,
				'visits'   => (int) $row->visits,
			)
		);
	}

	/**
	 * GET /pages/top
	 */
	public function get_top_pages( $request ) {
		global $wpdb;

		list( $from, $to ) = $this->get_date_range( $request );
		$limit              = $this->get_limit( $request );

		$wpdb->stats4wp_pages = DB::table( 'pages' );

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT uri, type, id, SUM(count) as hits
				FROM $wpdb->stats4wp_pages
				WHERE type != 'unknown'
					AND date BETWEEN %s AND %s
				GROUP BY uri, type, id
				ORDER BY hits DESC
				LIMIT %d",
				$from,
				$to,
				$limit
			)
		);

		$pages = array_map(
			static function ( $row ) {
				return array(
					'uri'  => $row->uri,
					'type' => $row->type,
					'id'   => (int) $row->id,
					'hits' => (int) $row->hits,
				);
			},
			$rows
		);

		return rest_ensure_response(
			array(
				'from'  => $from,
				'to'    => $to,
				'pages' => $pages,
			)
		);
	}

	/**
	 * GET /countries/top
	 */
	public function get_top_countries( $request ) {
		global $wpdb;

		list( $from, $to ) = $this->get_date_range( $request );
		$limit              = $this->get_limit( $request );
		$bot_filter         = $this->get_bot_filter_sql( $request );

		$wpdb->stats4wp_visitor = DB::table( 'visitor' );

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT location, COUNT(ID) as nb
				FROM $wpdb->stats4wp_visitor
				WHERE last_counter BETWEEN %s AND %s $bot_filter
				GROUP BY location
				ORDER BY nb DESC
				LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$from,
				$to,
				$limit
			)
		);

		$countries = array_map(
			static function ( $row ) {
				return array(
					'location' => $row->location,
					'visitors' => (int) $row->nb,
				);
			},
			$rows
		);

		return rest_ensure_response(
			array(
				'from'      => $from,
				'to'        => $to,
				'countries' => $countries,
			)
		);
	}

	/**
	 * GET /browsers/top
	 */
	public function get_top_browsers( $request ) {
		global $wpdb;

		list( $from, $to ) = $this->get_date_range( $request );
		$limit              = $this->get_limit( $request );
		$bot_filter         = $this->get_bot_filter_sql( $request );

		$wpdb->stats4wp_visitor = DB::table( 'visitor' );

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT agent, COUNT(ID) as nb
				FROM $wpdb->stats4wp_visitor
				WHERE last_counter BETWEEN %s AND %s $bot_filter
				GROUP BY agent
				ORDER BY nb DESC
				LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$from,
				$to,
				$limit
			)
		);

		$browsers = array_map(
			static function ( $row ) {
				return array(
					'browser'  => $row->agent,
					'visitors' => (int) $row->nb,
				);
			},
			$rows
		);

		return rest_ensure_response(
			array(
				'from'     => $from,
				'to'       => $to,
				'browsers' => $browsers,
			)
		);
	}

	/**
	 * GET /online
	 */
	public function get_online( $request ) {
		global $wpdb;

		$wpdb->stats4wp_useronline = DB::table( 'useronline' );

		$row = $wpdb->get_row( "SELECT COUNT(ID) as online FROM $wpdb->stats4wp_useronline" );

		return rest_ensure_response(
			array(
				'online' => (int) $row->online,
			)
		);
	}
}
