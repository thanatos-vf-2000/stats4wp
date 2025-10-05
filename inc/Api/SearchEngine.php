<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */
namespace STATS4WP\Api;

class SearchEngine {



	/**
	 * Default error not founding search engine
	 *
	 * @var string
	 */
	public static $error_found = 'No search query found!';

	/**
	 * Get List Of Search engine in WP-Statistics
	 *
	 * @param  bool $all
	 * @return array
	 */
	public static function get_list( $all = false ) {

		// List OF Search engine
		$default = $engines = array(
			'ask'        => array(
				'name'         => 'Ask.com',
				'translated'   => __( 'Ask.com', 'stats4wp' ),
				'tag'          => 'ask',
				'sqlpattern'   => '%ask.com%',
				'regexpattern' => 'ask\.com',
				'querykey'     => 'q',
				'image'        => 'ask.png',
				'logo_url'     => self::asset() . 'ask.png',
			),
			'baidu'      => array(
				'name'         => 'Baidu',
				'translated'   => __( 'Baidu', 'stats4wp' ),
				'tag'          => 'baidu',
				'sqlpattern'   => '%baidu.com%',
				'regexpattern' => 'baidu\.com',
				'querykey'     => 'wd',
				'image'        => 'baidu.png',
				'logo_url'     => self::asset() . 'baidu.png',
			),
			'bing'       => array(
				'name'         => 'Bing',
				'translated'   => __( 'Bing', 'stats4wp' ),
				'tag'          => 'bing',
				'sqlpattern'   => '%bing.com%',
				'regexpattern' => 'bing\.com',
				'querykey'     => 'q',
				'image'        => 'bing.png',
				'logo_url'     => self::asset() . 'bing.png',
			),
			'clearch'    => array(
				'name'         => 'clearch.org',
				'translated'   => __( 'clearch.org', 'stats4wp' ),
				'tag'          => 'clearch',
				'sqlpattern'   => '%clearch.org%',
				'regexpattern' => 'clearch\.org',
				'querykey'     => 'q',
				'image'        => 'clearch.png',
				'logo_url'     => self::asset() . 'clearch.png',
			),
			'duckduckgo' => array(
				'name'         => 'DuckDuckGo',
				'translated'   => __( 'DuckDuckGo', 'stats4wp' ),
				'tag'          => 'duckduckgo',
				'sqlpattern'   => array( '%duckduckgo.com%', '%ddg.gg%' ),
				'regexpattern' => array( 'duckduckgo\.com', 'ddg\.gg' ),
				'querykey'     => 'q',
				'image'        => 'duckduckgo.png',
				'logo_url'     => self::asset() . 'duckduckgo.png',
			),
			'google'     => array(
				'name'         => 'Google',
				'translated'   => __( 'Google', 'stats4wp' ),
				'tag'          => 'google',
				'sqlpattern'   => '%google.%',
				'regexpattern' => 'google\.',
				'querykey'     => 'q',
				'image'        => 'google.png',
				'logo_url'     => self::asset() . 'google.png',
			),
			'yahoo'      => array(
				'name'         => 'Yahoo!',
				'translated'   => __( 'Yahoo!', 'stats4wp' ),
				'tag'          => 'yahoo',
				'sqlpattern'   => '%yahoo.com%',
				'regexpattern' => 'yahoo\.com',
				'querykey'     => 'p',
				'image'        => 'yahoo.png',
				'logo_url'     => self::asset() . 'yahoo.png',
			),
			'yandex'     => array(
				'name'         => 'Yandex',
				'translated'   => __( 'Yandex', 'stats4wp' ),
				'tag'          => 'yandex',
				'sqlpattern'   => '%yandex.ru%',
				'regexpattern' => 'yandex\.ru',
				'querykey'     => 'text',
				'image'        => 'yandex.png',
				'logo_url'     => self::asset() . 'yandex.png',
			),
			'qwant'      => array(
				'name'         => 'Qwant',
				'translated'   => __( 'Qwant', 'stats4wp' ),
				'tag'          => 'qwant',
				'sqlpattern'   => '%qwant.com%',
				'regexpattern' => 'qwant\.com',
				'querykey'     => 'q',
				'image'        => 'qwant.png',
				'logo_url'     => self::asset() . 'qwant.png',
			),
		);

		if ( false === $all ) {
			// If we've disabled all the search engines, reset the list back to default.
			if ( count( $engines ) === 0 ) {
				$engines = $default;
			}
		}

		return $engines;
	}

