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

?>
<div class="stats4wp-dashboard">
	<div class="stats4wp-rows">
		<canvas  id="chartjs_nb_visitor" height="300vw" width="400vw"></canvas> 
	</div>
</div>
<?php
if ( DB::exist_row( 'visitor' ) ) {
	$param = AdminGraph::getdate( $data );
	if ( ! isset( $wpdb->stats4wp_visitor ) ) {
		$wpdb->stats4wp_visitor = DB::table( 'visitor' );}
	switch ( $param['interval'] ) {
		case 'days':
			$select     = 'last_counter as d';
			$char_title = __( 'Number of user per days', 'stats4wp' );
			break;
		case 'weeks':
			$select     = 'CONCAT(YEAR(last_counter),".",WEEK(last_counter)) as d';
			$char_title = __( 'Number of user per weeks', 'stats4wp' );
			break;
		case 'month':
			$select     = 'CONCAT(YEAR(last_counter),".",MONTH(last_counter)) as d';
			$char_title = __( 'Number of user per months', 'stats4wp' );
			break;
	}
	$visitors = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT {$select} ,count(*) as nb 
			FROM {$wpdb->stats4wp_visitor} 
			where device!='bot' 
			AND last_counter BETWEEN %s AND %s group by 1",
			$param['from'],
			$param['to']
		)
	);
	foreach ( $visitors as $visitor ) {
		$day[] = $visitor->d;
		$nb[]  = $visitor->nb;
	}

	$script_js = '
    const dataNbVisitor= {
		labels:' . wp_json_encode( $day ) . ',
		datasets: [{
			label: "' . esc_html( __( 'number of user', 'stats4wp' ) ) . '",
			borderColor: "#05419ad6",
			fill: false,
			pointRadius: [0],
			pointHitRadius: [0],
			cubicInterpolationMode: "monotone",
			tension: 0.4,
			backgroundColor: [
			   "#05419ad6"
			],
			data:' . wp_json_encode( $nb ) . ',
		}]
	};

    const optionsNbVisitor = {
		responsive: false,
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

    const configNbVisitor = {
      type: "line",
      data: dataNbVisitor,
      options: optionsNbVisitor,
    };

    // render init block
    const myChartNbVisitor = new Chart(
      document.getElementById("chartjs_nb_visitor"),
      configNbVisitor
    );
    ';
	wp_add_inline_script( 'chart-js', $script_js );
	unset( $day, $nb );
}
