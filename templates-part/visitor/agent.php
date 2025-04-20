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
				<canvas id="chartjs_agents" height="300vw" width="400vw"></canvas> 
				<?php
				switch ( $param['interval'] ) {
					case 'days':
						$wpdb->stats4wp_select = 'last_counter as z';
						$char_title            = __( 'Agents per days', 'stats4wp' );
						break;
					case 'weeks':
						$wpdb->stats4wp_select = 'CONCAT(YEAR(last_counter),".",WEEK(last_counter)) as z';
						$char_title            = __( 'Agents per weeks', 'stats4wp' );
						break;
					case 'month':
						$wpdb->stats4wp_select = 'CONCAT(YEAR(last_counter),".",MONTH(last_counter)) as z';
						$char_title            = __( 'Agents per months', 'stats4wp' );
						break;
				}
				$agents = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT z as d,
                    SUM(CASE WHEN agent =  'Internet Explorer' THEN nb END) ie,
                    SUM(CASE WHEN agent =  'Firefox' OR agent =  'Firefox Mobile' THEN nb END) firefox ,
                    SUM(CASE WHEN agent =  'Safari' THEN nb END) safari ,
                    SUM(CASE WHEN agent =  'Edge' THEN nb END) edge ,
                    SUM(CASE WHEN agent =  'Chrome' OR agent =  'Chromium' THEN nb END) chrome,
                    SUM(CASE WHEN agent =  'Opera' OR agent =  'Opera Mobile' THEN nb END) opera,
                    SUM(CASE WHEN agent =  'Samsung Internet' THEN nb END) samsungie,
                    SUM(CASE WHEN agent NOT IN  ('Internet Explorer','Firefox','Firefox Mobile','Safari','Edge','Chrome','Chromium','Opera','Opera Mobile','Samsung Internet') THEN nb END) other
                    FROM (
                    select {$wpdb->stats4wp_select}, agent, COUNT(*) as nb
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
				foreach ( $agents as $agent ) {
					$day[]       = $agent->d;
					$ie[]        = ( null === $agent->ie ) ? '0' : $agent->ie;
					$firefox[]   = ( null === $agent->firefox ) ? '0' : $agent->firefox;
					$safari[]    = ( null === $agent->safari ) ? '0' : $agent->safari;
					$edge[]      = ( null === $agent->edge ) ? '0' : $agent->edge;
					$chrome[]    = ( null === $agent->chrome ) ? '0' : $agent->chrome;
					$opera[]     = ( null === $agent->opera ) ? '0' : $agent->opera;
					$samsungie[] = ( null === $agent->samsungie ) ? '0' : $agent->samsungie;
					$other[]     = ( null === $agent->other ) ? '0' : $agent->other;
				}

				$script_js = ' 
              
                const dataAgentsChart= {
                    labels:' . wp_json_encode( $day ) . ',
                    datasets: [{
                            label: "' . esc_html( __( 'Internet Explorer', 'stats4wp' ) ) . '",
                            backgroundColor: [
                            "#36a2eb"
                            ],
                            data:' . wp_json_encode( $ie ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Firefox', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#f67019"
                            ],
                            data:' . wp_json_encode( $firefox ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Safari', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#f53794"
                            ],
                            data:' . wp_json_encode( $safari ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Edge', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#537bc4"
                            ],
                            data:' . wp_json_encode( $edge ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Chrome', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#acc236"
                            ],
                            data:' . wp_json_encode( $chrome ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Opera', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#166a8f"
                            ],
                            data:' . wp_json_encode( $opera ) . ',
                        },
                        {
                            label: "' . esc_html( __( 'Samsung Internet', 'stats4wp' ) ) . '",
                            backgroundColor: [
                               "#00a950"
                            ],
                            data:' . wp_json_encode( $samsungie ) . ',
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
            
                const optionsAgentsChart = {
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
            
                const configAgentsChart = {
                  type: "bar",
                  data: dataAgentsChart,
                  options: optionsAgentsChart,
                };
            
                // render init block
                const myChartAgents = new Chart(
                  document.getElementById("chartjs_agents"),
                  configAgentsChart
                );
                
                ';
				wp_add_inline_script( 'chart-js', $script_js );
				unset( $day, $ie, $firefox, $safari, $edge, $chrome, $opera, $samsungie, $other );
				?>
			</div>
			<div class="stats4wp-inline width46 ">
				<div class="stats4wp-agents">
					<?php
					$agents_version = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT agent, agent_v as version, count(*) as nb
                        FROM {$wpdb->stats4wp_visitor}
                        WHERE device !='bot' 
                        AND last_counter BETWEEN %s AND %s
                        GROUP BY 1,2 ORDER BY 1,3 DESC ",
							$param['from'],
							$param['to']
						)
					);
					$agents_total   = array_sum( array_column( $agents_version, 'nb' ) );
					$agents_nb      = 1;
					$agent_local    = '';
					echo '<table class="widefat table-stats stats4wp-report-table">
                        <tbody>
                            <tr>
                                <td style="width: 1%;"></td>
                                <td>' . esc_html( __( 'Navigator', 'stats4wp' ) ) . '</td>
                                <td style="width: 10%;"></td>
                            </tr>';
					foreach ( $agents_version as $agent_version ) {
						if ( $agent_local !== $agent_version->agent ) {
							$agents_nb = 1;
							echo '<tr><th colspan="3">' . esc_html( $agent_version->agent ) . '</th></tr>';
						}
						$percent = round( $agent_version->nb * 100 / $agents_total, 2 );
						echo '<tr><td>' . esc_html( $agents_nb ) . '</td><td>' . esc_html( substr( $agent_version->version, 0, 50 ) ) . '</td><td>' . esc_html( number_format( $agent_version->nb, 0, ',', ' ' ) ) . '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr( $percent ) . '%;"></div>' . esc_html( $percent ) . '%</td></tr>';
						$agent_local = $agent_version->agent;
						++$agents_nb;
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
