<?php
/**
 * @package STATS4WPPlugin
 * @version 1.6.0
 *
 * Desciption: Settings options
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Core\Options;
use STATS4WP\Api\RestApi;

global $wp_filesystem;

if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

WP_Filesystem();

settings_errors();

if ( isset( $_GET['stats4wp-notice'] ) && isset( $_GET['page'] ) && STATS4WP_NAME . '_settings' === $_GET['page'] ) {
	$notice = sanitize_text_field( wp_unslash( $_GET['stats4wp-notice'] ) );
	$messages = array(
		'key-regenerated' => array( 'success', __( 'A new REST API key has been generated. The previous key no longer works.', 'stats4wp' ) ),
		'report-sent'      => array( 'success', __( 'Test email sent successfully.', 'stats4wp' ) ),
		'report-failed'    => array( 'error', __( 'The test email could not be sent. Check the recipient address and your site\'s mail configuration.', 'stats4wp' ) ),
	);
	if ( isset( $messages[ $notice ] ) ) {
		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $messages[ $notice ][0] ),
			esc_html( $messages[ $notice ][1] )
		);
	}
}
?>
	<ul class="nav stats4wp-nav-tabs">
		<li class="active"><a href="#tab-1"><?php esc_html_e( 'Manage Settings', 'stats4wp' ); ?></a></li>
		<li><a href="#tab-2"><?php esc_html_e( 'Data', 'stats4wp' ); ?></a></li>
		<li><a href="#tab-5"><?php esc_html_e( 'API', 'stats4wp' ); ?></a></li>
		<li><a href="#tab-6"><?php esc_html_e( 'Reports', 'stats4wp' ); ?></a></li>
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
						$content = $wp_filesystem->get_contents( STATS4WP_PATH . 'changelog.txt' );

						if ( $content !== false ) {
							$lines = explode( "\n", $content );
							$nb    = 0;
							$ver   = '';
							foreach ( $lines as $line ) {
								$line = trim( $line );
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

		<div id="tab-5" class="stats4wp-tab-pane">
			<div class="stats4wp-infos">
				<h3><?php esc_html_e( 'REST API', 'stats4wp' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'The "Enable the REST API" checkbox is in the "Manage Settings" tab. Once enabled, the endpoints below are available under /wp-json/stats4wp/v1/.', 'stats4wp' ); ?>
				</p>
				<?php if ( true === Options::get_option( 'rest_api_enabled' ) ) : ?>
					<p>
						<strong><?php esc_html_e( 'Status:', 'stats4wp' ); ?></strong>
						<span class="stats4wp-status-enabled"><?php esc_html_e( 'Enabled', 'stats4wp' ); ?></span>
					</p>
					<p>
						<label for="stats4wp-api-key"><strong><?php esc_html_e( 'Your API key', 'stats4wp' ); ?></strong></label><br/>
						<input type="text" id="stats4wp-api-key" class="stats4wp-api-key-field" readonly="readonly" value="<?php echo esc_attr( RestApi::get_or_create_api_key() ); ?>" onclick="this.select();" />
					</p>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Regenerating the key will immediately break any integration using the current one. Continue?', 'stats4wp' ) ); ?>');">
						<input type="hidden" name="action" value="stats4wp_regenerate_api_key" />
						<?php wp_nonce_field( 'stats4wp_regenerate_api_key' ); ?>
						<?php submit_button( __( 'Regenerate API key', 'stats4wp' ), 'secondary', 'submit', false ); ?>
					</form>

					<h4><?php esc_html_e( 'Available endpoints', 'stats4wp' ); ?></h4>
					<ul class="stats4wp-api-endpoints">
						<li><code>GET /wp-json/stats4wp/v1/summary?from=YYYY-MM-DD&amp;to=YYYY-MM-DD</code></li>
						<li><code>GET /wp-json/stats4wp/v1/pages/top?limit=10</code></li>
						<li><code>GET /wp-json/stats4wp/v1/countries/top?limit=10</code></li>
						<li><code>GET /wp-json/stats4wp/v1/browsers/top?limit=10</code></li>
						<li><code>GET /wp-json/stats4wp/v1/online</code></li>
					</ul>
					<p class="description"><?php esc_html_e( 'Pass the key either as an "X-STATS4WP-API-KEY" header, or as an "api_key" query parameter.', 'stats4wp' ); ?></p>
					<pre class="stats4wp-code-block">curl -H "X-STATS4WP-API-KEY: <?php echo esc_html( RestApi::get_or_create_api_key() ); ?>" "<?php echo esc_url( rest_url( 'stats4wp/v1/summary' ) ); ?>"</pre>
				<?php else : ?>
					<p><strong><?php esc_html_e( 'Status:', 'stats4wp' ); ?></strong> <?php esc_html_e( 'Disabled', 'stats4wp' ); ?></p>
				<?php endif; ?>
			</div>

			<div class="stats4wp-advertise">
				<?php self::get_template( array( 'support' ) ); ?>
			</div>
		</div>

		<div id="tab-6" class="stats4wp-tab-pane">
			<div class="stats4wp-infos">
				<h3><?php esc_html_e( 'Weekly email report', 'stats4wp' ); ?></h3>
				<p class="description">
					<?php esc_html_e( 'The "Send a weekly email report" checkbox and recipient address are in the "Manage Settings" tab. Reports are sent every Monday for the previous 7 days.', 'stats4wp' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'Status:', 'stats4wp' ); ?></strong>
					<?php echo ( true === Options::get_option( 'weekly_report_enabled' ) ) ? '<span class="stats4wp-status-enabled">' . esc_html__( 'Enabled', 'stats4wp' ) . '</span>' : esc_html__( 'Disabled', 'stats4wp' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'Recipient:', 'stats4wp' ); ?></strong>
					<?php
					$recipient = Options::get_option( 'weekly_report_email' );
					echo esc_html( ! empty( $recipient ) ? $recipient : get_option( 'admin_email' ) );
					?>
				</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="stats4wp_send_test_report" />
					<?php wp_nonce_field( 'stats4wp_send_test_report' ); ?>
					<?php submit_button( __( 'Send a test report now', 'stats4wp' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>

			<div class="stats4wp-advertise">
				<?php self::get_template( array( 'support' ) ); ?>
			</div>
		</div>

	</div>