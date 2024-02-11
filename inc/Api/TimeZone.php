<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.5
 */
namespace STATS4WP\Api;

class TimeZone {

	/**
	 * Get Current timeStamp
	 *
	 * @return bool|string
	 */
	public static function get_current_timestamp() {
		return apply_filters( 'stats4wp_current_timestamp', self::get_current_date( 'U' ) );
	}

	/**
	 * Set WordPress TimeZone offset
	 */
	public static function set_timezone() {
		if ( get_option( 'timezone_string' ) ) {
			return timezone_offset_get( timezone_open( get_option( 'timezone_string' ) ), new \DateTime() );
		} elseif ( get_option( 'gmt_offset' ) ) {
			return get_option( 'gmt_offset' ) * 60 * 60;
		}

		return 0;
	}

	/**
	 * @param string $format
	 * @param null   $strtotime
	 * @param null   $relative
	 *
	 * @return bool|string
	 */
	public static function get_current_date( $format = 'Y-m-d H:i:s', $strtotime = null, $relative = null ) {
		if ( $strtotime ) {
			if ( $relative ) {
				return date( $format, strtotime( "{$strtotime} day", $relative ) + self::set_timezone() );
			} else {
				return date( $format, strtotime( "{$strtotime} day" ) + self::set_timezone() );
			}
		} else {
			return date( $format, time() + self::set_timezone() );
		}
	}

	/**
	 * @param string $format
	 * @param null   $strtotime
	 * @param null   $relative
	 *
	 * @return bool|string
	 */
	public static function get_current_gmdate( $format = 'Y-m-d H:i:s', $strtotime = null, $relative = null ) {
		if ( $strtotime ) {
			if ( $relative ) {
				return gmdate( $format, strtotime( "{$strtotime} day", $relative ) + self::set_timezone() );
			} else {
				return gmdate( $format, strtotime( "{$strtotime} day" ) + self::set_timezone() );
			}
		} else {
			return gmdate( $format, time() + self::set_timezone() );
		}
	}
}
