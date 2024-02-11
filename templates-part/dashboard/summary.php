<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.6
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

if ( ! isset( $wpdb->stats4wp_useronline ) ) {
	$wpdb->stats4wp_useronline = DB::table( 'useronline' );}

$user_online = $wpdb->get_row( "SELECT COUNT(*) as nb FROM $wpdb->stats4wp_useronline" );

?>
<div id="stats4wp-summary-widget" class="postbox">
	<div class="postbox-header">
		<h2 class="hndle ui-sortable-handle"><?php esc_html_e( 'Summary', 'stats4wp' ); ?></h2>
	</div>
	<div class="inside">
		<table width="100%" class="widefat table-stats stats4wp-summary-stats">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Online users', 'stats4wp' ); ?>:</th>
					<th colspan="2" id="th-colspan">
						<span><?php echo esc_html( $user_online->nb ); ?></span>
					</th>
				</tr>
				<tr>
					<th width="60%"></th>
					<th class="th-center"><?php esc_html_e( 'Visitors', 'stats4wp' ); ?></th>
					<th class="th-center"><?php esc_html_e( 'Visits', 'stats4wp' ); ?></th>
				</tr>
				<?php
				$nb  = 0;
				$max = 0;
				while ( ++$nb < 9 ) {
					switch ( $nb ) {
						case 1:
							$to          = $from = gmdate( 'Y-m-d' );
							$title_local = esc_html__( 'Today', 'stats4wp' );
							break;
						case 2:
							$from        = $to = gmdate( 'Y-m-d', strtotime( '-1 days' ) );
							$title_local = esc_html__( 'Yesterday', 'stats4wp' );
							break;
						case 3:
							$to          = gmdate( 'Y-m-d' );
							$from        = gmdate( 'Y-m-d', strtotime( '-7 days' ) );
							$title_local = esc_html__( 'Last 7 Days (Week)', 'stats4wp' );
							break;
						case 4:
							$to          = gmdate( 'Y-m-d' );
							$from        = gmdate( 'Y-m-d', strtotime( '-14 days' ) );
							$title_local = esc_html__( 'Last 14 Days (2 Week)', 'stats4wp' );
							break;
						case 5:
							$to          = gmdate( 'Y-m-d' );
							$from        = gmdate( 'Y-m-d', strtotime( '-1 months' ) );
							$title_local = esc_html__( 'Last 30 Days (Month)', 'stats4wp' );
							break;
						case 6:
							$to          = gmdate( 'Y-m-d' );
							$from        = gmdate( 'Y-m-d', strtotime( '-6 months' ) );
							$title_local = esc_html__( 'Last 6 Month', 'stats4wp' );
							break;
						case 7:
							$to          = gmdate( 'Y-m-d' );
							$from        = gmdate( 'Y-m-d', strtotime( '-1 years' ) );
							$title_local = esc_html__( 'Last 365 Days (Year)', 'stats4wp' );
							break;
						case 8:
							$to          = gmdate( 'Y-m-d' );
							$from        = '1999-01-01';
							$title_local = esc_html__( 'Total', 'stats4wp' );
							break;
					}
					if ( ! isset( $wpdb->stats4wp_visitor ) ) {
						$wpdb->stats4wp_visitor = DB::table( 'visitor' );}
					$summary_users = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT count(*) as visitors,SUM(hits) as visits 
							FROM $wpdb->stats4wp_visitor 
							WHERE device!='bot' 
							AND location != 'local'
							AND last_counter BETWEEN %s AND %s",
							array(
								$from,
								$to,
							)
						)
					);
					if ( $max !== $summary_users->visitors || 8 === $nb ) {
						echo '<tr>
                        <th>' . esc_html( $title_local ) . ': </th>
                        <th class="th-center">
                            <span>' . esc_html( $summary_users->visitors ) . '</span>
                        </th>
                        <th class="th-center">
                            <span>' . esc_html( $summary_users->visits ) . '</span>
                        </th>
                    </tr>';
					}
					$max = $summary_users->visitors;
				}
				?>
			<tr>
				<th colspan="3"><br><hr></th>
			</tr>
			<tr>
				<th colspan="3" style="text-align: center;"><?php esc_html_e( 'Search engine redirects', 'stats4wp' ); ?></th>
			</tr>
			<tr>
				<th width="60%"></th>
				<th class="th-center"><?php esc_html_e( 'Today', 'stats4wp' ); ?></th>
				<th class="th-center"><?php esc_html_e( 'Yesterday', 'stats4wp' ); ?></th>
				<th class="th-center"><?php esc_html_e( 'Monthly', 'stats4wp' ); ?></th>
			</tr>
			<?php
			$nb          = 0;
			$total_bot_t = 0;
			$total_bot_y = 0;
			$total_bot_m = 0;
			$display     = 0;
			while ( ++$nb < 9 ) {
				switch ( $nb ) {
					case 1:
						$search_local = 'bing';
						$title_local  = 'Bing';
						break;
					case 2:
						$search_local = 'duckduckgo';
						$title_local  = 'DuckDuckGo';
						break;
					case 3:
						$search_local = 'google';
						$title_local  = 'Google';
						break;
					case 4:
						$search_local = 'yahoo';
						$title_local  = 'Yahoo!';
						break;
					case 5:
						$search_local = 'yandex';
						$title_local  = 'Yandex';
						break;
					case 6:
						$search_local = 'lilo';
						$title_local  = 'LiLo';
						break;
					case 7:
						$search_local = 'searchbip';
						$title_local  = 'SearchBip';
						break;
					case 8:
						$search_local = 'qwant';
						$title_local  = 'Qwant';
						break;
					case 9:
						$search_local = 'ecosia';
						$title_local  = 'Ecosia';
						break;
				}
				$local_day       = gmdate( 'Y-m-d' );
				$bot_today       = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT count(*) as nb 
                        FROM $wpdb->stats4wp_visitor
						WHERE device!='bot' 
                        AND last_counter = %s 
                        AND referred like %s",
						array(
							$local_day,
							'%' . $search_local . '%',
						)
					)
				);
				$local_yesterday = gmdate( 'Y-m-d', strtotime( '-1 days' ) );
				$bot_yesterday   = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT count(*) as nb 
						FROM $wpdb->stats4wp_visitor
						WHERE device!='bot' 
						AND last_counter = %s 
						AND referred like %s",
						array(
							$local_yesterday,
							'%' . $search_local . '%',
						)
					)
				);
				$local_month     = gmdate( 'Y-m-d', strtotime( '-1 months' ) );
				$bot_month       = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT count(*) as nb 
                    FROM $wpdb->stats4wp_visitor
					WHERE device!='bot' 
                    AND last_counter > %s 
                    AND referred like %s",
						array(
							$local_month,
							'%' . $search_local . '%',
						)
					)
				);
				if ( 0 !== $bot_today->nb || 0 !== $bot_yesterday->nb || 0 !== $bot_month->nb ) {
					echo '<tr>
                        <th>
                            <img src="' . esc_attr( STATS4WP_URL ) . '/assets/images/search-engine/' . esc_attr( $search_local ) . '.png" alt="' . esc_attr( $title_local ) . '" class="stats4wp-engine-logo"> ' . esc_html( $title_local ) . ':</th>
                        <th class="th-center">
                            <span>' . esc_html( $bot_today->nb ) . '</span>
                        </th>
                        <th class="th-center">
                            <span>' . esc_html( $bot_yesterday->nb ) . '</span>
                        </th>
                        <th class="th-center">
                            <span>' . esc_html( $bot_month->nb ) . '</span>
                        </th>
                    </tr>';
					$display++;
				}
				$total_bot_t += $bot_today->nb;
				$total_bot_y += $bot_yesterday->nb;
				$total_bot_m += $bot_month->nb;
			}
			if ( $display > 0 ) {
				?>
			<tr>
				<th><?php esc_html_e( 'Daily Total', 'stats4wp' ); ?>:</th>
				<td id="th-colspan" class="th-center">
					<span><?php echo esc_html( $total_bot_t ); ?></span>
				</td>
				<td id="th-colspan" class="th-center">
					<span><?php echo esc_html( $total_bot_y ); ?></span>
				</td>
				<td id="th-colspan" class="th-center">
					<span><?php echo esc_html( $total_bot_m ); ?></span>
				</td>
			</tr>
				<?php
			} else {
				echo '<tr><th>' . esc_html__( 'No data.', 'stats4wp' ) . '</th><td colspan=3></td></tr>';
			}
			?>
			<tr>
				<th colspan="3"><br><hr></th>
			</tr>
			<tr>
				<th colspan="3" style="text-align: center;"><?php esc_html_e( 'Current time and date', 'stats4wp' ); ?><span id="time_zone">
					<a href="/wp-admin/options-general.php"> (<?php esc_html_e( 'Adjustment', 'stats4wp' ); ?>)</a>
					</span>
				</th>
			</tr>
			<tr>
				<th colspan="3"><?php esc_html_e( 'Dated', 'stats4wp' ); ?>: <code dir="ltr"><?php echo esc_html( gmdate( get_option( 'date_format' ) ) ); ?></code></th>
			</tr>
			<tr>
				<th colspan="3"><?php esc_html_e( 'Time', 'stats4wp' ); ?>: <code dir="ltr"><?php echo esc_html( gmdate( get_option( 'time_format' ) ) ); ?></code></th>
			</tr>
		</tbody></table>
	</div>
</div>
<?php
unset( $user_online, $summary_users, $nb );
