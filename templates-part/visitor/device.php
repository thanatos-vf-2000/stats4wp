<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
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
		$wpdb->stats4wp_visitor = DB::table( 'visitor' );
	}
	?>
	<div class="stats4wp-dashboard">
		<div class="stats4wp-rows">
			<div class="stats4wp-inline width46 ">
				<canvas id="chartjs_devices" height="300vw" width="400vw"></canvas> 
				<?php
				switch ( $param['interval'] ) {
					case 'days':
						$wpdb->stats4wp_select = 'last_counter as z';
						$char_title            = __( 'Device per days', 'stats4wp' );
						break;
					case 'weeks':
						$wpdb->stats4wp_select = 'CONCAT(YEAR(last_counter),".",WEEK(last_counter)) as z';
						$char_title            = __( 'Device per weeks', 'stats4wp' );
						break;
					case 'month':
						$wpdb->stats4wp_select = 'CONCAT(YEAR(last_counter),".",MONTH(last_counter)) as z';
						$char_title            = __( 'Device per months', 'stats4wp' );
						break;
				}
				$devices = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT z as d,
                    SUM(CASE WHEN device =  'desktop' THEN nb END) desktop,
                    SUM(CASE WHEN device =  'mobile' THEN nb END) mobile ,
                    SUM(CASE WHEN device =  'tablet' THEN nb END) tablet ,
                    SUM(CASE WHEN device =  'gaming' THEN nb END) gaming ,
                    SUM(CASE WHEN device =  'television ' THEN nb END) television ,
                    SUM(CASE WHEN device =  'pda' THEN nb END) pda ,
                    SUM(CASE WHEN device NOT IN  ('desktop','mobile','tablet','gaming','television','pda') THEN nb END) other
                    FROM (
                    select {$wpdb->stats4wp_select}, device, COUNT(*) as nb
                    from {$wpdb->stats4wp_visitor} 
                    WHERE device NOT IN ('bot','') 
                    AND last_counter BETWEEN %s AND %s
                    GROUP BY  1, 2
                    ) all_data
                    group by 1",
						$param['from'],
						$param['to']
					)
				);
				foreach ( $devices as $device ) {
					$day[]        = $device->d;
					$desktop[]    = ( null === $device->desktop ) ? 0 : $device->desktop;
					$mobile[]     = ( null === $device->mobile ) ? 0 : $device->mobile;
					$tablet[]     = ( null === $device->tablet ) ? 0 : $device->tablet;
					$gaming[]     = ( null === $device->gaming ) ? 0 : $device->gaming;
					$television[] = ( null === $device->television ) ? 0 : $device->television;
					$pda[]        = ( null === $device->pda ) ? 0 : $device->pda;
					$other[]      = ( null === $device->other ) ? 0 : $device->other;
				}

				$script_js = '
                const dataDevices= {
                    labels:' . wp_json_encode( $day ) . ',
                    datasets: [{
                            label: "' . esc_html( __( 'Desktop', 'stats4wp' ) ) . '",
                            backgroundColor: [
                            "#36a2eb"
                            ],
                            data:' . wp_json_encode( $desktop ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Mobile', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#f67019"
                            ],
                            data:' . wp_json_encode( $mobile ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Tablet', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#f53794"
                            ],
                            data:' . wp_json_encode( $tablet ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Gaming', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#166a8f"
                            ],
                            data:' . wp_json_encode( $gaming ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Television', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#00a950"
                            ],
                            data:' . wp_json_encode( $television ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Pda', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#00a950"
                            ],
                            data:' . wp_json_encode( $pda ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Other', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#537bc4"
                            ],
                            data:' . wp_json_encode( $other ) . ',
                        }
                    ]
                };
            
                const optionsDevices = {
                    responsive: false,
                    scales: {
                        x: {
                          stacked: true,
                        },
                        y: {
                          stacked: true
                        }
                      },
                    plugins: {
                        title: {
                          display: true,
                          text: "' . esc_html( $char_title ) . '"
                        },
                      },
                    legend: {
                        display: true,
                        position: "bottom",
                        labels: {
                            fontColor: "#05419ad6",
                            fontFamily: "Circular Std Book",
                            fontSize: 14,
                        }
                    },
                };
            
                const configDevices = {
                  type: "bar",
                  data: dataDevices,
                  options: optionsDevices,
                };
            
                // render init block
                const myChartDevices = new Chart(
                  document.getElementById("chartjs_devices"),
                  configDevices
                );
                
                ';
				wp_add_inline_script( 'chart-js', $script_js );
				unset( $day, $desktop, $mobile, $tablet, $other );
				?>
			</div>
			<div class="stats4wp-inline width46 ">
				<div class="stats4wp-device">
					<?php
					$devices_version = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT device, manufacturer as version, count(*) as nb
                        FROM {$wpdb->stats4wp_visitor}
                        WHERE device NOT in ('bot','') 
                        AND last_counter BETWEEN %s AND %s
                        GROUP BY 1,2 ORDER BY 1,3 DESC ",
							$param['from'],
							$param['to']
						)
					);
					$device_total    = array_sum( array_column( $devices_version, 'nb' ) );
					$device_nb       = 1;
					$device_local    = '';
					echo '<table class="widefat table-stats stats4wp-report-table">
                        <tbody>
                            <tr>
                                <td style="width: 1%;"></td>
                                <td>' . esc_html( __( 'Devices', 'stats4wp' ) ) . '</td>
                                <td style="width: 20%;">' . esc_html( __( 'Users', 'stats4wp' ) ) . '</td>
                                <td style="width: 20%;">' . esc_html( __( '% Users', 'stats4wp' ) ) . '</td>
                            </tr>';
					foreach ( $devices_version as $device_version ) {
						if ( $device_local !== $device_version->device ) {
							$device_nb = 1;
							echo '<tr><th colspan="4">' . esc_html( $device_version->device ) . '</th></tr>';
						}
						$tr_class = ( 0 === $device_nb % 2 ) ? 'stats4wp-bg' : '';
						$percent  = round( $device_version->nb * 100 / $device_total, 2 );
						echo '<tr class="' . esc_attr( $tr_class ) . '"><td>' . esc_html( $device_nb ) . '</td><td>' . esc_html( substr( $device_version->version, 0, 50 ) ) . '</td><td class="stats4wp-right">' . esc_html( number_format( $device_version->nb, 0, ',', ' ' ) ) . '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr( $percent ) . '%;"></div>' . esc_html( $percent ) . '%</td></tr>';
						$device_local = $device_version->device;
						++$device_nb;
					}
					echo '</tbody>
                    </table>';
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
