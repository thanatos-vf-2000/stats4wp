<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.2
 */

namespace STATS4WP\Ui;

use STATS4WP\Core\BaseController;

use STATS4WP\Api\SettingsApi;
use STATS4WP\Api\Callbacks\AdminCallbacks;
use STATS4WP\Api\Callbacks\ManagerCallbacks;

/**
* 
*/
class DashBoard extends BaseController
{

    public $pages = array();

    public $settings;

    public $callbacks;

	public $callbacks_mngr;

    public function register()
    {
        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();
		$this->callbacks_mngr = new ManagerCallbacks();

        $this->setPages();

        $this->setSettings();
        $this->setSections();
        $this->setFields();

        $this->settings->addPages( $this->pages )->withSubPage( 'Dashboard' )->register();

		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
    }

    public function setPages() 
	{
		$icon_svg = "data:image/svg+xml;charset=UTF-8,%3c?xml version='1.0' standalone='no'?%3e%3c!DOCTYPE svg PUBLIC '-//W3C//DTD SVG 20010904//EN' 'http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd'%3e%3csvg version='1.0' xmlns='http://www.w3.org/2000/svg' width='20pt' height='20pt' viewBox='0 0 256.000000 256.000000' preserveAspectRatio='xMidYMid meet'%3e%3cg transform='translate(0.000000,256.000000) scale(0.100000,-0.100000)' fill='%23000000' stroke='none'%3e%3cpath style=' stroke:none;fill-rule:nonzero;fill:rgb(65.490196%25,66.27451%25,67.45098%25);fill-opacity:1;' d='M614 2470 c-5 -5 -43 -10 -84 -12 l-75 -3 1 -55 c1 -46 22 -126 60 -225 4 -11 -16 9 -44 44 -59 73 -102 107 -154 123 -32 9 -40 8 -63 -10 -14 -12 -41 -26 -59 -32 -28 -9 -32 -14 -28 -40 2 -16 -3 -43 -12 -61 -9 -18 -16 -38 -16 -44 0 -7 -11 -20 -25 -29 -19 -12 -25 -25 -25 -51 0 -20 -8 -41 -19 -52 -10 -10 -21 -36 -24 -58 -4 -22 -14 -45 -23 -52 -13 -10 -14 -19 -6 -60 8 -33 8 -70 0 -120 -9 -63 -8 -76 8 -110 11 -21 35 -48 54 -61 48 -33 108 -26 285 31 253 82 340 94 439 61 39 -14 51 -23 63 -53 20 -47 115 -209 142 -241 11 -14 -5 21 -37 76 -86 150 -116 232 -108 288 11 74 73 153 231 296 79 71 153 145 164 163 35 56 24 105 -28 122 -10 3 -32 23 -49 44 -32 40 -99 71 -154 71 -18 0 -33 8 -44 25 -22 34 -48 39 -102 20 -44 -16 -53 -16 -126 -1 -89 18 -128 20 -142 6z'/%3e%3cpath d='M1775 2383 c-45 -23 -85 -64 -102 -103 -8 -19 -19 -93 -24 -165 -5 -71 -14 -155 -20 -185 -26 -130 -105 -284 -221 -427 -42 -51 -60 -65 -109 -84 -33 -12 -58 -23 -56 -25 1 -2 29 4 61 12 l57 15 67 -36 c197 -109 326 -259 474 -550 87 -172 103 -195 132 -195 28 0 43 16 48 49 2 19 18 30 82 57 87 37 126 67 126 98 0 15 14 27 50 43 44 19 50 26 50 51 0 16 9 37 20 47 33 30 87 125 105 185 9 30 24 72 32 93 14 32 14 45 3 91 -10 42 -10 65 -1 100 16 64 14 94 -10 143 -18 38 -27 45 -75 59 -61 17 -87 14 -243 -31 -86 -25 -185 -33 -196 -15 -11 17 75 51 204 80 198 45 266 75 302 134 46 76 -1 205 -76 206 -11 1 -29 14 -40 32 -30 47 -70 73 -115 73 -36 0 -41 3 -57 39 -22 50 -39 66 -67 66 -13 0 -29 7 -36 15 -7 8 -30 18 -52 22 -22 3 -45 12 -51 20 -36 44 -49 52 -94 58 -26 3 -65 14 -85 25 -44 24 -45 24 -83 3z'/%3e%3cpath d='M1140 1360 c-8 -5 -10 -10 -5 -10 6 0 17 5 25 10 8 5 11 10 5 10 -5 0 -17 -5 -25 -10z'/%3e%3cpath d='M1090 1340 c-8 -5 -10 -10 -5 -10 6 0 17 5 25 10 8 5 11 10 5 10 -5 0 -17 -5 -25 -10z'/%3e%3cpath d='M1030 1326 c0 -2 8 -10 18 -17 15 -13 16 -12 3 4 -13 16 -21 21 -21 13z'/%3e%3cpath d='M950 1271 c-137 -80 -246 -206 -289 -334 -29 -84 -39 -176 -20 -188 26 -16 29 -10 29 55 0 82 29 176 78 252 47 72 155 174 228 215 50 29 64 40 47 38 -5 0 -37 -18 -73 -38z'/%3e%3cpath d='M1081 1265 c14 -16 85 -95 158 -174 73 -79 155 -178 183 -220 27 -42 51 -77 52 -79 2 -1 8 4 15 11 21 26 -95 163 -325 387 -60 58 -97 92 -83 75z'/%3e%3cpath d='M142 624 c-27 -19 -29 -64 -4 -86 9 -9 34 -21 55 -28 30 -9 38 -16 35 -33 -2 -16 -11 -23 -33 -25 -24 -3 -35 3 -57 29 l-28 33 0 -39 0 -40 56 -2 c76 -4 114 17 114 63 0 27 -25 48 -73 64 -45 14 -58 37 -32 56 27 20 33 18 66 -17 l29 -31 0 34 0 34 -53 2 c-34 1 -60 -3 -75 -14z'/%3e%3cpath d='M290 610 c0 -34 15 -40 25 -11 6 21 31 34 46 25 5 -3 9 -43 9 -90 0 -78 -2 -85 -22 -94 -17 -6 -7 -9 42 -9 49 0 61 2 48 10 -15 8 -18 25 -18 99 0 97 6 105 45 65 l25 -24 0 29 0 30 -100 0 -100 0 0 -30z'/%3e%3cpath d='M591 618 c-37 -97 -65 -156 -83 -170 -20 -18 -19 -18 28 -18 42 0 46 2 30 13 -30 22 -12 57 29 57 44 0 68 -35 39 -57 -15 -11 -10 -13 45 -13 61 0 63 1 43 18 -12 9 -37 55 -56 102 -36 88 -59 110 -75 68z m24 -80 c6 -25 5 -28 -19 -28 -30 0 -29 -2 -15 39 12 35 24 31 34 -11z'/%3e%3cpath d='M730 610 c0 -34 15 -40 25 -11 6 21 31 34 46 25 5 -3 9 -43 9 -90 0 -78 -2 -85 -22 -94 -17 -6 -7 -9 42 -9 49 0 61 2 48 10 -15 8 -18 25 -18 99 0 97 6 105 45 65 l25 -24 0 29 0 30 -100 0 -100 0 0 -30z'/%3e%3cpath d='M982 624 c-27 -19 -29 -64 -4 -86 9 -9 34 -21 55 -28 30 -9 38 -16 35 -33 -2 -16 -11 -23 -33 -25 -24 -3 -35 3 -57 29 l-28 33 0 -39 0 -40 56 -2 c76 -4 114 17 114 63 0 27 -25 48 -73 64 -45 14 -58 37 -32 56 27 20 33 18 66 -17 l29 -31 0 34 0 34 -53 2 c-34 1 -60 -3 -75 -14z'/%3e%3cpath d='M1218 431 c-49 -60 -88 -119 -88 -130 0 -20 5 -21 80 -21 l80 0 0 -45 0 -45 45 0 45 0 0 45 c0 41 2 45 25 45 20 0 25 5 25 25 0 20 -5 25 -25 25 l-25 0 0 105 0 105 -37 0 c-36 0 -42 -6 -125 -109z m72 -31 l0 -70 -60 0 -61 0 57 70 c31 39 58 70 60 70 2 0 4 -31 4 -70z'/%3e%3cpath d='M1535 397 c10 -8 32 -54 50 -103 41 -114 56 -118 85 -23 12 39 25 68 29 63 4 -5 18 -37 31 -72 31 -84 46 -79 81 27 16 46 36 92 45 103 16 17 15 18 -26 18 -40 0 -42 -1 -25 -14 19 -14 19 -15 -1 -78 l-20 -63 -22 65 c-12 36 -26 71 -31 79 -16 26 -38 3 -55 -58 -9 -34 -20 -61 -25 -61 -4 0 -15 23 -24 52 -12 38 -13 55 -5 65 8 10 -1 13 -48 13 -51 0 -56 -2 -39 -13z'/%3e%3cpath d='M1893 396 c14 -11 17 -29 17 -94 0 -67 -3 -83 -17 -91 -14 -8 -4 -10 42 -10 53 0 58 1 42 13 -12 10 -17 24 -15 43 3 24 9 29 40 34 50 8 68 25 68 65 0 45 -21 54 -117 54 -67 -1 -76 -3 -60 -14z m111 -12 c9 -8 16 -24 16 -34 0 -22 -26 -50 -46 -50 -10 0 -14 12 -14 43 0 59 14 72 44 41z'/%3e%3c/g%3e%3c/svg%3e";
		if( !function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$plugin_data = get_plugin_data(STATS4WP_FILE );
		$this->pages = array(
			array(
				'page_title' => $plugin_data['Name'], 
				'menu_title' => ucwords(STATS4WP_NAME), 
				'capability' => 'manage_options', 
				'menu_slug' => STATS4WP_NAME.'_plugin', 
				'callback' => array( $this->callbacks, 'adminDashboard' ), 
				'icon_url' => $icon_svg, 
				'position' => 110
			)
		);
	}

    public function setSettings()
	{
		$args = array(
			array(
				'option_group' => STATS4WP_NAME.'_plugin_settings',
				'option_name' => STATS4WP_NAME.'_plugin',
				'callback' => array( $this->callbacks_mngr, 'checkboxSanitize' )
			)
		);

		$this->settings->setSettings( $args );
	}

    public function setSections()
	{
		$args = array(
			array(
				'id' => STATS4WP_NAME.'_admin_index',
				'title' => __('Settings Manager', 'stats4wp'),
				'callback' => array( $this->callbacks_mngr, 'adminIndexSectionManager' ),
				'page' => STATS4WP_NAME.'_plugin'
			)
		);

		$this->settings->setSections( $args );
	}

    public function setFields()
	{
		$args = array();
		$defaults = $this->get_customizer_configuration_defaults();
		$all_defaults = $this->loadPHPConfig(STATS4WP_PATH . 'assets/defaults.php');
		foreach ( $this->managers as $key => $value ) {
			if (!in_array($key,array('version','t','install'))) {
				$config = wp_parse_args( $all_defaults[$key], $defaults );
				switch ( $config['type'] ) {
					case 'checkboxField':
						$args[] = array(
							'id' => $key,
							'title' => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'checkboxField' ),
							'page' => STATS4WP_NAME.'_plugin',
							'section' => $config['section'],
							'args' => array(
								'option_name' => STATS4WP_NAME.'_plugin',
								'label_for' => $key,
								'value'	=> $value,
								'message'	=> $config['message'],
								'link'	=> $config['link'],
								'class' => 'ui-toggle'
							)
						);
						break;
					case 'listField':
						$args[] = array(
							'id' => $key,
							'title' => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'listField' ),
							'page' => STATS4WP_NAME.'_plugin',
							'section' => $config['section'],
							'args' => array(
								'option_name' => STATS4WP_NAME.'_plugin',
								'label_for' => $key,
								'value'	=> $value,
								'message'	=> $config['message'],
								'class' => 'ui-toggle',
								'choices' => $config['choices']
							)
						);
						break;
					case 'ImageField':
						$args[] = array(
							'id' => $key,
							'title' => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'ImageField' ),
							'page' => STATS4WP_NAME.'_plugin',
							'section' => $config['section'],
							'args' => array(
								'option_name' => STATS4WP_NAME.'_plugin',
								'label_for' => $key,
								'value'	=> $value,
								'message'	=> $config['message'],
								'class' => 'ui-toggle',
								'height'	=> $config['height'],
								'width'		=> $config['width']
							)
						);
						break;
					case 'ColorField':
						$args[] = array(
							'id' => $key,
							'title' => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'ColorField' ),
							'page' => STATS4WP_NAME.'_plugin',
							'section' => $config['section'],
							'args' => array(
								'option_name' => STATS4WP_NAME.'_plugin',
								'label_for' => $key,
								'value'	=> $value,
								'message'	=> $config['message'],
								'class' => 'ui-toggle'
							)
						);
						break;
					case 'TextField':
						$args[] = array(
							'id' => $key,
							'title' => $config['title'],
							'callback' => array( $this->callbacks_mngr, 'TextField' ),
							'page' => STATS4WP_NAME.'_plugin',
							'section' => $config['section'],
							'args' => array(
								'option_name' => STATS4WP_NAME.'_plugin',
								'label_for' => $key,
								'value'	=> $value,
								'message'	=> $config['message'],
								'class' => 'ui-toggle'
							)
						);
						break;
				}
			}
		}

		$this->settings->setFields( $args );
	}

    private function get_customizer_configuration_defaults() {
		return apply_filters(
			'stats4wp_customizer_configuration_defaults',
			array(
				'type'		=> null,
				'title'		=> null,
				'message'	=> '',
				'section' 	=> STATS4WP_NAME.'_admin_index',
				'height'	=> null,
				'width'		=> null,
				'link'		=> '',
			)
		);
	}

	private function loadPHPConfig($path)
	{
		if ( ! file_exists($path)) {
			return array();
		}
		$content = require $path;
		return $content;
	}

	/**
	 * Admin footer text.
	 *
	 * Modifies the "Thank you" text displayed in the admin footer.
	 *
	 * Fired by `admin_footer_text` filter.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param string $footer_text The content that will be printed.
	 *
	 * @return string The content that will be printed.
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
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