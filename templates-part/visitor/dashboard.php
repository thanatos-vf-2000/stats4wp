<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.5
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

$page = ( isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '' );
if ( 'stats4wp_plugin' === $page ) {
	$data = 'all';
} else {
	$data = '';
}

if ( DB::exist_row( 'visitor' ) ) {
	$param = AdminGraph::getdate( $data );
	if ( ! isset( $wpdb->stats4wp_visitor ) ) {
		$wpdb->stats4wp_visitor = DB::table( 'visitor' );}
	?>
	<div class="rows stats4wp-dashboard">
		<div class="stats4wp-rows">
			<div class="stats4wp-inline width30 border">
				<p class="title"><?php echo esc_html( __( 'Navigator', 'stats4wp' ) ); ?></p>
				<?php
				$navigators      = $wpdb->get_results( $wpdb->prepare( "SELECT agent,count(*) as nb FROM {$wpdb->stats4wp_visitor} where device!='bot' AND last_counter BETWEEN %s AND %s group by agent order by 2 DESC", $param['from'], $param['to'] ) );
				$navigator_total = array_sum( array_column( $navigators, 'nb' ) );
				$navigator_nb    = 0;
				echo '<p>';
				foreach ( $navigators as $navigator ) {
					$navigator_nb++;
					$percent = round( $navigator->nb * 100 / $navigator_total );
					if ( $navigator_nb < 10 && $percent > 0 ) {
						echo esc_html( $navigator->agent . " ($percent%) - " );
					}
				}
				echo esc_html( __( 'All', 'stats4wp' ) ) . '</p>';
				?>
			</div>
			<div class="stats4wp-inline width30 border">
				<p class="title"><?php echo esc_html( __( 'Operating system', 'stats4wp' ) ); ?></p>
				<?php
				$platforms      = $wpdb->get_results( $wpdb->prepare( "SELECT platform,count(*) as nb FROM {$wpdb->stats4wp_visitor} where device!='bot' AND last_counter BETWEEN %s AND %s group by platform order by 2 DESC", $param['from'], $param['to'] ) );
				$platform_total = array_sum( array_column( $platforms, 'nb' ) );
				$platform_nb    = 0;
				echo '<p>';
				foreach ( $platforms as $platform ) {
					$platform_nb++;
					$percent = round( $platform->nb * 100 / $platform_total );
					if ( $platform_nb < 10 && $percent > 0 ) {
						echo esc_html( $platform->platform . " ($percent%) - " );
					}
				}
				echo esc_html( __( 'All', 'stats4wp' ) ) . '</p>';
				?>
			</div>
			<div class="stats4wp-inline width30 border">
				<p class="title"><?php echo esc_html( __( 'Device', 'stats4wp' ) ); ?></p>
				<?php
				$devices      = $wpdb->get_results( $wpdb->prepare( "SELECT device,count(*) as nb FROM {$wpdb->stats4wp_visitor} where device not in ('bot','') AND last_counter BETWEEN %s AND %s group by device order by 2 DESC", $param['from'], $param['to'] ) );
				$device_total = array_sum( array_column( $devices, 'nb' ) );
				$device_nb    = 0;
				echo '<p>';
				foreach ( $devices as $device ) {
					$device_nb++;
					$percent = round( $device->nb * 100 / $device_total );
					if ( $device_nb < 10 && $percent > 0 ) {
						echo esc_html( $device->device . " ($percent%) - " );
					}
				}
				echo esc_html( __( 'All', 'stats4wp' ) ) . '</p>';
				?>
			</div>
		</div>
		<div class="stats4wp-rows">
			<div class="stats4wp-inline width46 border">
				<p class="title"><?php echo esc_html( __( 'Location', 'stats4wp' ) ); ?></p>
				<?php
				$locations      = $wpdb->get_results( $wpdb->prepare( "SELECT location,count(*) as nb FROM {$wpdb->stats4wp_visitor} where device!='bot' AND last_counter BETWEEN %s AND %s group by location order by 2 DESC", $param['from'], $param['to'] ) );
				$location_total = array_sum( array_column( $locations, 'nb' ) );
				$location_nb    = 0;
				echo '<p>';
				foreach ( $locations as $location ) {
					$location_nb++;
					$percent = round( $location->nb * 100 / $location_total );
					if ( $location_nb < 10 && $percent > 0 ) {
						echo esc_html( $location->location . " ($percent%) - " );
					}
				}
				echo esc_html( __( 'All', 'stats4wp' ) ) . '</p>';
				?>
			</div>
			<div class="stats4wp-inline width46 border">
				<p class="title"><?php echo esc_html( __( 'Language', 'stats4wp' ) ); ?></p>
				<?php
				$languages      = $wpdb->get_results( $wpdb->prepare( "SELECT language,count(*) as nb FROM {$wpdb->stats4wp_visitor} where device!='bot' and language!='' AND last_counter BETWEEN %s AND %s group by language order by 2 DESC", $param['from'], $param['to'] ) );
				$language_total = array_sum( array_column( $languages, 'nb' ) );
				$language_nb    = 0;
				echo '<p>';
				foreach ( $languages as $language ) {
					$language_nb++;
					$percent = round( $language->nb * 100 / $language_total );
					if ( $language_nb < 10 && $percent > 0 ) {
						echo esc_html( $language->language . " ($percent%) - " );
					}
				}
				echo '<a href="/wp-admin/admin.php?page=stats4wp_visitors&spage=lang" >' . esc_html( __( 'All', 'stats4wp' ) ) . '</a></p>';
				?>
			</div>
		</div>
		<div class="stats4wp-rows">
			<div class="stats4wp-inline width94 border">
				<p class="title"><?php echo esc_html( __( 'Bot', 'stats4wp' ) ); ?></p>
				<?php
				$bots      = $wpdb->get_results( $wpdb->prepare( "SELECT agent,count(*) as nb FROM {$wpdb->stats4wp_visitor} where device='bot' AND last_counter BETWEEN %s AND %s group by agent order by 2 DESC", $param['from'], $param['to'] ) );
				$bot_total = array_sum( array_column( $bots, 'nb' ) );
				$bot_nb    = 0;
				echo '<p>';
				foreach ( $bots as $bot ) {
					$bot_nb++;
					$percent = round( $bot->nb * 100 / $bot_total );
					if ( $bot_nb < 10 && $percent > 0 ) {
						echo esc_html( $bot->agent . " ($percent%) - " );
					}
				}
				echo '</p>';
				?>
			</div>
		</div>
	</div>
	<?php
}
