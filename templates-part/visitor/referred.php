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
$all_data= (isset($_GET['data'])) ? sanitize_text_field($_GET['data']) : '';
if ($all_data == 'all') {
    $all_data = '';
    $title = __('All Referred', 'stats4wp');
} else {
    $all_data = 'LIMIT 0,10';
    $title = __('Referred TOP 10', 'stats4wp');
}



if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate($data);
    ?>
    <div class="stats4wp-dashboard">
        <div class="stats4wp-rows">
            <ul id="stats4wp-menu" >
                <li><a class="<?php echo ($all_data=="")? "active":"";?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=referred&data=all" ><?php echo esc_html(__('All Referred', 'stats4wp')); ?></a></li>
                <li><a class="<?php echo ($all_data!="")? "active":"";?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=referred" ><?php echo esc_html(__('TOP 10 Referred', 'stats4wp')); ?></a></li>
            </ul>  
        </div>
        <div class="stats4wp-rows">
            <div class="stats4wp-inline width46 ">
            <canvas  id="chartjs_referred" height="300vw" width="400vw"></canvas> 
                <?php
                $referreds = $wpdb->get_results("SELECT referred, count(*) as nb FROM ". DB::table('visitor') ." 
                WHERE device NOT IN ('bot','')
                AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                GROUP BY referred
                ORDER by nb DESC ". $all_data);
                $referred_total = array_sum(array_column($referreds, 'nb'));
                $referred_nb=0;
                $referred_list='<table class="widefat table-stats stats4wp-report-table">
                <tbody>
                    <tr>
                        <td style="width: 1%;"></td>
                        <td>' .  esc_html(__('Referred', 'stats4wp')) . '</td>
                        <td style="width: 20%;"></td>
                        <td style="width: 20%;"></td>
                    </tr>';
                foreach ( $referreds as $referred ) {
                    if ($referred_nb <10) {
                        $src[]  = $referred->referred ;
                        $nb[] = ($referred->nb == null) ? 0 : $referred->nb;
                    }
                    $referred_nb++;
                    $tr_class = ($referred_nb % 2 == 0) ? "stats4wp-bg" : '';
                    $percent = round($referred->nb * 100 / $referred_total, 2);
                    $referred_list .=  '<tr class="' . esc_attr($tr_class) . '"><td>' . $referred_nb . '</td><td>' . esc_html(substr($referred->referred,0,50))  . '</td><td class="stats4wp-right">' .  esc_html(number_format($referred->nb, 0, ',', ' ')). '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr($percent) . '%;"></div>' . esc_html($percent) . '%</td></tr>' ;               
                    
                }
                $referred_list .= '</tbody>
                    </table>';
                $script_js = ' var ctx = document.getElementById("chartjs_referred").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels:'.json_encode($src). ',
                        datasets: [{
                            label: "'. esc_html(__('Referred', 'stats4wp')) .'",
                            data:'. json_encode($nb). ',
                            backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
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
                unset($src, $nb);
                ?>
            </div>
            <div class="stats4wp-inline width46 ">
                <div class="stats4wp-referred">
                    <?php echo $referred_list; ?>
                </div>
            </div>
            </div>
    </div>
    <?php
}