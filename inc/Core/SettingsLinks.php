<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.15
 */
namespace STATS4WP\Core;

class SettingsLinks {


	public function register() {
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 5 );
	}

	/**
	 * Adds the manage link in the plugins list
	 *
	 * @access global
	 * @return string The manage link in the plugins list
	 */
	public function settings_link( $actions, $stats4wp_file ) {

		static $plugin;

		if ( ! isset( $plugin ) ) {
			$plugin = plugin_basename( STATS4WP_FILE );
		}
		if ( $plugin === $stats4wp_file ) {
			$plugin_data = get_plugin_data( STATS4WP_FILE );
			$actions[]   = '<a href="admin.php?page=' . esc_attr( STATS4WP_NAME ) . '_settings">' . __( 'Settings', 'stats4wp' ) . '</a>';
			$actions[]   = '<a href="' . esc_attr( $plugin_data['PluginURI'] ) . '" target="_blank">' . __( 'Support', 'stats4wp' ) . '</a>';
			$actions[]   = '<a href="' . esc_attr( $plugin_data['AuthorURI'] ) . '" target="_blank">' . __( 'all GinkGos plugins', 'stats4wp' ) . '</a>';
		}

		return $actions;
	}
}
