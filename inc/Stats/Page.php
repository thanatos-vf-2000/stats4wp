<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.9
 */
namespace STATS4WP\Stats;

use STATS4WP\Api\TimeZone;

use STATS4WP\Core\CoreHelper;
use STATS4WP\Core\DB;
use STATS4WP\Core\Options;
use STATS4WP\Core\Fonctions;

class Page {

	public function register() {
		if ( Options::get_option( 'version' ) === STATS4WP_VERSION ) {
			add_action( 'init', array( $this, 'page' ) );
		}
	}

	/**
	 * Get WordPress Page Type
	 */
	public static function get_page_type() {

		/**
		 * Set Default Option
		 */
		$current_page = array(
			'type' => 'unknown',
			'id'   => 0,
		);

		/**
		 * Check Query object
		 */
		$id = get_queried_object_id();
		if ( is_numeric( $id ) && $id > 0 ) {
			$current_page['id'] = $id;
		}

		/**
		 * WooCommerce Product
		 */
		if ( class_exists( 'WooCommerce' ) ) {
			if ( is_product() ) {
				return wp_parse_args( array( 'type' => 'product' ), $current_page );
			}
		}

		/**
		 * Home Page or Front Page
		 */
		if ( is_front_page() || is_home() ) {
			return wp_parse_args( array( 'type' => 'home' ), $current_page );
		}

		/**
		 * Attachment View
		 */
		if ( is_attachment() ) {
			$current_page['type'] = 'attachment';
		}

		/**
		 * Is Archive Page
		 */
		if ( is_archive() ) {
			$current_page['type'] = 'archive';
		}

		/**
		 * Single Post Fro All Post Type
		 */
		if ( is_singular() ) {
			$current_page['type'] = 'post';
		}

		/**
		 * Single Page
		 */
		if ( is_page() ) {
			$current_page['type'] = 'page';
		}

		/**
		 * Category Page
		 */
		if ( is_category() ) {
			$current_page['type'] = 'category';
		}

		/**
		 * Tag Page
		 */
		if ( is_tag() ) {
			$current_page['type'] = 'post_tag';
		}

		/**
		 * Is Custom Term From Taxonomy
		 */
		if ( is_tax() ) {
			$current_page['type'] = 'tax';
		}

		/**
		 * Is Author Page
		 */
		if ( is_author() ) {
			$current_page['type'] = 'author';
		}

		/**
		 * Is search page
		 */
		$search_query = filter_var( get_search_query( false ), FILTER_CALLBACK, array( 'options' => 'filter_string_polyfill' ) );
		if ( trim( $search_query ) !== '' ) {
			return array(
				'type'         => 'search',
				'id'           => 0,
				'search_query' => $search_query,
			);
		}

		/**
		 * Is 404 Page
		 */
		if ( is_404() ) {
			$current_page['type'] = '404';
		}

		/**
		 * Add WordPress Feed
		 */
		if ( is_feed() ) {
			$current_page['type'] = 'feed';
		}

		/**
		 * Add WordPress Login Page
		 */
		if ( CoreHelper::is_login_page() ) {
			$current_page['type'] = 'loginpage';
		}

		/**
		 * Add admin URL
		 */
		if ( strpos( self::get_page_uri(), wp_parse_url( admin_url(), PHP_URL_PATH ) ) !== false ) {
			$current_page['type'] = 'admin';
		}

		if ( 'unknown' === $current_page['type'] ) {
			$current_page['type'] = get_post_type( get_the_ID() );
			if ( false !== $current_page['type'] ) {
				$current_page['id'] = get_the_ID();
				return apply_filters( 'stats4wp_current_page', $current_page );
			} else {
				$current_page['type'] = 'unknown';
			}
		}

		if ( '/' === self::get_page_uri() ) {
			return wp_parse_args( array( 'type' => 'home' ), $current_page );
		}

		if ( 'unknown' === $current_page['type'] ) {
			$local_post = get_page_by_path( self::get_page_uri() );
			if ( is_object( $local_post ) ) {
				$current_page['type'] = $local_post->post_type;
				$current_page['id']   = $local_post->ID;
				return apply_filters( 'stats4wp_current_page', $current_page );
			} elseif ( is_array( $local_post ) ) {
				$current_page['type'] = $local_post['post_type'];
				$current_page['id']   = $local_post['ID'];
				return apply_filters( 'stats4wp_current_page', $current_page );
			}
		}

		if ( '' === $current_page['type'] ) {
			$current_page['type'] = 'unknown';
			$current_page['id']   = 0;
		}

		return apply_filters( 'stats4wp_current_page', $current_page );
	}

	/**
	 * Get Page Url
	 *
	 * @return bool|mixed|string
	 */
	public static function get_page_uri() {

		/**
		 * Get the site's path from the URL.
		 */
		$site_uri = wp_parse_url( site_url(), PHP_URL_PATH );
		if ( isset( $site_uri ) ) {
			$site_uri_len = strlen( $site_uri );
		} else {
			$site_uri_len = 0;}

		/**
		 * Get the site's path from the URL.
		 */
		$home_uri = wp_parse_url( home_url(), PHP_URL_PATH );
		if ( isset( $home_uri ) ) {
			$home_uri_len = strlen( $home_uri );
		} else {
			$home_uri_len = 0;}

		/**
		 * Get the current page URI.
		 */
		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$page_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		} else {
			$page_uri = '';
		}

