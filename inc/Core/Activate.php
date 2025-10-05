<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */
namespace STATS4WP\Core;

use SYAYS\Api\GeoIP;

class Activate {



	public static function activate( $network_wide ) {
		flush_rewrite_rules();
		Install::install( $network_wide );

		Options::set_option( 'version', STATS4WP_VERSION );
		Options::set_option( 'install', 1 );
	}
}
