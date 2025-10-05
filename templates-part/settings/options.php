<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.17
 *
 * Desciption: Settings options
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Core\Options;

global $wp_filesystem;

require_once ABSPATH . '/wp-admin/includes/file.php';
WP_Filesystem();

settings_errors();
?>
	<ul class="nav stats4wp-nav-tabs">
		<li class="active"><a href="#tab-1"><?php esc_html_e( 'Manage Settings', 'stats4wp' ); ?></a></li>
		<li><a href="#tab-2"><?php esc_html_e( 'Data', 'stats4wp' ); ?></a></li>
		<li><a href="#tab-3"><?php esc_html_e( 'Updates', 'stats4wp' ); ?></a></li>
		<li><a href="#tab-4"><?php esc_html_e( 'About', 'stats4wp' ); ?></a></li>
	</ul>

	<div class="stats4wp-tab-content">
		<div id="tab-1" class="stats4wp-tab-pane active">
			<div class="stats4wp-infos">
				<form method="post" action="options.php">
					<input type="hidden" name="stats4wp_plugin[install]" value="1"/>
					<input type="hidden" name="stats4wp_plugin[version]" value="<?php echo esc_html( Options::get_option( 'version' ) ); ?>"/>
					<?php
					settings_fields( STATS4WP_NAME . '_plugin_settings' );
					do_settings_sections( STATS4WP_NAME . '_plugin' );
					submit_button();
					?>
				</form>
			</div>
			<div class="stats4wp-advertise">
				<?php self::get_template( array( 'support' ) ); ?>
			</div>
		</div>
		<div id="tab-2" class="stats4wp-tab-pane">
			<div class="stats4wp-infos">
				<?php self::get_template( array( 'settings/tables' ) ); ?>
			</div>
			<div class="stats4wp-advertise">
				<?php self::get_template( array( 'support' ) ); ?>
			</div>
		</div>
		<div id="tab-3" class="stats4wp-tab-pane">
			<div class="stats4wp-infos">
				<h3><?php esc_html_e( 'Updates', 'stats4wp' ); ?></h3>
				<dl>
					<?php
					$nb = 0;
					if ( $wp_filesystem->exists( STATS4WP_PATH . 'changelog.txt' ) ) {
						$file = $wp_filesystem->get_contents( STATS4WP_PATH . 'changelog.txt' );
						while ( ! feof( $file ) ) {
							$line = fgets( $file );
							if ( preg_match( '/= (.*) =/', $line, $matches ) ) {
										++$nb;
										$ver = $matches[1];
							} elseif ( preg_match( '/\*Release Date -(.*)\*/', $line, $matches ) ) {
								++$nb;
								echo '<dt><b>' . esc_html( $ver ) . '</b>: ' . esc_html( $matches[1] ) . '</dt>';
							} elseif ( $nb > 2 ) {
										echo '<dd>' . esc_html( $line ) . '</dd>';
							}
						}
					}
					?>
				</dl>
			</div>
			<div class="stats4wp-advertise">
				<?php self::get_template( array( 'support' ) ); ?>
			</div>
		</div>

		<div id="tab-4" class="stats4wp-tab-pane">
			<div class="stats4wp-infos">
				<h3><?php esc_html_e( 'About', 'stats4wp' ); ?></h3>
				<p>Version : <?php echo esc_html( STATS4WP_VERSION ); ?></p>
				<p><?php esc_html_e( 'Credit', 'stats4wp' ); ?>: Franck VANHOUCKE</p>
				<dl>
					<?php
					$json_install = file_get_contents( STATS4WP_PATH . 'vendor/composer/installed.json' );
					$json_data    = json_decode( $json_install, true );
					foreach ( $json_data as $install_name => $install_data ) {
						if ( isset( $install_data ) && is_array( $install_data ) ) {
							foreach ( $install_data as $component ) {
										echo '<dt><b>' . esc_html( $component['name'] ) . '</b></td>';
										echo '<dd>' . esc_html__( 'Version', 'stats4wp' ) . ': ' . esc_html( $component['version'] ) . '</dd>';
							}
						}
					}
					?>
					<dt><b>Geo2IP Lite</b></dt>
					<dd><?php echo esc_html( STATS4WP\Api\GeoIP::$geoip_date ); ?></dd>
					<dd><?php echo esc_html( STATS4WP\Api\GeoIP::$geoip_file ); ?></dd>
					<dt><b>Chart.js</b> (<a href="https://www.chartjs.org/" target="_blank">www.chartjs.org</a>)</dt>
					<dd><?php echo esc_html( STATS4WP_CHARTJS_VERSION ); ?></dd>
					<dt><b>jVectorMap</b> (<a href="https://jvectormap.com/" target="_blank">jvectormap.com</a>)</dt>
					<dd>Updated 3 mai 2021</dd>

				</dl>
			</div>
			<div class="stats4wp-advertise">
				<?php self::get_template( array( 'support' ) ); ?>
			</div>
		</div>
	</div>