		/*
		 * We need to check which URI is longer in case one contains the other.
		 * For example home_uri might be "/site/wp" and site_uri might be "/site".
		 * In that case we want to check to see if the page_uri starts with "/site/wp" before
		 * we check for "/site", but in the reverse case, we need to swap the order of the check.
		 */
		if ( $site_uri_len > $home_uri_len ) {
			if ( substr( $page_uri, 0, $site_uri_len ) === $site_uri ) {
				$page_uri = substr( $page_uri, $site_uri_len );
			}

			if ( substr( $page_uri, 0, $home_uri_len ) === $home_uri ) {
				$page_uri = substr( $page_uri, $home_uri_len );
			}
		} else {
			if ( substr( $page_uri, 0, $home_uri_len ) === $home_uri ) {
				$page_uri = substr( $page_uri, $home_uri_len );
			}

			if ( substr( $page_uri, 0, $site_uri_len ) === $site_uri ) {
				$page_uri = substr( $page_uri, $site_uri_len );
			}
		}

		/**
		 * Sanitize Xss injection
		 */
		$page_uri = filter_var( $page_uri, FILTER_CALLBACK, array( 'options' => 'filter_string_polyfill' ) );

		/**
		 * If we're at the root (aka the URI is blank), let's make sure to indicate it.
		 */
		if ( '' === $page_uri ) {
			$page_uri = '/';
		}

		if ( function_exists( 'pll_current_language' ) ) {
			$page_uri = str_replace( '/' . pll_current_language() . '/', '/', $page_uri );
		}

		return apply_filters( 'stats4wp_page_uri', $page_uri );
	}

	/**
	 * Sanitize Page Url For Push to Database
	 */
	public static function sanitize_page_uri() {

		/**
		 * Get Current WordPress Page
		 */
		$current_page = self::get_page_type();

		/**
		 * Get the current page URI.
		 */
		$page_uri = self::get_page_uri();

		/**
		 * Get String Search WordPress
		 */
		if ( array_key_exists( 'search_query', $current_page ) && ! empty( $current_page['search_query'] ) ) {
			$page_uri = '?s=' . $current_page['search_query'];
		}

		/**
		 * Sanitize for WordPress Login Page
		 */
		if ( 'loginpage' === $current_page['type'] ) {
			$page_uri = CoreHelper::remove_query_string_url( $page_uri );
		}

		/**
		 * Check Strip Url Parameter
		 */
		if ( array_key_exists( 'search_query', $current_page ) === false ) {
			$temp = explode( '?', $page_uri );
			if ( false !== $temp ) {
				$page_uri = $temp[0];
			}
		}

		/**
		 * Limit the URI length to 255 characters, otherwise we may overrun the SQL field size.
		 */
		return substr( $page_uri, 0, 255 );
	}

	public function page() {
		global $wpdb;

		/**
		 *  Get Current WordPress Page
		 */
		$current_page = self::get_page_type();

		/**
		 *  Check if admin page and page stats disable
		 */
		if ( strpos( self::get_page_uri(), wp_parse_url( admin_url(), PHP_URL_PATH ) ) !== false && Options::get_option( 'disableadminstat' ) === true ) {
			return false;
		}

		if ( ! isset( $wpdb->stats4wp_pages ) ) {
			$wpdb->stats4wp_pages = DB::table( 'pages' );}
		/**
		 * Get Page uri
		 */
		$page_uri = self::sanitize_page_uri();

		/**
		 *  Check if we have already been to this page today.
		 */
		$day       = TimeZone::get_current_date( 'Y-m-d' );
		$page_type = $current_page['type'];
		$page_id   = $current_page['id'];
		if ( array_key_exists( 'search_query', $current_page ) === true ) {
			$uri   = esc_sql( $page_uri );
			$exist = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT page_id 
					FROM $wpdb->stats4wp_pages 
					WHERE `date` = %s  
						AND uri = %s 
						AND `type` = %s 
						AND id = %d",
					array(
						$day,
						$page_type,
						$uri,
						$page_id,
					)
				),
				ARRAY_A
			);
		} else {
			$exist = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT page_id 
					FROM $wpdb->stats4wp_pages
					WHERE `date` = %s 
						AND `type` = %s 
						AND id = %d",
					array(
						$day,
						$page_type,
						$page_id,
					)
				),
				ARRAY_A
			);
		}

		/**
		 *  Update Exist Page
		 */
		if ( null !== $exist ) {
			$query_cond = ( array_key_exists( 'search_query', $current_page ) === true ? " AND `uri` = '" . esc_sql( $page_uri ) . "'" : '' );
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE $wpdb->stats4wp_pages 
				SET `count` = `count` + 1 
				WHERE `date` = %s %s 
					AND `type` = %s 
					AND `id` = %d",
					array(
						$day,
						$query_cond,
						$page_type,
						$page_id,
					)
				)
			);
			$page_id = $exist['page_id'];
		} else {
			/**
			 *  Prepare Pages Data
			 */
			$pages = array(
				'uri'   => $page_uri,
				'date'  => TimeZone::get_current_date( 'Y-m-d' ),
				'count' => 1,
				'id'    => $current_page['id'],
				'type'  => $current_page['type'],
			);
			$pages = apply_filters( 'stats4wp_pages_information', $pages );

			/**
			 * Added to DB
			 */
			$page_id = self::save_page( $pages );
		}

		return ( isset( $page_id ) ? $page_id : false );
	}

	/**
	 * Add new row to Pages Table
	 *
	 * @param  array $page
	 * @return int
	 */
	public static function save_page( $page = array() ) {
		global $wpdb;

		/**
		 * Save to WordPress Database
		 */
		$insert = $wpdb->insert(
			DB::table( 'pages' ),
			$page
		);
		if ( ! $insert ) {
			if ( ! empty( $wpdb->last_error ) && defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				// phpcs:disable WordPress.PHP.DevelopmentFunctions
				error_log( $wpdb->last_error );
				// phpcs:enable
			}
		}

		/**
		 * Get Page ID
		 */
		$page_id = $wpdb->insert_id;

		return $page_id;
	}
}
