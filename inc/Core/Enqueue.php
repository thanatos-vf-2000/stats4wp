<?php
/**
 * @package  STATS4WPPlugin
 * @version 1.4.0
 */
namespace STATS4WP\Core;

use STATS4WP\Core\Options;

class Enqueue
{
    public function register()
    {
        add_action('init', array( $this, 'init' ));
        add_action('admin_enqueue_scripts', array( $this, 'admin_enqueue' ));
    }

    public function init()
    {
        load_plugin_textdomain('stats4wp', false, STATS4WP_PATH . '/languages');

        if (WP_DEBUG) {
            wp_enqueue_style(STATS4WP_NAME, STATS4WP_URL . 'assets/css/style.css');
        } else {
            wp_enqueue_style(STATS4WP_NAME, STATS4WP_URL . 'assets/css/style.min.css');
        }
    }
    
    public function admin_enqueue()
    {

        wp_enqueue_script('jquery');
        if (WP_DEBUG) {
            wp_enqueue_style(STATS4WP_NAME."_admin", STATS4WP_URL . 'assets/css/admin-customizer.css', false, STATS4WP_VERSION);
            wp_enqueue_script(STATS4WP_NAME."_admin", STATS4WP_URL . 'assets/js/admin.js', array('jquery'), STATS4WP_VERSION, true);
            wp_enqueue_script("chart-js", STATS4WP_URL . 'assets/js/chart.umd.js', array('jquery'), STATS4WP_CHARTJS_VERSION, true);
        } else {
            wp_enqueue_style(STATS4WP_NAME."_admin", STATS4WP_URL . 'assets/css/admin-customizer.min.css', false, STATS4WP_VERSION);
            wp_enqueue_script(STATS4WP_NAME."_admin", STATS4WP_URL . 'assets/js/admin.min.js', array('jquery'), STATS4WP_VERSION, true);
            wp_enqueue_script("chart-js", STATS4WP_URL . 'assets/js/chart.umd.min.js', array('jquery'), STATS4WP_CHARTJS_VERSION, true);
        }
        wp_enqueue_script("google-loader", STATS4WP_URL . 'assets/js/loader.js', array('jquery'), STATS4WP_CHARTJS_VERSION, true);
    }
}
