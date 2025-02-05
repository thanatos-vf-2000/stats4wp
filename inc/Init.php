<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */

namespace STATS4WP;

use STATS4WP\Core\Options;
use STATS4WP\Core\Install;

require STATS4WP_PATH . 'inc/Core/Fonctions.php';

final class Init
{

    /**
     * Store all the classes inside an array
     *
     * @return array Full list of classes
     */
    public static function get_services()
    {
        return array(
        Core\SettingsLinks::class,
        Core\Enqueue::class,
        Stats\Page::class,
        Stats\Visitor::class,
        Stats\UserOnline::class,
        Ui\DashBoard::class,
        Ui\Visitors::class,
        Ui\Pages::class,
        Ui\Settings::class,
        Ui\DashboardWidgetAdmin::class,
        Ui\CSVExport::class,
        Widgets\CptVisitors::class,
        );
    }

    /**
     * Loop through the classes, initialize them,
     * and call the register() method if it exists
     *
     * @return
     */
    public static function register_services()
    {

        $opt = get_option(STATS4WP_NAME . '_plugin');
        if (is_array($opt) ) {
            if (! array_key_exists('version', $opt) || STATS4WP_VERSION !== $opt['version'] ) {
                Options::set_option('version', STATS4WP_VERSION);
                if (is_multisite() ) {
                    Install::install(true);
                } else {
                    Install::install(false);
                }
            }
        }
        $del_old_options = array( 'geochart', 'cdn_chartjs' );
        foreach ( $del_old_options as $doo ) {
            Options::del_option($doo);
        }

        foreach ( self::get_services() as $class ) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register') ) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     *
     * @param  class $class class from the services array
     * @return class instance  new instance of the class
     */
    private static function instantiate( $class )
    {
        $service = new $class();

        return $service;
    }
}
