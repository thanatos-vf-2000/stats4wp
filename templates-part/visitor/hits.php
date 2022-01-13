<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.0
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
    switch($param['interval']) {
        case 'days':
            $select = 'last_counter as d';
            $char_title = __('Number of hits per days', 'stats4wp');
            break;
        case 'weeks':
            $select = 'WEEK(last_counter) as d';
            $char_title = __('Number of hits per weeks', 'stats4wp');
            break;
        case 'month':
            $select = 'MONTH(last_counter) as d';
            $char_title = __('Number of hits per months', 'stats4wp');
            break;
    }
    $hits = $wpdb->get_results("SELECT ".  $select .",AVG(hits) nb 
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' group by 1");
    foreach ( $hits as $hit ) {
        $day[]  = $hit->d ;
        $nb[] = $hit->nb;
    }

    $script_js = ' var ctx = document.getElementById("chartjs_nb_hits").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "line",
                    data: {
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
                    },
                    options: {
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
                    },
                }
                );';
    wp_add_inline_script('chart-js',$script_js);
    unset($day, $nb);

}