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
                <canvas id="chartjs_agents" height="300vw" width="400vw"></canvas> 
                <?php
                switch($param['interval']) {
                    case 'days':
                        $select = 'last_counter as z';
                        $char_title = __('Agents per days', 'stats4wp');
                        break;
                    case 'weeks':
                        $select = 'CONCAT(YEAR(last_counter),".",WEEK(last_counter)) as z';
                        $char_title = __('Agents per weeks', 'stats4wp');
                        break;
                    case 'month':
                        $select = 'CONCAT(YEAR(last_counter),".",MONTH(last_counter)) as z';
                        $char_title = __('Agents per months', 'stats4wp');
                        break;
                }
                $agents = $wpdb->get_results('SELECT z as d,
                SUM(CASE WHEN agent =  "Internet Explorer" THEN nb END) ie,
                SUM(CASE WHEN agent =  "Firefox " THEN nb END) firefox ,
                SUM(CASE WHEN agent =  "Safari " THEN nb END) safari ,
                SUM(CASE WHEN agent =  "Edge " THEN nb END) edge ,
                SUM(CASE WHEN agent =  "Chrome" THEN nb END) chrome,
                SUM(CASE WHEN agent NOT IN  ("Internet Explorer","Firefox","Safari","Edge","Chrome") THEN nb END) other
                FROM (
                select '. $select .', agent, COUNT(*) as nb
                  from '. DB::table('visitor') ." 
                  WHERE device !='bot' 
                  AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                  GROUP BY  1, 2
                ) all_data
                group by 1");
                foreach ( $agents as $agent ) {
                    $day[]  = $agent->d ;
                    $ie[] = ($agent->ie == null) ? 0 : $agent->ie;
                    $firefox[] = ($agent->firefox == null) ? 0 : $agent->firefox;
                    $safari[] = ($agent->safari == null) ? 0 : $agent->safari;
                    $edge[] = ($agent->edge == null) ? 0 : $agent->edge;
                    $chrome[] = ($agent->chrome == null) ? 0 : $agent->chrome;
                    $other[] = ($agent->other == null) ? 0 : $agent->other;
                }
                
                $script_js = ' var ctx = document.getElementById("chartjs_agents").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels:'.json_encode($day). ',
                        datasets: [{
                                label: "'. esc_html(__('Internet Explorer', 'stats4wp')) .'",
                                backgroundColor: [
                                "#36a2eb"
                                ],
                                data:'. json_encode($ie). ',
                            },
                            {
                                label: "'. esc_html(__('Firefox', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#f67019"
                                ],
                                data:'. json_encode($firefox). ',
                            },
                            {
                                label: "'. esc_html(__('Safari', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#f53794"
                                ],
                                data:'. json_encode($safari). ',
                            },
                            {
                                label: "'. esc_html(__('Edge', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#537bc4"
                                ],
                                data:'. json_encode($edge). ',
                            },
                            {
                                label: "'. esc_html(__('Chrome', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#acc236"
                                ],
                                data:'. json_encode($chrome). ',
                            },
                            {
                                label: "'. esc_html(__('Other', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#166a8f"
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
                unset($day, $ie, $firefox, $safari, $edge, $chrome, $other);
                ?>
            </div>
            <div class="stats4wp-inline width46 ">
                <div class="stats4wp-agents">
                    <?php
                    $agents_version = $wpdb->get_results("SELECT agent, agent_v as version, count(*) as nb
                        FROM ". DB::table('visitor') ."
                        WHERE device !='bot' 
                        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                        GROUP BY 1,2 ORDER BY 1,2 ASC ");
                    $agents_total = array_sum(array_column($agents_version, 'nb'));
                    $agents_nb=1;
                    $agent_local='';
                    echo '<table class="widefat table-stats stats4wp-report-table">
                        <tbody>
                            <tr>
                                <td style="width: 1%;"></td>
                                <td>' .  esc_html(__('Os', 'stats4wp')) . '</td>
                                <td style="width: 10%;"></td>
                            </tr>';
                    foreach ( $agents_version as $agent_version ) {
                        if ($agent_local != $agent_version->agent) {
                            $agents_nb = 1;
                            echo  '<tr><th colspan="3">'. esc_html($agent_version->agent).'</th></tr>';
                        }
                        $percent = round($agent_version->nb * 100 / $agents_total, 2);
                        echo '<tr><td>' . $agents_nb . '</td><td>' . esc_html(substr($agent_version->version,0,50))  . '</td><td>' . esc_html($percent) . '%</td><td>' .  esc_html(number_format($agent_version->nb, 0, ',', ' ')). '</td></tr>' ;
                        $agent_local = $agent_version->agent;
                        $agents_nb++;
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