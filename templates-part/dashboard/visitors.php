<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */


if (! defined('ABSPATH') ) {
    exit;
}

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

if (DB::exist_row('visitor') ) {
    $param = AdminGraph::getdate('');
    if (! isset($wpdb->stats4wp_visitor) ) {
        $wpdb->stats4wp_visitor = DB::table('visitor');
    }
    $visitors = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT count(DISTINCT(ip)) as users, 
        count(*) as sessions,  
        SUM(hits) as pages 
        FROM $wpdb->stats4wp_visitor
        WHERE device!='bot' 
        AND last_counter BETWEEN %s AND %s",
            array(
                $param['from'],
                $param['to'],
            )
        )
    );
    $newusers = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT count(DISTINCT(ip)) as nb
        FROM $wpdb->stats4wp_visitor
        WHERE device!='bot' 
        AND last_counter BETWEEN %s AND %s
        AND IP not in (SELECT DISTINCT(ip) FROM $wpdb->stats4wp_visitor
            WHERE device!='bot' AND last_counter < %s)",
            array(
                $param['from'],
                $param['to'],
                $param['from'],
            )
        )
    );
    $bounce   = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT count(*) as sessions  
        FROM $wpdb->stats4wp_visitor 
        WHERE device!='bot' 
        AND hits=1
        AND last_counter BETWEEN %s AND %s",
            array(
                $param['from'],
                $param['to'],
            )
        )
    );

    $type_local[] = __('New Visitor', 'stats4wp');
    $nb[]         = round($newusers->nb * 100 / $visitors->users, 2);
    $type_local[] = __('Returning Visitor', 'stats4wp');
    $nb[]         = round(100 - $newusers->nb * 100 / $visitors->users, 2);
}

?>
<div id ="stats4wp-visitors-widget" class="postbox " >
    <div class="postbox-header">
        <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Users', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
        <table class="_S4WPt">
            <tbody>
                <tr>
                    <td class="_S4WPtr" >
                        <div class="_S4WPc0">
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php esc_html_e('Users', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->users, 0, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php esc_html_e('New users', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($newusers->nb, 0, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php esc_html_e('Sessions', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->sessions, 0, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php esc_html_e('Number of sessions per user', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->sessions / $visitors->users, 2, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php esc_html_e('Page views', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->pages, 0, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php esc_html_e('Pages/session', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($visitors->pages / $visitors->sessions, 2, ',', ' ')); ?></div>
                                </div>
                            </div>
                            <div class="_S4WPb-_S4WPci-_S4WPhi _S4WPYd">
                                <div class="_S4WPgmb _S4WPYs" ><?php esc_html_e('Bounce rate', 'stats4wp'); ?></div>
                                <div class="_S4WPvo">
                                    <div class="_S4WPGu"><?php echo esc_html(number_format($bounce->sessions * 100 / $visitors->sessions, 2, ',', ' ')); ?> %</div>
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
$script_js = ' 

    const dataVisitorsNew = {
        labels:' . wp_json_encode($type_local) . ',
        datasets: [{
            label: "' . esc_html(__('Browsers', 'stats4wp')) . '",
            data:' . wp_json_encode($nb) . ',
            backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
        }]
    };

    const optionsVisitorsNew = {
        responsive: true,
        plugins: {
            title: {
              display: false,
              text: "' . esc_html(__('Browser TOP 10', 'stats4wp')) . '"
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

    const configVisitorsNew = {
        type: "doughnut",
        data: dataVisitorsNew,
        options: optionsVisitorsNew,
    };

    // render init block
    const myChartVisitorsNew = new Chart(
        document.getElementById("chartjs_visitors_new"),
        configVisitorsNew
    );

';
wp_add_inline_script('chart-js', $script_js);

unset($type_local, $nb);
