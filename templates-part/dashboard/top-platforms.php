<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.5
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

?>
<div id="stats4wp-platforms-widget" class="postbox ">
	<div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php esc_html_e( 'Top Platforms', 'stats4wp' ); ?></h2>
	</div>
	<div class="inside">
	<canvas  id="chartjs_top_platforms" height="150vw" width="200vw"></canvas> 
	</div>
</div>
<?php
if ( DB::exist_row( 'visitor' ) ) {
	$param = AdminGraph::getdate( '' );
	if ( ! isset( $wpdb->stats4wp_visitor ) ) {
		$wpdb->stats4wp_visitor = DB::table( 'visitor' );}
	$platforms = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT platform, count(*) as nb
        FROM {$wpdb->stats4wp_visitor} 
        where device!='bot' 
        AND last_counter BETWEEN %s AND %s
        GROUP BY 1 ORDER BY 2 DESC LIMIT 10",
			$param['from'],
			$param['to']
		)
	);
	foreach ( $platforms as $platform ) {
		$type_local[] = $platform->platform;
		$nb[]         = $platform->nb;
	}
	$script_js = ' 
    
    const dataTopPlatforms= {
        labels: ' . wp_json_encode( $type_local ) . ',
        datasets: [{
          label: "' . esc_html( __( 'Platforms', 'stats4wp' ) ) . '",
          data: ' . wp_json_encode( $nb ) . ',
          backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
          hoverOffset: 4
        }]
      };

    const optionsTopPlatforms = {
      responsive: true,
      plugins: {
          title: {
              display: false,
              text: "' . esc_html( __( 'Platforms TOP 10', 'stats4wp' ) ) . '"
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

    const configTopPlatforms = {
      type: "doughnut",
      data: dataTopPlatforms,
      options: optionsTopPlatforms,
    };

    // render init block
    const myChartTopPlatforms = new Chart(
      document.getElementById("chartjs_top_platforms"),
      configTopPlatforms
    );

    ';
	wp_add_inline_script( 'chart-js', $script_js );

	unset( $browsers, $type_local, $nb );
}
