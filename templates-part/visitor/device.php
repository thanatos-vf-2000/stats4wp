<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.2
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
                <canvas id="chartjs_devices" height="300vw" width="400vw"></canvas> 
                <?php
                switch($param['interval']) {
                    case 'days':
                        $select = 'last_counter as z';
                        $char_title = __('Device per days', 'stats4wp');
                        break;
                    case 'weeks':
                        $select = 'CONCAT(YEAR(last_counter),".",WEEK(last_counter)) as z';
                        $char_title = __('Device per weeks', 'stats4wp');
                        break;
                    case 'month':
                        $select = 'CONCAT(YEAR(last_counter),".",MONTH(last_counter)) as z';
                        $char_title = __('Device per months', 'stats4wp');
                        break;
                }
                $devices = $wpdb->get_results('SELECT z as d,
                SUM(CASE WHEN device =  "desktop" THEN nb END) desktop,
                SUM(CASE WHEN device =  "mobile " THEN nb END) mobile ,
                SUM(CASE WHEN device =  "tablet " THEN nb END) tablet ,
                SUM(CASE WHEN device NOT IN  ("desktop","mobile","tablet") THEN nb END) other
                FROM (
                select '. $select .', device, COUNT(*) as nb
                  from '. DB::table('visitor') ." 
                  WHERE device NOT IN ('bot','') 
                  AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                  GROUP BY  1, 2
                ) all_data
                group by 1");
                foreach ( $devices as $device ) {
                    $day[]  = $device->d ;
                    $desktop[] = ($device->desktop == null) ? 0 : $device->desktop;
                    $mobile[] = ($device->mobile == null) ? 0 : $device->mobile;
                    $tablet[] = ($device->tablet == null) ? 0 : $device->tablet;
                    $other[] = ($device->other == null) ? 0 : $device->other;
                }
                
                $script_js = ' var ctx = document.getElementById("chartjs_devices").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels:'.json_encode($day). ',
                        datasets: [{
                                label: "'. esc_html(__('Desktop', 'stats4wp')) .'",
                                backgroundColor: [
                                "#36a2eb"
                                ],
                                data:'. json_encode($desktop). ',
                            },
                            {
                                label: "'. esc_html(__('Mobile', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#f67019"
                                ],
                                data:'. json_encode($mobile). ',
                            },
                            {
                                label: "'. esc_html(__('Tablet', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#f53794"
                                ],
                                data:'. json_encode($tablet). ',
                            },
                            {
                                label: "'. esc_html(__('Other', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#537bc4"
                                ],
                                data:'. json_encode($other). ',
                            }
                        ]
                    },
                    options: {
                        responsive: false,
                        scales: {
                            x: {
                              stacked: true,
                            },
                            y: {
                              stacked: true
                            }
                          },
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
                unset($day, $desktop, $mobile, $tablet, $other);
                ?>
            </div>
            <div class="stats4wp-inline width46 ">
                <div class="stats4wp-device">
                    <?php
                    $devices_version = $wpdb->get_results("SELECT device, manufacturer as version, count(*) as nb
                        FROM ". DB::table('visitor') ."
                        WHERE device NOT in ('bot','') 
                        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                        GROUP BY 1,2 ORDER BY 1,2 ASC ");
                    $device_total = array_sum(array_column($devices_version, 'nb'));
                    $device_nb=1;
                    $device_local='';
                    echo '<table class="widefat table-stats stats4wp-report-table">
                        <tbody>
                            <tr>
                                <td style="width: 1%;"></td>
                                <td>' .  esc_html(__('Devices', 'stats4wp')) . '</td>
                                <td style="width: 20%;">' .  esc_html(__('Users', 'stats4wp')) . '</td>
                                <td style="width: 20%;">' .  esc_html(__('% Users', 'stats4wp')) . '</td>
                            </tr>';
                    foreach ( $devices_version as $device_version ) {
                        if ($device_local != $device_version->device) {
                            $device_nb = 1;
                            echo  '<tr><th colspan="4">'. esc_html($device_version->device).'</th></tr>';
                        }
                        $tr_class = ($device_nb % 2 == 0) ? "stats4wp-bg" : '';
                        $percent = round($device_version->nb * 100 / $device_total, 2);
                        echo '<tr class="' . esc_attr($tr_class) . '"><td>' . $device_nb . '</td><td>' . esc_html(substr($device_version->version,0,50))  . '</td><td class="stats4wp-right">' .  esc_html(number_format($device_version->nb, 0, ',', ' ')). '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr($percent) . '%;"></div>' . esc_html($percent) . '%</td></tr>' ;
                        $device_local = $device_version->device;
                        $device_nb++;
                    }
                    echo  '</tbody>
                    </table>';
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}