<?php
/**
 * @package  STATS4WPPlugin
 * @Version 0.0.1
 * 
 * Desciption: Admin Page
 */
use STATS4WP\Core\Options;


self::get_template('header');
settings_errors();
?>
    <ul class="nav stats4wp-nav-tabs">
		<li class="active"><a href="#tab-1"><?php _e('Manage Settings','stats4wp'); ?></a></li>
		<li><a href="#tab-2"><?php _e('Updates','stats4wp'); ?></a></li>
		<li><a href="#tab-3"><?php _e('About','stats4wp'); ?></a></li>
	</ul>

	<div class="stats4wp-tab-content">
		<div id="tab-1" class="stats4wp-tab-pane active">

			<form method="post" action="options.php">
				<input type="hidden" name="stats4wp_plugin[install]" value="1"/>
				<input type="hidden" name="stats4wp_plugin[version]" value="<?php echo esc_html(Options::get_option('version')); ?>"/>
				<?php 
					settings_fields( STATS4WP_NAME.'_plugin_settings' );
					do_settings_sections( STATS4WP_NAME.'_plugin' );
					submit_button();
				?>
			</form>
			
		</div>

		<div id="tab-2" class="stats4wp-tab-pane">
			<h3><?php _e('Updates','stats4wp'); ?></h3>
			<dl>
				<?php
				$nb=0;
				if ($file = fopen(STATS4WP_PATH ."changelog.txt", "r")) {
					while(!feof($file)) {
						$line = fgets($file);
						if (preg_match("/= (.*) =/", $line, $matches)) {
							$nb++;
							$ver = $matches[1];
						} elseif (preg_match("/\*Release Date -(.*)\*/", $line, $matches)) {
							$nb++;
							echo "<dt><b>" . esc_html($ver) ."</b>: ". esc_html($matches[1])."</dt>";
						} elseif ($nb > 2) {
							echo "<dd>" . esc_html($line) ."</dd>";
						}
					}
					fclose($file);
				}
				?>
			</dl>
		</div>

		<div id="tab-3" class="stats4wp-tab-pane">
			<h3><?php _e('About','stats4wp'); ?></h3>
			<p>Version : <?php echo STATS4WP_VERSION; ?></p>
			<p><?php _e('Credit','stats4wp'); ?>: Franck VANHOUCKE</p>
			<dl>
				<?php
				$json_install = file_get_contents(STATS4WP_PATH ."vendor/composer/installed.json");
				$json_data = json_decode($json_install, true);
				foreach ($json_data as $install_name => $install_data) {
					echo "<dt><b>" . esc_html($install_data['name']) . "</b></td>";
					echo "<dd>" . __('Version', 'stats4wp'). ": " .esc_html($install_data['version']). "</dd>";
				}
				?>
				<dt><b>Geo2IP Lite</b></dt>
				<dd><?php echo esc_html(STATS4WP\Api\GeoIP::$geoip_date); ?></dd>
				<dd><?php echo esc_html(STATS4WP\Api\GeoIP::$geoip_file); ?></dd>
				<dt><b>Chart.js</b> (<a href="https://www.chartjs.org/" target="_blank">www.chartjs.org</a>)</dt>
				<dd><?php echo esc_html(STATS4WP_CHARTJS_VERSION); ?></dd>
			</dl>
		</div>
	</div>
<?php
self::get_template('footer');
?>

