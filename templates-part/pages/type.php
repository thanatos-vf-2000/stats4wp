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

if ( DB::exist_row( 'pages' ) ) {
	$param = AdminGraph::getdate( $data );
	?>
	<div class="stats4wp-dashboard">
		<div class="stats4wp-rows">
			<div class="stats4wp-inline width46 ">
			<canvas  id="chartjs_type" height="300vw" width="400vw"></canvas> 
				<?php
				if ( ! isset( $wpdb->stats4wp_pages ) ) {
					$wpdb->stats4wp_pages = DB::table( 'pages' );
				}
				$types      = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT type, count(*) as nb FROM {$wpdb->stats4wp_pages} 
                    WHERE date BETWEEN %s AND %s
                    AND type!='unknown'
                    GROUP BY type
                    ORDER by nb DESC",
						$param['from'],
						$param['to']
					)
				);
				$type_total = array_sum( array_column( $types, 'nb' ) );
				$type_nb    = 0;
				$type_list  = '<table class="widefat table-stats stats4wp-report-table">
                    <tbody>
                        <tr>
                            <td style="width: 1%;"></td>
                            <td>' . esc_html( __( 'Type', 'stats4wp' ) ) . '</td>
                            <td style="width: 20%;"></td>
                            <td style="width: 20%;"></td>
                        </tr>';
				foreach ( $types as $type_local ) {
					if ( $type_nb < 10 ) {
						$t[]  = $type_local->type;
						$nb[] = ( null === $type_local->nb ) ? 0 : $type_local->nb;
					}
					++$type_nb;
					$tr_class   = ( 0 === $type_nb % 2 ) ? 'stats4wp-bg' : '';
					$percent    = round( $type_local->nb * 100 / $type_total, 2 );
					$type_list .= '<tr class="' . esc_attr( $tr_class ) . '"><td>' . esc_html( $type_nb ) . '</td><td>' . esc_html( $type_local->type ) . '</td><td class="stats4wp-right">' . esc_html( number_format( $type_local->nb, 0, ',', ' ' ) ) . '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr( $percent ) . '%;"></div>' . esc_html( $percent ) . '%</td></tr>';
				}
				$type_list .= '</tbody>
                    </table>';
				$script_js  = '
                
                const dataType= {
                    labels:' . wp_json_encode( $t ) . ',
                    datasets: [{
                        label: "' . esc_html( __( 'Type', 'stats4wp' ) ) . '",
                        data:' . wp_json_encode( $nb ) . ',
                        backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
                    }]
                };
            
                const optionsType = {
                    responsive: false,
                    plugins: {
                        title: {
                          display: true,
                          text: "' . esc_html( __( 'Type TOP 10', 'stats4wp' ) ) . '"
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
            
                const configType = {
                  type: "doughnut",
                  data: dataType,
                  options: optionsType,
                };
            
                // render init block
                const myChartType = new Chart(
                  document.getElementById("chartjs_type"),
                  configType
                );
                
                ';
				wp_add_inline_script( 'chart-js', $script_js );
				unset( $t, $nb );
				?>
			</div>
			<div class="stats4wp-inline width46">
				<div class="stats4wp-type">
					<?php echo wp_kses_post( $type_list ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
