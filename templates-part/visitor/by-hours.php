<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.4
 * 
 * Desciption: By Hour
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
        <div class="stats4wp-inline width46 ">
            <canvas  id="chartjs_by_hours" height="300vw" width="400vw"></canvas> 
        </div>
        <div class="stats4wp-inline width46 ">
            <canvas  id="chartjs_by_hours_days" height="300vw" width="400vw"></canvas> 
        </div>
    </div>
</div>
<?php
if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate($data);
    $by_hours = $wpdb->get_results("SELECT HOUR(hour) as hour, COUNT(*) AS nb 
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' 
        GROUP BY 1 ORDER by 1 ASC");
    foreach ( $by_hours as $by_hour ) {
        $hour[]  = $by_hour->hour ;
        $nb[] = $by_hour->nb;
    }

    $script_js = ' var ctx = document.getElementById("chartjs_by_hours").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels:'.json_encode($hour). ',
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
                              text: "'. esc_html(__('Visitors by hour', 'stats4wp')) .'"
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
    unset($day, $nb,$script_js);
    $by_hours_days = $wpdb->get_results("SELECT DAYOFWEEK(last_counter) as d, HOUR(hour) as hour, COUNT(*) AS nb 
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' 
        GROUP BY 1,2 ORDER by 1 ASC");
    foreach ( $by_hours_days as $by_hour_day ) {
        switch ($by_hour_day->d) {
            case 1:
                $d = __('Sunday', 'stats4wp');
                break;
            case 2:
                $d = __('Monday', 'stats4wp');
                break;
            case 3:
                $d = __('Tuesday', 'stats4wp');
                break;
            case 4:
                $d = __('Wednesday', 'stats4wp');
                break;
            case 5:
                $d = __('Thursday', 'stats4wp');
                break;
            case 6:
                $d = __('Friday', 'stats4wp');
                break;
            case 7:
                $d = __('Saturday', 'stats4wp');
                break;
        }
        $hour[]  = $d . " - " . $by_hour_day->hour ;
        $nb[] = $by_hour_day->nb;
    }

    $script_js = ' var ctx = document.getElementById("chartjs_by_hours_days").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels:'.json_encode($hour). ',
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
                              text: "'. esc_html(__('Visitors by hour and days', 'stats4wp')) .'"
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

}