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

if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate($data);
    ?>
    <div class="stats4wp-dashboard">
        <div class="stats4wp-rows">
            <div class="stats4wp-inline width46 ">
            <canvas  id="chartjs_location" height="300vw" width="400vw"></canvas> 
                <?php
                $locations = $wpdb->get_results("SELECT location, count(*) as nb FROM ". DB::table('visitor') ." 
                WHERE device NOT IN ('bot','')
                AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                GROUP BY location
                ORDER by nb DESC");
                $pays_total = array_sum(array_column($locations, 'nb'));
                $pays_nb=0;
                $pays_list='<table class="widefat table-stats stats4wp-report-table">
                <tbody>
                    <tr>
                        <td style="width: 1%;"></td>
                        <td>' .  esc_html(__('Languages', 'stats4wp')) . '</td>
                        <td style="width: 20%;">' .  esc_html(__('Users', 'stats4wp')) . '</td>
                        <td style="width: 20%;">' .  esc_html(__('% Users', 'stats4wp')) . '</td>
                    </tr>';
                foreach ($locations as $location) {
                    if ($pays_nb <10) {
                        $pay[]  = $location->location ;
                        $nb[] = ($location->nb == null) ? 0 : $location->nb;
                    }
                    $pays_nb++;
                    $tr_class = ($pays_nb % 2 == 0) ? "stats4wp-bg" : '';
                    $percent = round($location->nb * 100 / $pays_total, 2);
                    $pays_list .=  '<tr class="' . esc_attr($tr_class) . '"><td>' . esc_html($pays_nb) . '</td><td>' . esc_html(substr($location->location, 0, 50))  . '</td><td class="stats4wp-right">' .  esc_html(number_format($location->nb, 0, ',', ' ')). '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr($percent) . '%;"></div>' . esc_html($percent) . '%</td></tr>' ;
                }
                $pays_list .= '</tbody>
                    </table>';
                $script_js = '
                const dataLocation= {
                    labels:'.json_encode($pay). ',
                    datasets: [{
                        label: "'. esc_html(__('Country', 'stats4wp')) .'",
                        data:'. json_encode($nb). ',
                        backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
                    }]
                };
            
                const optionsLocation = {
                    responsive: false,
                    plugins: {
                        title: {
                          display: true,
                          text: "'. esc_html(__('Location TOP 10', 'stats4wp')) .'"
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
            
                const configLocation = {
                  type: "doughnut",
                  data: dataLocation,
                  options: optionsLocation,
                };
            
                // render init block
                const myChartLocation = new Chart(
                  document.getElementById("chartjs_location"),
                  configLocation
                );
                ';
                wp_add_inline_script('chart-js', $script_js);
                unset($pay, $nb);
                ?>
            </div>
            <div class="stats4wp-inline width46 ">
                <div class="stats4wp-location">
                    <?php _e($pays_list); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
