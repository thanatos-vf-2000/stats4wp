<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */

namespace STATS4WP\Ui;

use STATS4WP\Core\BaseController;

use STATS4WP\Api\SettingsApi;
use STATS4WP\Api\Callbacks\AdminCallbacks;
use STATS4WP\Api\Callbacks\ManagerCallbacks;

/**
 * Class DashBoard
 */
class DashBoard extends BaseController {



	public $pages = array();

	public $settings;

	public $callbacks;

	public $callbacks_mngr;

	public function register() {
		$this->settings       = new SettingsApi();
		$this->callbacks      = new AdminCallbacks();
		$this->callbacks_mngr = new ManagerCallbacks();

		$this->setPages();

		$this->set_settings();
		$this->set_sections();
		$this->set_fields();

		$this->settings->add_pages( $this->pages )->with_sub_page( 'Dashboard' )->register();

		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
	}

	public function setPages() {
		$icon_svg = STATS4WP_URL . 'assets/images/logo-end.png';
		if ( ! function_exists( 'get_plugin_data' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugin_data = get_plugin_data( STATS4WP_FILE );
		$this->pages = array(
			array(
				'page_title' => $plugin_data['Name'],
				'menu_title' => ucwords( STATS4WP_NAME ),
				'capability' => 'manage_options',
				'menu_slug'  => STATS4WP_NAME . '_plugin',
				'callback'   => array( $this->callbacks, 'adminDashboard' ),
				'icon_url'   => $icon_svg,
				'position'   => 110,
			),
		);
	}

	public function set_settings() {
		$args = array(
			array(
				'option_group' => STATS4WP_NAME . '_plugin_settings',
				'option_name'  => STATS4WP_NAME . '_plugin',
				'callback'     => array( $this->callbacks_mngr, 'checkboxSanitize' ),
			),
		);

		$this->settings->set_settings( $args );
	}

	public function set_sections() {
		$args = array(
			array(
				'id'       => STATS4WP_NAME . '_admin_index',
				'title'    => __( 'Settings Manager', 'stats4wp' ),
				'callback' => array( $this->callbacks_mngr, 'adminIndexSectionManager' ),
				'page'     => STATS4WP_NAME . '_plugin',
			),
		);

		$this->settings->set_sections( $args );
	}

	public function set_fields() {
		$args         = array();
		$defaults     = $this->get_customizer_configuration_defaults();
		$all_defaults = $this->load_php_config( STATS4WP_PATH . 'assets/defaults.php' );
		foreach ( $this->managers as $key => $value ) {
			if ( ! in_array( $key, array( 'version', 't', 'install' ), false ) ) {
				$config = wp_parse_args( $all_defaults[ $key ], $defaults );
				switch ( $config['type'] ) {
					case 'checkboxField':
						$args[] = array(
							'id'       => $key,
							'title'    => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
							'page'     => STATS4WP_NAME . '_plugin',
							'section'  => $config['section'],
							'args'     => array(
								'option_name' => STATS4WP_NAME . '_plugin',
								'label_for'   => $key,
								'value'       => $value,
								'message'     => $config['message'],
								'link'        => $config['link'],
								'class'       => 'ui-toggle',
							),
						);
						break;
					case 'listField':
						$args[] = array(
							'id'       => $key,
							'title'    => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'listField' ),
							'page'     => STATS4WP_NAME . '_plugin',
							'section'  => $config['section'],
							'args'     => array(
								'option_name' => STATS4WP_NAME . '_plugin',
								'label_for'   => $key,
								'value'       => $value,
								'message'     => $config['message'],
								'class'       => 'ui-toggle',
								'choices'     => $config['choices'],
							),
						);
						break;
					case 'ImageField':
						$args[] = array(
							'id'       => $key,
							'title'    => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'ImageField' ),
							'page'     => STATS4WP_NAME . '_plugin',
							'section'  => $config['section'],
							'args'     => array(
								'option_name' => STATS4WP_NAME . '_plugin',
								'label_for'   => $key,
								'value'       => $value,
								'message'     => $config['message'],
								'class'       => 'ui-toggle',
								'height'      => $config['height'],
								'width'       => $config['width'],
							),
						);
						break;
					case 'ColorField':
						$args[] = array(
							'id'       => $key,
							'title'    => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'ColorField' ),
							'page'     => STATS4WP_NAME . '_plugin',
							'section'  => $config['section'],
							'args'     => array(
								'option_name' => STATS4WP_NAME . '_plugin',
								'label_for'   => $key,
								'value'       => $value,
								'message'     => $config['message'],
								'class'       => 'ui-toggle',
							),
						);
						break;
					case 'TextField':
						$args[] = array(
							'id'       => $key,
							'title'    => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'TextField' ),
							'page'     => STATS4WP_NAME . '_plugin',
							'section'  => $config['section'],
							'args'     => array(
								'option_name' => STATS4WP_NAME . '_plugin',
								'label_for'   => $key,
								'value'       => $value,
								'message'     => $config['message'],
								'class'       => 'ui-toggle',
							),
						);
						break;
				}
			}
		}

		$this->settings->set_fields( $args );
	}

	private function get_customizer_configuration_defaults() {
		return apply_filters(
			'stats4wp_customizer_configuration_defaults',
			array(
				'type'    => null,
				'title'   => null,
				'message' => '',
				'section' => STATS4WP_NAME . '_admin_index',
				'height'  => null,
				'width'   => null,
				'link'    => '',
			)
		);
	}

	private function load_php_config( $path ) {
		if ( ! file_exists( $path ) ) {
			return array();
		}
		$content = include $path;
		return $content;
	}

	/**
	 * Admin footer text.
	 *
	 * Modifies the "Thank you" text displayed in the admin footer.
	 *
	 * Fired by `admin_footer_text` filter.
	 *
	 * @since  1.3.0
	 * @access public
	 *
	 * @param string $footer_text The content that will be printed.
	 *
	 * @return string The content that will be printed.
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen     = get_current_screen();
		$is_stats4wp_screen = ( $current_screen && false !== strpos( $current_screen->id, 'stats4wp' ) );

		if ( $is_stats4wp_screen ) {
			$footer_text = sprintf(
			/* translators: 1: Elementor, 2: Link to plugin review */
				__( 'Enjoyed %1$s? Please leave us a %2$s rating. We really appreciate your support!', 'stats4wp' ),
				'<strong>' . esc_html__( 'Stats4WP', 'stats4wp' ) . '</strong>',
				'<a href="https://wordpress.org/support/plugin/stats4wp/reviews/#new-post" target="_blank" class ="stats4wp-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}
}
