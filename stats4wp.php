<?php
/*
Plugin Name: stats4wp
Plugin URI: https://ginkgos.net/plugin/stats4wp/
Description: Statistics For WorPress.
Version: 1.2.0
Requires at least: 5.7
Tested up to: 5.8
Requires PHP: 7.4
Author: Franck VANHOUCKE
Author URI: https://ginkgos.net/
Network: true
License: GPLv2 or later
Domain Path: /languages
Text Domain: stats4wp

Copyright 2020-2021 Franck VANHOUCKE

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );


if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * Plugin variable information
 */
define('STATS4WP_VERSION', '1.2.0' );
define('STATS4WP_NAME', 'stats4wp' );
define('STATS4WP_FILE', __FILE__ );
define('STATS4WP_PATH', plugin_dir_path( STATS4WP_FILE ) );
define('STATS4WP_URL', plugin_dir_url( STATS4WP_FILE ) );
define('STATS4WP_CHARTJS_VERSION', '3.6.2' );


/**
 * The code that runs during plugin activation
 */
function stats4wp_activate_plugin($network_wide) {
	STATS4WP\Core\Activate::activate($network_wide);
}
register_activation_hook( STATS4WP_FILE, 'stats4wp_activate_plugin' );

/**
 * The code that runs during plugin deactivation
 */
function stats4wp_deactivate_plugin() {
	STATS4WP\Core\Deactivate::deactivate();
}
register_deactivation_hook( STATS4WP_FILE, 'stats4wp_deactivate_plugin' );

/**
 * The code that run for Core executing
 */
if ( class_exists( 'STATS4WP\\Init' ) ) {
	STATS4WP\Init::register_services();
}