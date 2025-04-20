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

$page_local = ( isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '' );
if ( 'stats4wp_plugin' === $page_local ) {
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
				<div class="stats4wp-os">
					<?php
					$os_versions = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT platform, platform_v as version, count(*) as nb
                        FROM {$wpdb->stats4wp_visitor}
                        WHERE device !='bot' 
                        AND last_counter BETWEEN %s AND %s
                        GROUP BY 1,2 ORDER BY 1,3 DESC ",
							$param['from'],
							$param['to']
						)
					);
					$os_total    = array_sum( array_column( $os_versions, 'nb' ) );
					$os_nb       = 1;
					$os_local    = '';
					echo '<table class="widefat table-stats stats4wp-report-table" style="width: 90%;">
                        <tbody>
                            <tr>
                                <td style="width: 1%;"></td>
                                <td>' . esc_html( __( 'Os', 'stats4wp' ) ) . '</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 10%;"></td>
                            </tr>';
					foreach ( $os_versions as $os_version ) {
						if ( $os_local !== $os_version->platform ) {
							$os_nb = 1;
							echo '<tr><th colspan="4">' . esc_html( $os_version->platform ) . '</th></tr>';
						}
						$percent = round( $os_version->nb * 100 / $os_total, 2 );
						echo '<tr><td>' . esc_html( $os_nb ) . '</td><td>' . esc_html( substr( $os_version->version, 0, 50 ) ) . '</td><td>' . esc_html( number_format( $os_version->nb, 0, ',', ' ' ) ) . '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr( $percent ) . '%;"></div>' . esc_html( $percent ) . '%</td></tr>';
						$os_local = $os_version->platform;
						++$os_nb;
					}
					echo '</tbody>
                    </table>';
					?>
				</div>
			</div>
			<div class="stats4wp-inline width46 ">
				<canvas id="chartjs_os" height="300vw" width="400vw"></canvas> 
				<?php
				switch ( $param['interval'] ) {
					case 'days':
						$wpdb->stats4wp_select = 'last_counter as z';
						$char_title            = __( 'OS per days', 'stats4wp' );
						break;
					case 'weeks':
						$wpdb->stats4wp_select = 'CONCAT(YEAR(last_counter),".",WEEK(last_counter)) as z';
						$char_title            = __( 'OS per weeks', 'stats4wp' );
						break;
					case 'month':
						$wpdb->stats4wp_select = 'CONCAT(YEAR(last_counter),".",MONTH(last_counter)) as z';
						$char_title            = __( 'OS per months', 'stats4wp' );
						break;
				}
				$oss = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT z as d,
                    SUM(CASE WHEN platform = 'Windows' THEN nb END) windows,
                    SUM(CASE WHEN platform = 'Ubuntu' THEN nb END) ubuntu ,
                    SUM(CASE WHEN platform = 'OS X' THEN nb END) osx ,
                    SUM(CASE WHEN platform = 'Linux' THEN nb END) linux ,
                    SUM(CASE WHEN platform = 'Fedora' THEN nb END) fedora ,
                    SUM(CASE WHEN platform = 'Ubuntu' THEN nb END) ubuntu ,
                    SUM(CASE WHEN platform = 'iOS' THEN nb END) ios,
                    SUM(CASE WHEN platform = 'Chrome OS' THEN nb END) chromeos,
                    SUM(CASE WHEN platform = 'Android' THEN nb END) android,
                    SUM(CASE WHEN platform NOT IN  ('Windows','Ubuntu','OS X','Linux','Fedora','Ubuntu','iOS','Chrome OS','Android') THEN nb END) other
                    FROM (
                    select {$wpdb->stats4wp_select}, platform, COUNT(*) as nb
                    from {$wpdb->stats4wp_visitor}
                    WHERE device !='bot' 
                    AND last_counter BETWEEN %s AND %s
                    GROUP BY  1, 2
                    ) all_data
                    group by 1",
						$param['from'],
						$param['to']
					)
				);
				foreach ( $oss as $os ) {
					$day[]      = $os->d;
					$windows[]  = ( null === $os->windows ) ? 0 : $os->windows;
					$ubuntu[]   = ( null === $os->ubuntu ) ? 0 : $os->ubuntu;
					$osx[]      = ( null === $os->osx ) ? 0 : $os->osx;
					$linux[]    = ( null === $os->linux ) ? 0 : $os->linux;
					$fedora[]   = ( null === $os->fedora ) ? 0 : $os->fedora;
					$ubuntu[]   = ( null === $os->ubuntu ) ? 0 : $os->ubuntu;
					$ios[]      = ( null === $os->ios ) ? 0 : $os->ios;
					$chromeos[] = ( null === $os->chromeos ) ? 0 : $os->chromeos;
					$android[]  = ( null === $os->android ) ? 0 : $os->android;
					$other[]    = ( null === $os->other ) ? 0 : $os->other;
				}

				$script_js = '
                const dataOs= {
                    labels:' . wp_json_encode( $day ) . ',
                    datasets: [{
                            label: "' . esc_html( __( 'Windows', 'stats4wp' ) ) . '",
                            backgroundColor: [
                            "#36a2eb"
                            ],
                            data:' . wp_json_encode( $windows ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Ubuntu', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#f67019"
                            ],
                            data:' . wp_json_encode( $ubuntu ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'osx', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#f53794"
                            ],
                            data:' . wp_json_encode( $osx ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Linux', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#537bc4"
                            ],
                            data:' . wp_json_encode( $linux ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Fedora', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#537bc4"
                            ],
                            data:' . wp_json_encode( $fedora ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Ubuntu', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#537bc4"
                            ],
                            data:' . wp_json_encode( $ubuntu ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'iOS', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#acc236"
                            ],
                            data:' . wp_json_encode( $ios ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Chrome OS', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#166a8f"
                            ],
                            data:' . wp_json_encode( $chromeos ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Android', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#00a950"
                            ],
                            data:' . wp_json_encode( $android ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Other', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#58595b"
                            ],
                            data:' . wp_json_encode( $other ) . ',
                        }
                    ]
                };
            
                const optionsOs = {
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
            
                const configOs = {
                  type: "bar",
                  data: dataOs,
                  options: optionsOs,
                };
            
                // render init block
                const myChartOs = new Chart(
                  document.getElementById("chartjs_os"),
                  configOs
                );
                ';
				wp_add_inline_script( 'chart-js', $script_js );
				unset( $day, $windows, $ubuntu, $safari, $edge, $chrome, $chromeos, $android, $other );
				?>
			</div>
		</div>
	</div>
	<?php
}
