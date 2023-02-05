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
                $type_total = array_sum(array_column($types, 'nb'));
                $type_nb=0;
                $type_list='<table class="widefat table-stats stats4wp-report-table">
                    <tbody>
                        <tr>
                            <td style="width: 1%;"></td>
                            <td>' .  esc_html(__('Type', 'stats4wp')) . '</td>
                            <td style="width: 20%;"></td>
                            <td style="width: 20%;"></td>
                        </tr>';
                foreach ( $types as $type ) {
                    if ($type_nb <10) {
                        $t[]  = $type->type ;
                        $nb[] = ($type->nb == null) ? 0 : $type->nb;
                    }
                    $type_nb++;
                    $tr_class = ($type_nb % 2 == 0) ? "stats4wp-bg" : '';
                    $percent = round($type->nb * 100 / $type_total, 2);
                    $type_list .=  '<tr class="' . esc_attr($tr_class) . '"><td>' . $type_nb . '</td><td>' . esc_html($type->type)  . '</td><td class="stats4wp-right">' .  esc_html(number_format($type->nb, 0, ',', ' ')). '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr($percent) . '%;"></div>' . esc_html($percent) . '%</td></tr>' ;
                }
                $type_list .= '</tbody>
                    </table>';
                $script_js = '
                
                const dataType= {
                    labels:'.json_encode($t). ',
                    datasets: [{
                        label: "'. esc_html(__('Type', 'stats4wp')) .'",
                        data:'. json_encode($nb). ',
                        backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
                    }]
                };
            
                const optionsType = {
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
                };
            
                const configType = {
                  type: "doughnut",
                  data: dataType,
                  options: optionsType,
                };
            
                // render init block
                const myChartType = new Chart(
                  document.getElementById("chartjs_type"),
                  configType
                );
                
                ';
                wp_add_inline_script('chart-js',$script_js);
                unset($t, $nb);
                ?>
            </div>
            <div class="stats4wp-inline width46">
                <div class="stats4wp-type">
                    <?php echo $type_list; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}