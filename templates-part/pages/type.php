<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.1.0
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;
$page = (isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '');
if ($page == 'stats4wp_plugin') {
    $data = 'all';
} else {
    $data ='';
}

if (DB::ExistRow('pages')) {
    $param = AdminGraph::getdate($data);
    ?>
    <div class="stats4wp-dashboard">
        <div class="stats4wp-rows">
            <div class="stats4wp-inline width46 ">
            <canvas  id="chartjs_type" height="300vw" width="400vw"></canvas> 
                <?php
                $types = $wpdb->get_results("SELECT type, count(*) as nb FROM ". DB::table('pages') ." 
                WHERE date BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                AND type!='unknown'
                GROUP BY type
                ORDER by nb DESC");
                $type_nb=0;
                $type_list='';
                foreach ( $types as $type ) {
                    if ($type_nb <10) {
                        $t[]  = $type->type ;
                        $nb[] = ($type->nb == null) ? 0 : $type->nb;
                    }
                    if ($type_list != '') $type_list .= ' - ';
                    $type_list .=  esc_html($type->type) . ': '. esc_html($type->nb) ;                 
                    $type_nb++;
                }
                $script_js = ' var ctx = document.getElementById("chartjs_type").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels:'.json_encode($t). ',
                        datasets: [{
                            label: "'. esc_html(__('Type', 'stats4wp')) .'",
                            data:'. json_encode($nb). ',
                            backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
                        }]
                    },
                    options: {
                        responsive: false,
                        plugins: {
                            title: {
                              display: true,
                              text: "'. esc_html(__('Type TOP 10', 'stats4wp')) .'"
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
                unset($t, $nb);
                ?>
            </div>
            <div class="stats4wp-inline width46 border">
                <div classs="stats4wp-type">
                    <p><?php echo esc_html($type_list); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php
}