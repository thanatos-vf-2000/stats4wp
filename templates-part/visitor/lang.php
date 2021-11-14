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

if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate($data);
    ?>
    <div class="stats4wp-dashboard">
        <div class="stats4wp-rows">
            <div class="stats4wp-inline width46 ">
            <canvas  id="chartjs_lang" height="300vw" width="400vw"></canvas> 
                <?php
                $languages = $wpdb->get_results("SELECT language, count(*) as nb FROM ". DB::table('visitor') ." 
                WHERE device NOT IN ('bot','')
                AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                GROUP BY language
                ORDER by nb DESC");
                $language_nb=0;
                $language_list='';
                foreach ( $languages as $language ) {
                    if ($language_nb <10) {
                        $lang[]  = $language->language ;
                        $nb[] = ($language->nb == null) ? 0 : $language->nb;
                    }
                    if ($language_list != '') $language_list .= ' - ';
                    $language_list .=  esc_html($language->language) . ': '. esc_html($language->nb) ;                 
                    $language_nb++;
                }
                $script_js = ' var ctx = document.getElementById("chartjs_lang").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels:'.json_encode($lang). ',
                        datasets: [{
                            label: "'. esc_html(__('Languages', 'stats4wp')) .'",
                            data:'. json_encode($nb). ',
                            backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
                        }]
                    },
                    options: {
                        responsive: false,
                        plugins: {
                            title: {
                              display: true,
                              text: "'. esc_html(__('Language TOP 10', 'stats4wp')) .'"
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
                unset($lang, $nb);
                ?>
            </div>
            <div class="stats4wp-inline width46 border">
                <div classs="stats4wp-language">
                    <p><?php echo esc_html($language_list); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php
}