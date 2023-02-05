<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.8
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

$page = (isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '');
if ($page == 'stats4wp_plugin') {
    $data = 'all';
} else {
    $data ='';
}

?>
<div class="stats4wp-dashboard">
    <div class="stats4wp-rows">
        <canvas  id="chartjs_nb_visitor" height="300vw" width="400vw"></canvas> 
    </div>
</div>
<?php
if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate($data);
    switch($param['interval']) {
        case 'days':
            $select = 'last_counter as d';
            $char_title = __('Number of user per days', 'stats4wp');
            break;
        case 'weeks':
            $select = 'CONCAT(YEAR(last_counter),".",WEEK(last_counter)) as d';
            $char_title = __('Number of user per weeks', 'stats4wp');
            break;
        case 'month':
            $select = 'CONCAT(YEAR(last_counter),".",MONTH(last_counter)) as d';
            $char_title = __('Number of user per months', 'stats4wp');
            break;
    }
    $visitors = $wpdb->get_results("SELECT ".  $select .",count(*) as nb 
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' group by 1");
    foreach ( $visitors as $visitor ) {
        $day[]  = $visitor->d ;
        $nb[] = $visitor->nb;
    }

    $script_js = '
    const dataNbVisitor= {
		labels:'.json_encode($day). ',
		datasets: [{
			label: "'. esc_html(__('number of user', 'stats4wp')) .'",
			borderColor: "#05419ad6",
			fill: false,
			pointRadius: [0],
			pointHitRadius: [0],
			cubicInterpolationMode: "monotone",
			tension: 0.4,
			backgroundColor: [
			   "#05419ad6"
			],
			data:'. json_encode($nb). ',
		}]
	};

    const optionsNbVisitor = {
		responsive: false,
		plugins: {
			title: {
			  display: true,
			  text: "'. $char_title .'"
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
    wp_add_inline_script('chart-js',$script_js);
    unset($day, $nb);

}