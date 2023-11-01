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
use STATS4WP\Api\TimeZone;
use STATS4WP\Core\Options;

?>
<div id="stats4wp-topvisitors-widget" class="postbox ">
	<div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php esc_html_e( 'Top 10 visitors today', 'stats4wp' ); ?></h2>
	</div>
	<div class="inside">
	<table width="100%" class="widefat table-stats stats4wp-report-table">
		<tbody>
			<tr>
				<td><?php esc_html_e( 'ID', 'stats4wp' ); ?></td>
				<td><?php esc_html_e( 'Views', 'stats4wp' ); ?></td>
				<td><?php esc_html_e( 'IP', 'stats4wp' ); ?></td>
				<td><?php esc_html_e( 'Browser', 'stats4wp' ); ?></td>
				<td><?php esc_html_e( 'OS', 'stats4wp' ); ?></td>
				<td><?php esc_html_e( 'Type', 'stats4wp' ); ?></td>
			</tr>
			<?php
			if ( ! isset( $wpdb->stats4wp_visitor ) ) {
				$wpdb->stats4wp_visitor = DB::table( 'visitor' );}
			$top_visitors = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT *
                FROM {$wpdb->stats4wp_visitor} 
                WHERE last_counter=%s 
                AND device!='bot' 
                ORDER BY hits  DESC LIMIT 10",
					TimeZone::get_current_date( 'Y-m-d' )
				)
			);
			$i            = 1;
			foreach ( $top_visitors as $top_visitor ) {
				$views   = $top_visitor->hits;
				$ip      = ( true === Options::get_option( 'anonymize_ips' ) ) ? __( 'None', 'stats4wp' ) : $top_visitor->ip;
				$browser = $top_visitor->agent . ' - ' . $top_visitor->agent_v;
				$os      = $top_visitor->platform . ' - ' . $top_visitor->platform_v;
				$device  = $top_visitor->device;
				echo '<tr>
                    <td>' . esc_html( $i ) . '</td>
                    <td>' . esc_html( $views ) . '</td>
                    <td>' . esc_html( $ip ) . '</td>
                    <td>' . esc_html( $browser ) . '</td>
                    <td>' . esc_html( $os ) . '</td>
                    <td>' . esc_html( $device ) . '</td>
                </tr>';
				$i++;
			}
			unset( $top_visitors, $i, $views, $ip, $browser, $os );
			?>
		</tbody>
	</table>
	</div>
</div>
