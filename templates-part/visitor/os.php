<?php
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
            <div class="stats4wp-inline width46 border">
                <div id="accordion-os">
                    <?php
                    $os_versions = $wpdb->get_results("SELECT platform, platform_v as version, count(*) as nb
                        FROM ". DB::table('visitor') ."
                        WHERE device !='bot' 
                        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                        GROUP BY 1,2 ORDER BY 1,2 ASC ");
                    $os_local='';
                    foreach ( $os_versions as $os_version ) {
                        if ($os_local != $os_version->platform) {
                            if ($os_local != '') echo '</div>';
                            echo '<button class="stats4wp-accordion">'. esc_html($os_version->platform).'</button><div class="panel">';
                        }
                        echo '<p>' . esc_html($os_version->version) . ': '. esc_html($os_version->nb) .'</p>';
                        $os_local = $os_version->platform;
                    }
                    ?>
                    </div>
                </div>
            </div>
            <div class="stats4wp-inline width46 ">
                <canvas id="chartjs_os" height="300vw" width="400vw"></canvas> 
                <?php
                switch($param['interval']) {
                    case 'days':
                        $select = 'last_counter as z';
                        $char_title = __('OS per days', 'stats4wp');
                        break;
                    case 'weeks':
                        $select = 'WEEK(last_counter) as z';
                        $char_title = __('OS per weeks', 'stats4wp');
                        break;
                    case 'month':
                        $select = 'MONTH(last_counter) as z';
                        $char_title = __('OS per months', 'stats4wp');
                        break;
                }
                $oss = $wpdb->get_results('SELECT z as d,
                SUM(CASE WHEN platform = "Windows" THEN nb END) windows,
                SUM(CASE WHEN platform = "Ubuntu " THEN nb END) ubuntu ,
                SUM(CASE WHEN platform = "OS X " THEN nb END) osx ,
                SUM(CASE WHEN platform = "Linux " THEN nb END) linux ,
                SUM(CASE WHEN platform = "iOS" THEN nb END) ios,
                SUM(CASE WHEN platform = "Chrome OS" THEN nb END) chromeos,
                SUM(CASE WHEN platform = "Android" THEN nb END) android,
                SUM(CASE WHEN platform NOT IN  ("Windows","Ubuntu","OS X","Linux","iOS","Chrome OS","Android") THEN nb END) other
                FROM (
                select '. $select .', platform, COUNT(*) as nb
                  from '. DB::table('visitor') ." 
                  WHERE device !='bot' 
                  AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                  GROUP BY  1, 2
                ) all_data
                group by 1");
                foreach ( $oss as $os ) {
                    $day[]  = $os->d ;
                    $windows[] = ($os->windows == null) ? 0 : $os->windows;
                    $ubuntu[] = ($os->ubuntu == null) ? 0 : $os->ubuntu;
                    $osx[] = ($os->osx == null) ? 0 : $os->osx;
                    $linux[] = ($os->linux == null) ? 0 : $os->linux;
                    $ios[] = ($os->ios == null) ? 0 : $os->ios;
                    $chromeos[] = ($os->chromeos == null) ? 0 : $os->chromeos;
                    $android[] = ($os->android == null) ? 0 : $os->android;
                    $other[] = ($os->other == null) ? 0 : $os->other;
                }
                
                $script_js = ' var ctx = document.getElementById("chartjs_os").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels:'.json_encode($day). ',
                        datasets: [{
                                label: "'. esc_html(__('Windows', 'stats4wp')) .'",
                                backgroundColor: [
                                "#05419ad6"
                                ],
                                data:'. json_encode($windows). ',
                            },
                            {
                                label: "'. esc_html(__('Ubuntu', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#9a0505d6"
                                ],
                                data:'. json_encode($ubuntu). ',
                            },
                            {
                                label: "'. esc_html(__('osx', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#9a8805d6"
                                ],
                                data:'. json_encode($osx). ',
                            },
                            {
                                label: "'. esc_html(__('Linux', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#059a1ed6"
                                ],
                                data:'. json_encode($linux). ',
                            },
                            {
                                label: "'. esc_html(__('iOS', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#9a0588d6"
                                ],
                                data:'. json_encode($ios). ',
                            },
                            {
                                label: "'. esc_html(__('Chrome OS', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#05864a"
                                ],
                                data:'. json_encode($chromeos). ',
                            },
                            {
                                label: "'. esc_html(__('Android', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#052186"
                                ],
                                data:'. json_encode($android). ',
                            },
                            {
                                label: "'. esc_html(__('Other', 'stats4wp')) .'",
                                backgroundColor: [
                                   "#d4ec0cd6"
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
                unset($day, $windows, $ubuntu, $safari, $edge, $chrome, $chromeos, $android, $other);
                ?>
            </div>
        </div>
    </div>
    <?php
}