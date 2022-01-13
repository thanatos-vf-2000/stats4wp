<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.0
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate('');
    $visitors = $wpdb->get_row("SELECT count(DISTINCT(ip)) as users, 
        count(*) as sessions,  
        SUM(hits) as pages 
        FROM ". DB::table('visitor'). " 
        WHERE device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'");
    $newusers = $wpdb->get_row("SELECT count(DISTINCT(ip)) as nb
        FROM ". DB::table('visitor'). " 
        WHERE device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' 
        AND IP not in (SELECT DISTINCT(ip) FROM ". DB::table('visitor'). " 
            WHERE device!='bot' AND last_counter < '". $param['from'] ."')");
    $bounce = $wpdb->get_row("SELECT count(*) as sessions  
        FROM ". DB::table('visitor'). " 
        WHERE device!='bot' 
        AND hits=1
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."'");
    
    $type[]  = __('New Visitor', 'stats4wp') ;
    $nb[] =  round($newusers->nb * 100 / $visitors->users, 2);
    $type[]  = __('Returning Visitor', 'stats4wp') ;
    $nb[] =  round(100 - $newusers->nb * 100 / $visitors->users, 2);

} 

?>
<div id ="stats4wp-visitors-widget" class="postbox " >
    <div class="postbox-header">
        <h2 class="hndle ui-sortable-handle"><?php _e('Users', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
        <table class="_S4WPt">
            <tbody>
                <tr>
                    <td class="_S4WPtr" >
                        <div class="_S4WPc0">
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php _e('Users', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->users, 0, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php _e('New users', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($newusers->nb, 0, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php _e('Sessions', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->sessions, 0, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php _e('Number of sessions per user', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->sessions/$visitors->users, 2, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php _e('Page views', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->pages, 0, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php _e('Pages/session', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->pages/$visitors->sessions, 2, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php _e('Bounce rate', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($bounce->sessions*100/$visitors->sessions, 2, ',', ' ')); ?> %</div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="_S4WPtr" >
                        <canvas  id="chartjs_visitors_new" height="150vw" width="200vw"></canvas>
                    </td>
                </tr>
            </tbody>
        </table> 
    </div>
</div>
<?php
$script_js = ' var ctx = document.getElementById("chartjs_visitors_new").getContext("2d");
var myChart = new Chart(ctx, {
    type: "doughnut",
    data: {
        labels:'.json_encode($type). ',
        datasets: [{
            label: "'. esc_html(__('Browsers', 'stats4wp')) .'",
            data:'. json_encode($nb). ',
            backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
              display: false,
              text: "'. esc_html(__('Browser TOP 10', 'stats4wp')) .'"
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

unset($type, $nb);