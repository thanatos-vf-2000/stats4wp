<?php
/**
 *
 * @package STATS4WPPlugin
 * @version 1.4.23
 */
namespace STATS4WP\Core;

class I18n {
    public function register() {
		add_action( 'init', array( $this, 'init' ) );
	}

    public function init() {
        if ( is_textdomain_loaded( 'stats4wp' ) ) {
            return;
        }
		load_plugin_textdomain('stats4wp', false, STATS4WP_PATH . '/languages');

	}
}