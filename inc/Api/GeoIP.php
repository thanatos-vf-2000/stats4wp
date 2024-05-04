<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.11
 */
namespace STATS4WP\Api;

use MaxMind\Db\Reader;

class GeoIP {

	/**
	 * Date of database GeoIpLitle User IP
	 *
	 * @var string
	 */
	public static $geoip_date = '20240503';

	/**
	 * Date of database GeoIpLitle User IP
	 *
	 * @var string
	 */
	public static $geoip_file = STATS4WP_PATH . 'db/GeoLite2-Country.mmdb';



	/**
	 * Returns the current Country.
	 *
	 * @return string
	 */
	public static function get_country( $ip ) {
		if ( IP::check_ip_range( IP::$private_sub_nets, $ip ) ) {
			return 'local';
		}
		$reader  = new Reader( self::$geoip_file );
		$ip_data = $reader->get( $ip );
		if ( WP_DEBUG ) {
			error_log( print_r( $ip_data, true ) );
		}
		$reader->close();
		return ( ! $ip_data['country']['iso_code'] ? 'none' : $ip_data['country']['iso_code'] );
	}
}
