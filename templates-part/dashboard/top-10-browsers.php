<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.8
 */
global $wpdb;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

?>
<div id="stats4wp-browsers-widget" class="postbox ">
    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php _e('Top 10 Browsers', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
        <canvas  id="chartjs_top_browsers" height="150vw" width="200vw"></canvas> 
    </div>
</div>

<?php
if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate('');
    $browsers = $wpdb->get_results("SELECT agent, count(*) as nb
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' 
        GROUP BY 1 ORDER BY 2 DESC LIMIT 10");
    foreach ($browsers as $browser) {
        $type[]  = $browser->agent ;
        $nb[] = $browser->nb ;
    }
    $script_js = ' 
   
    const dataTopBrowsers = {
        labels: '.json_encode($type). ',
        datasets: [{
          label: "'. esc_html(__('Browsers', 'stats4wp')) .'",
          data: '. json_encode($nb). ',
          backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
          hoverOffset: 4
        }]
      };

    const optionsTopBrowsers = {
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
    }
    };

    const configTopBrowsers = {
    type: "doughnut",
    data: dataTopBrowsers,
    options: optionsTopBrowsers,
    };

    // render init block
    const myChartTopBrowsers = new Chart(
    document.getElementById("chartjs_top_browsers"),
    configTopBrowsers
    );
    ';
    wp_add_inline_script('chart-js',$script_js);

    unset($browsers, $type, $nb);
}