<?php

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

$page = (isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '');
if ($page == 'stats4wp_plugin') {
    $data = 'all';
} else {
    $data ='';
}
$all_data= (isset($_GET['data'])) ? sanitize_text_field($_GET['data']) : '';
if ($all_data == 'all') {
    $all_data = '';
    $title = __('All Bots', 'stats4wp');
} else {
    $all_data = 'LIMIT 0,10';
    $title = __('TOP 10 Bots', 'stats4wp');
}


?>
<div class="stats4wp-dashboard">
    <div class="stats4wp-rows">
        <ul id="stats4wp-menu" >
            <li><a class="<?php echo ($all_data=="")? "active":"";?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=bots&data=all" ><?php echo esc_html(__('All Bots', 'stats4wp')); ?></a></li>
            <li><a class="<?php echo ($all_data!="")? "active":"";?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=bots" ><?php echo esc_html(__('TOP 10 Bots', 'stats4wp')); ?></a></li>
        </ul>  
    </div>
    <div class="stats4wp-rows">
        <canvas  id="chartjs_bots" height="300vw" width="400vw"></canvas> 
    </div>
</div>
<?php
if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate($data);
    $bots = $wpdb->get_results("SELECT agent, COUNT(*) AS nb 
        FROM ". DB::table('visitor') ." 
        where device='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' 
        GROUP BY 1 ORDER by 2 DESC ". $all_data);
    foreach ( $bots as $bot ) {
        $bot_agent[]  = $bot->agent ;
        $nb[] = $bot->nb;
    }

    $script_js = ' var ctx = document.getElementById("chartjs_bots").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels:'.json_encode($bot_agent). ',
                        datasets: [{
                            label: "'. esc_html(__('Bots', 'stats4wp')) .'",
                            borderColor: "#05419ad6",
                            fill: false,
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
                              text: "'. esc_html($title) .'"
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