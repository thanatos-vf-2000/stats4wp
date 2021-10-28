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
            <div class="stats4wp-inline width46 ">
            <canvas  id="chartjs_location" height="300vw" width="400vw"></canvas> 
                <?php
                $locations = $wpdb->get_results("SELECT location, count(*) as nb FROM ". DB::table('visitor') ." 
                WHERE device NOT IN ('bot','')
                AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'
                GROUP BY location
                ORDER by nb DESC");
                $pays_nb=0;
                $pays_list='';
                foreach ( $locations as $location ) {
                    if ($pays_nb <10) {
                        $pay[]  = $location->location ;
                        $nb[] = ($location->nb == null) ? 0 : $location->nb;
                    }
                    if ($pays_list != '') $pays_list .= ' - ';
                    $pays_list .=  esc_html($location->location) . ': '. esc_html($location->nb) ;                 
                    $pays_nb++;
                }
                $script_js = ' var ctx = document.getElementById("chartjs_location").getContext("2d");
                var myChart = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels:'.json_encode($pay). ',
                        datasets: [{
                            label: "'. esc_html(__('Country', 'stats4wp')) .'",
                            data:'. json_encode($nb). ',
                            backgroundColor: ["#05419ad6", "#9a0505d6", "#9a8805d6", "#059a1ed6", "#9a0588d6", "#d4ec0cd6"],
                        }]
                    },
                    options: {
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
                    },
                }
                );';
                wp_add_inline_script('chart-js',$script_js);
                unset($pay, $nb);
                ?>
            </div>
            <div class="stats4wp-inline width46 border">
                <div classs="stats4wp-location">
                    <p><?php echo esc_html($pays_list); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php
}