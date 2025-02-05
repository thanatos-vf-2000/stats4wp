<?php
/**
 * @package STATS4WP Plugin
 * @version 1.4.14
 */
namespace STATS4WP\Core;

class BaseController
{

    public $plugin_path;

    public $plugin_url;

    public $plugin;

    public $managers = array();

    public function __construct()
    {
        $this->plugin_path = STATS4WP_PATH;
        $this->plugin_url  = STATS4WP_URL;
        $this->plugin      = STATS4WP_NAME;
        $this->managers    = array_merge(array(), Options::get_options());
    }

    public function activated( string $key )
    {
        $option = get_option(STATS4WP_NAME . '_plugin');

        return isset($option[ $key ]) ? $option[ $key ] : false;
    }

    /**
     * Get Template File
     *
     * @param string $template
     * @param array  $args
     */
    public static function get_template( $template )
    {
        global $wpdb;

        // Check Load single file or array list
        if (is_string($template) ) {
            $template = explode(' ', $template);
        }

        // Load File
        foreach ( $template as $file ) {
            $template_file = STATS4WP_PATH . 'templates-part/' . $file . '.php';
            if (! file_exists($template_file) ) {
                continue;
            }

            // include File
            include $template_file;
        }
    }
}