	/**
	 * Return Default Value if Search Engine Not Exist
	 *
	 * @return array
	 */
	public static function default_engine() {
		return array(
			'name'         => _x( 'Unknown', 'Search Engine', 'stats4wp' ),
			'tag'          => '',
			'sqlpattern'   => '',
			'regexpattern' => '',
			'querykey'     => 'q',
			'image'        => 'unknown.png',
			'logo_url'     => self::asset() . 'unknown.png',
		);
	}

	/**
	 * Get base assets url search engine logo
	 *
	 * @return string
	 */
	public static function asset() {
		return STATS4WP_URL . 'assets/images/search-engine/';
	}
	/**
	 * Get Search Engine information Bt Url Regex
	 *
	 * @param  bool|false $url
	 * @return array|bool
	 */
	public static function get_by_url( $url = false ) {

		// If no URL was passed in, get the current referrer for the session.
		if ( ! $url ) {
			$url = ! empty( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) ) ? Referred::get() : false;
		}

		// If there is no URL and no referrer, always return false.
		if ( false === $url ) {
			return false;
		}

		// Parse the URL in to it's component parts.
		$parts = wp_parse_url( $url );

		// Get the list of search engines we currently support.
		$search_engines = self::get_list();

		// Loop through the SE list until we find which search engine matches.
		foreach ( $search_engines as $key => $value ) {
			$search_regex = self::regex( $key );
			preg_match( '/' . $search_regex . '/', $parts['host'], $matches );
			if ( isset( $matches[1] ) ) {
				// Return the first matched SE.
				return $value;
			}
		}

		// If no SE matched, return some defaults.
		return self::default_engine();
	}

	/**
	 * Get Search Engine Regex From List
	 *
	 * @param  string $search_engine
	 * @return string
	 */
	public static function regex( $search_engine = 'all' ) {

		// Get a complete list of search engines
		$search_engine_list = self::get_list();
		$search_query       = '';

		// Are we getting results for all search engines or a specific one?
		if ( strtolower( $search_engine ) === 'all' ) {
			foreach ( $search_engine_list as $se ) {
				if ( is_array( $se['regexpattern'] ) ) {
					foreach ( $se['regexpattern'] as $subse ) {
						$search_query .= "{$subse}|";
					}
				} else {
					$search_query .= "{$se['regexpattern']}|";
				}
			}

			// Trim off the last '|' for the loop above.
			$search_query = substr( $search_query, 0, strlen( $search_query ) - 1 );
		} elseif ( is_array( $search_engine_list[ $search_engine ]['regexpattern'] ) ) {
			foreach ( $search_engine_list[ $search_engine ]['regexpattern'] as $se ) {
				$search_query .= "{$se}|";
			}

				// Trim off the last '|' for the loop above.
				$search_query = substr( $search_query, 0, strlen( $search_query ) - 1 );
		} else {
			$search_query .= $search_engine_list[ $search_engine ]['regexpattern'];
		}

		return "({$search_query})";
	}

	/**
	 * Parses a URL from a referrer and return the search query words used.
	 *
	 * @param  bool|false $url
	 * @return bool|string
	 */
	public static function get_by_query_string( $url = false ) {

		// Get Referred Url
		$referred_url = Referred::get_referer_url();

		// If no URL was passed in, get the current referrer for the session.
		if ( ! $url ) {
			$url = ( '' === $referred_url ? false : $referred_url );
		}

		// If there is no URL and no referrer, always return false.
		if ( false === $url ) {
			return false;
		}

		// Parse the URL in to it's component parts.
		$parts = wp_parse_url( $url );

		// Check query exist
		if ( array_key_exists( 'query', $parts ) ) {
			parse_str( $parts['query'], $query );
		} else {
			$query = array();
		}

		// Get the list of search engines we currently support.
		$search_engines = self::get_list();

		// Loop through the SE list until we find which search engine matches.
		foreach ( $search_engines as $key => $value ) {
			$search_regex = self::regex( $key );
			preg_match( '/' . $search_regex . '/', $parts['host'], $matches );
			if ( isset( $matches[1] ) ) {
				if ( array_key_exists( $search_engines[ $key ]['querykey'], $query ) ) {
					$words = wp_strip_all_tags( $query[ $search_engines[ $key ]['querykey'] ] );
				} else {
					$words = '';
				}

				return ( '' === $words ? self::$error_found : $words );
			}
		}

		return self::$error_found;
	}
}
