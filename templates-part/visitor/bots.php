<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.7
 *
 * Desciption: Bots
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
$all_data = ( isset( $_GET['data'] ) ) ? sanitize_text_field( wp_unslash( $_GET['data'] ) ) : '';

?>
<div class="stats4wp-dashboard">
	<div class="stats4wp-rows">
		<ul id="stats4wp-menu" >
			<li><a class="<?php echo ( '' === $all_data ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=bots&data=all" ><?php echo esc_html( __( 'All Bots', 'stats4wp' ) ); ?></a></li>
			<li><a class="<?php echo ( '' !== $all_data ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=bots" ><?php echo esc_html( __( 'TOP 10 Bots', 'stats4wp' ) ); ?></a></li>
		</ul>  
	</div>
	<div class="stats4wp-rows">
		<canvas  id="chartjs_bots" height="300vw" width="400vw"></canvas> 
	</div>
</div>
<?php
if ( DB::exist_row( 'visitor' ) ) {
	$param = AdminGraph::getdate( $data );
	if ( ! isset( $wpdb->stats4wp_visitor ) ) {
		$wpdb->stats4wp_visitor = DB::table( 'visitor' );
	}
	if ( 'all' === $all_data ) {
		$bots        = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT agent, COUNT(*) AS nb 
			FROM {$wpdb->stats4wp_visitor}
			where device='bot' 
			AND last_counter BETWEEN %s AND %s
			GROUP BY 1 ORDER by 2 DESC",
				$param['from'],
				$param['to']
			)
		);
		$title_local = __( 'All Bots', 'stats4wp' );
	} else {
		$bots        = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT agent, COUNT(*) AS nb 
			FROM {$wpdb->stats4wp_visitor}
			where device='bot' 
			AND last_counter BETWEEN %s AND %s
			GROUP BY 1 ORDER by 2 DESC LIMIT 0,10",
				$param['from'],
				$param['to']
			)
		);
		$title_local = __( 'TOP 10 Bots', 'stats4wp' );
	}

	foreach ( $bots as $bot ) {
		$bot_agent[] = $bot->agent;
		$nb[]        = $bot->nb;
	}

	$script_js = ' 
    const dataBots= {
		labels:' . wp_json_encode( $bot_agent ) . ',
		datasets: [{
			label: "' . esc_html( __( 'Bots', 'stats4wp' ) ) . '",
			borderColor: "#05419ad6",
			fill: false,
			cubicInterpolationMode: "monotone",
			tension: 0.4,
			backgroundColor: [
			   "#05419ad6"
			],
			data:' . wp_json_encode( $nb ) . ',
		}]
	};

    const optionsBots = {
		responsive: false,
		plugins: {
			title: {
			  display: true,
			  text: "' . esc_html( $title_local ) . '"
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

    const configBots = {
      type: "bar",
      data: dataBots,
      options: optionsBots,
    };

    // render init block
    const myChartBots = new Chart(
      document.getElementById("chartjs_bots"),
      configBots
    );
                
    ';
	wp_add_inline_script( 'chart-js', $script_js );
	unset( $day, $nb );
}
