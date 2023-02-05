<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.8
 */
namespace STATS4WP\Core;

use STATS4WP\Core\Options;

class Enqueue
{
    public function register() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
	}

	public function init() {
		load_plugin_textdomain( 'stats4wp', false, STATS4WP_PATH . '/languages');

		if (WP_DEBUG) {
			wp_enqueue_style( STATS4WP_NAME, STATS4WP_URL . 'assets/css/style.css' );
		} else {
			wp_enqueue_style( STATS4WP_NAME, STATS4WP_URL . 'assets/css/style.min.css' );
		}
	}
	
	public function admin_enqueue() {

		wp_enqueue_script('jquery');
		if (WP_DEBUG) {
			wp_enqueue_style( STATS4WP_NAME."_admin", STATS4WP_URL . 'assets/css/admin-customizer.css', false,STATS4WP_VERSION);
			wp_enqueue_script( STATS4WP_NAME."_admin", STATS4WP_URL . 'assets/js/admin.js', array('jquery'), STATS4WP_VERSION, true  );
			if (Options::get_option('cdn_chartjs') === false ) {
				wp_enqueue_script( "chart-js", STATS4WP_URL . 'assets/js/chart.js', array('jquery'), STATS4WP_CHARTJS_VERSION, true  );
			} else {
				wp_enqueue_script( "chart-js", "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/" . STATS4WP_CHARTJS_VERSION . "/chart.umd.js", array('jquery'), STATS4WP_CHARTJS_VERSION, true  );
			}
		} else {
			wp_enqueue_style( STATS4WP_NAME."_admin", STATS4WP_URL . 'assets/css/admin-customizer.min.css', false,STATS4WP_VERSION);
			wp_enqueue_script( STATS4WP_NAME."_admin", STATS4WP_URL . 'assets/js/admin.min.js', array('jquery'), STATS4WP_VERSION, true   );
			if (Options::get_option('cdn_chartjs') === false ) {
				wp_enqueue_script( "chart-js", STATS4WP_URL . 'assets/js/chart.min.js', array('jquery'), STATS4WP_CHARTJS_VERSION, true  );
			} else {
				wp_enqueue_script( "chart-js", "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/" . STATS4WP_CHARTJS_VERSION . "/chart.umd.min.js



				", array('jquery'), STATS4WP_CHARTJS_VERSION, true  );
			}
			
		}
	}

}