<?php
/**
 *
 * @package STATS4WPPlugin
 * @version 1.4.14
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

        if (WP_DEBUG ) {
            wp_enqueue_style(STATS4WP_NAME, STATS4WP_URL . 'assets/css/style.css', array(), STATS4WP_VERSION);
        } else {
            wp_enqueue_style(STATS4WP_NAME, STATS4WP_URL . 'assets/css/style.min.css', array(), STATS4WP_VERSION);
        }
    }

    public function admin_enqueue()
    {

        wp_enqueue_script('jquery');
        if (WP_DEBUG ) {
            wp_enqueue_style(STATS4WP_NAME . '_admin', STATS4WP_URL . 'assets/css/admin-customizer.css', false, STATS4WP_VERSION);
            wp_enqueue_script(STATS4WP_NAME . '_admin', STATS4WP_URL . 'assets/js/admin.js', array( 'jquery' ), STATS4WP_VERSION, true);
            wp_enqueue_script('chart-js', STATS4WP_URL . 'assets/js/chart.umd.js', array( 'jquery' ), STATS4WP_CHARTJS_VERSION, true);
        } else {
            wp_enqueue_style(STATS4WP_NAME . '_admin', STATS4WP_URL . 'assets/css/admin-customizer.min.css', false, STATS4WP_VERSION);
            wp_enqueue_script(STATS4WP_NAME . '_admin', STATS4WP_URL . 'assets/js/admin.min.js', array( 'jquery' ), STATS4WP_VERSION, true);
            wp_enqueue_script('chart-js', STATS4WP_URL . 'assets/js/chart.umd.min.js', array( 'jquery' ), STATS4WP_CHARTJS_VERSION, true);
        }
        if (! wp_script_is('jquery', 'done') ) {
            wp_enqueue_script('jquery');
        }
        if (! wp_script_is('jquery-ui-core', 'done') ) {
            wp_enqueue_script('jquery-ui-core');
        }
        wp_enqueue_script(
            'jvectormap',
            STATS4WP_URL . 'assets/js/jquery-jvectormap-' . STATS4WP_JVECTORMAP_VERSION . '.min.js',
            array( 'jquery' ),
            STATS4WP_JVECTORMAP_VERSION,
            array(
            'strategy' => 'defer',
            )
        );
        wp_enqueue_script(
            'jvectormap-world',
            STATS4WP_URL . 'assets/js/jquery-jvectormap-world-mill.js',
            array( 'jquery', 'jvectormap' ),
            STATS4WP_JVECTORMAP_VERSION,
            array(
            'strategy' => 'defer',
            )
        );
        wp_enqueue_style('jvectormap', STATS4WP_URL . 'assets/css/jquery-jvectormap-2.0.5.css', false, STATS4WP_JVECTORMAP_VERSION);

    }
}
