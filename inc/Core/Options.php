<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 */
namespace STATS4WP\Core;

class Options
{
    /**
     * Class instance.
     *
     * @since 1.0.0
     * @access private
     * @var $instance Class instance.
     */
    private static $instance;

    /**
     * A static option variable.
     *
     * @since 1.0.0
     * @access private
     * @var mixed $db_options
     */
    private static $db_options;

    /**
     * A static option variable.
     *
     * @since 1.0.0
     * @access private
     * @var mixed $db_options
     */
    private static $db_options_no_defaults;
    
    /**
     * Initiator
     *
     * @since 1.0.0
     */
    public function register()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
            if (empty(self::$db_options)) {
                self::refresh();
            }
        }
    }

    /**
     * LoadPHPConfig - load default config for plugin
     *
     * @since 1.0.0
     * @return array()
     */
    private static function loadPHPConfig($path)
    {
        
        if (! file_exists($path)) {
            return array();
        }
        
        $content = require $path;
        
        return $content;
    }

    /**
     * Set default option values
     *
     * @since 1.0.0
     * @return default values of the .
     */
    public static function defaults()
    {
        // Defaults list of options.
        $defaults_sys = array('version'  => '0.0.0',
                't'=> time()
        );
        $defaults_app = self::loadPHPConfig(STATS4WP_PATH . 'assets/options.php');

        return array_merge($defaults_sys, $defaults_app);
    }

    /**
     * Get options from static array()
     *
     * @since 1.0.0
     * @return array    Return array of options.
     */
    public static function get_options()
    {
        if (empty(self::$db_options)) {
            self::refresh();
        }
        return self::$db_options;
    }

    /**
     * Get specific option
     *
     * @since 1.0.0
     * @return array    Return array of options.
     */
    public static function get_option($opt)
    {
        if (empty(self::$db_options)) {
            self::refresh();
        }
        return self::$db_options[$opt];
    }

    /**
     * Update  static option array.
     *
     * @since 1.0.0
     */
    public static function refresh()
    {
        self::$db_options = wp_parse_args(
            self::get_db_options(),
            self::defaults()
        );
    }

    /**
     * Get options from static array() from database
     *
     * @since 1.0.0
     * @return array    Return array of options from database.
     */
    public static function get_db_options()
    {
        self::$db_options_no_defaults = get_option(STATS4WP_NAME.'_plugin');
        return self::$db_options_no_defaults;
    }

    /**
     * Set option to database
     *
     * @since 1.0.0
     * @return true/false
     */
    public static function set_option($name, $value)
    {
        if (empty(self::$db_options)) {
            self::refresh();
        }
        self::$db_options[$name] = $value;
        self::$db_options['t']=time();
        update_option(STATS4WP_NAME.'_plugin', self::$db_options);
        return true;
    }

    /**
     * Delete option to database
     *
     * @since 1.0.0
     * @return true/false
     */
    public static function del_option($name)
    {
        if (empty(self::$db_options)) {
            self::refresh();
        }
        unset(self::$db_options[$name]);
        self::$db_options['t']=time();
        update_option(STATS4WP_NAME.'_plugin', self::$db_options);
        return true;
    }
}
