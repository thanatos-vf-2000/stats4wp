<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.8
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

?>
<div id ="stats4wp-nb-users-hits-widget" class="postbox " >
    <div class="postbox-header">
        <h2 class="hndle ui-sortable-handle"><?php _e('Number of Users and Hits', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
        <canvas  id="chartjs_nb_users_hits" height="300vw" width="800vw"></canvas> 
    </div>
</div>
<?php
if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate('');
    switch($param['group']) {
        case 1:
            $select = 'last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits';
            $char_title = __('Number of users and Hits per days', 'stats4wp');
            break;
        case 2:
            $select = 'CONCAT(YEAR(last_counter),"-",WEEK(last_counter)) as last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits';
            $char_title = __('Number of users and Hits per weeks', 'stats4wp');
            break;
        case 3:
            $select = 'CONCAT(YEAR(last_counter),"-",MONTH(last_counter)) as last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits';
            $char_title = __('Number of users and Hits per months', 'stats4wp');
            break;
        case 4:
            $select = 'CONCAT(YEAR(last_counter),"-",QUARTER(last_counter)) as last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits';
            $char_title = __('Number of users and Hits per quarter', 'stats4wp');
            break;
        case 5:
            $select = 'YEAR(last_counter) as last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits';
            $char_title = __('Number of users and Hits per Years', 'stats4wp');
            break;
    }
    $visitors = $wpdb->get_results("SELECT ".  $select ." 
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' 
        GROUP BY 1 ORDER BY 1 ASC");
    foreach ( $visitors as $visitor ) {
        $day[]  = $visitor->last_counter ;
        $users[] = $visitor->user;
        $hits[] = $visitor->hits;
    }

    $script_js = ' 
    const dataNbUsersHits = {
        labels:'.json_encode($day). ',
        datasets: [{
            label: "'. esc_html(__('Users', 'stats4wp')) .'",
            borderColor: "#FF6384",
            fill: false,
            pointRadius: [0],
            pointHitRadius: [0],
            cubicInterpolationMode: "monotone",
            tension: 0.4,
            backgroundColor: [
               "#FF6384"
            ],
            data:'. json_encode($users). ',
        },
        {
            label: "'. esc_html(__('Hits', 'stats4wp')) .'",
            borderColor: "#36A2EB",
            fill: false,
            pointRadius: [0],
            pointHitRadius: [0],
            cubicInterpolationMode: "monotone",
            tension: 0.4,
            backgroundColor: [
               "#36A2EB"
            ],
            data:'. json_encode($hits). ',
        }]
    };

    const optionsNbUsersHits = {
        responsive: true,
        plugins: {
            title: {
              display: false,
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
    };

    const configNbUsersHits = {
    type: "line",
    data: dataNbUsersHits,
    options: optionsNbUsersHits,
    };

    // render init block
    const myChartNbUsersHits = new Chart(
    document.getElementById("chartjs_nb_users_hits"),
    configNbUsersHits
    );
    
    ;';
    wp_add_inline_script('chart-js',$script_js);
    unset($day, $users, $hits, $visitors);

}