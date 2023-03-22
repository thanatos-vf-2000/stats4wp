<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
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
        <canvas  id="chartjs_nb_hits" height="300vw" width="400vw"></canvas> 
    </div>
</div>
<?php
if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate($data);
    switch ($param['interval']) {
        case 'days':
            $select = 'last_counter as d';
            $char_title = __('Number of hits per days', 'stats4wp');
            break;
        case 'weeks':
            $select = 'CONCAT(YEAR(last_counter),".",WEEK(last_counter)) as d';
            $char_title = __('Number of hits per weeks', 'stats4wp');
            break;
        case 'month':
            $select = 'CONCAT(YEAR(last_counter),".",MONTH(last_counter)) as d';
            $char_title = __('Number of hits per months', 'stats4wp');
            break;
    }
    $hits = $wpdb->get_results("SELECT ".  $select .",AVG(hits) nb 
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' group by 1");
    foreach ($hits as $hit) {
        $day[]  = $hit->d ;
        $nb[] = $hit->nb;
    }

    $script_js = '
    const dataNbHits= {
		labels:'.json_encode($day). ',
		datasets: [{
			label: "'. esc_html(__('Hits', 'stats4wp')) .'",
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

    const optionsNbHits = {
		responsive: false,
		plugins: {
			title: {
			  display: true,
			  text: "'. esc_html($char_title) .'"
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

    const configNbHits = {
      type: "line",
      data: dataNbHits,
      options: optionsNbHits,
    };

    // render init block
    const myChartNbHits = new Chart(
      document.getElementById("chartjs_nb_hits"),
      configNbHits
    );
    
    ';
    wp_add_inline_script('chart-js', $script_js);
    unset($day, $nb);
}
