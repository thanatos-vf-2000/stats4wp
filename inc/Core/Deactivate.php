<?php
/**
 * @package STATS4WPPlugin
 * @version 1.5.0
 */
namespace STATS4WP\Core;

class Deactivate {



	public static function deactivate() {
		flush_rewrite_rules();

		Cron::unschedule();

		Uninstall::uninstall();

		if ( get_option( STATS4WP_NAME . '_plugin' ) ) {
			delete_option( STATS4WP_NAME . '_plugin' );
		}
	}
}
