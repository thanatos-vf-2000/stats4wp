<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 */

namespace STATS4WP;

use STATS4WP\Core\Options;

final class Init
{
	/**
	 * Store all the classes inside an array
	 * @return array Full list of classes
	 */
	public static function get_services() 
	{
		return [
			Core\SettingsLinks::class,
			Core\Enqueue::class,
			Stats\Visitor::class,
			Stats\Page::class,
			Stats\UserOnline::class,
			Ui\DashBoard::class,
			Ui\Visitors::class,
			Ui\Pages::class,
			Ui\Settings::class,
			Ui\DashboardWidgetAdmin::class
		];
	}

	/**
	 * Loop through the classes, initialize them, 
	 * and call the register() method if it exists
	 * @return
	 */
	public static function register_services() 
	{

		$opt = get_option( STATS4WP_NAME . '_plugin' );
		if (is_array($opt)) {
			if ( !array_key_exists('version',$opt) || $opt['version'] != STATS4WP_VERSION) {
				Options::set_option('version',STATS4WP_VERSION);
			}
		}

		
		
		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/**
	 * Initialize the class
	 * @param  class $class    class from the services array
	 * @return class instance  new instance of the class
	 */
	private static function instantiate( $class )
	{
		$service = new $class();

		return $service;
	}
}