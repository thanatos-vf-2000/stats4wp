<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 */
namespace STATS4WP\Core;

class Deactivate
{
	public static function deactivate() {
		flush_rewrite_rules();

		Uninstall::uninstall();
		
		if ( get_option( STATS4WP_NAME.'_plugin' ) ) {
			delete_option( STATS4WP_NAME.'_plugin');
		}
	}
}