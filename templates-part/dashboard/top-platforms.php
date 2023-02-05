<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.8
 */
global $wpdb;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

?>
<div id="stats4wp-platforms-widget" class="postbox ">
    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php _e('Top Platforms', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
    <canvas  id="chartjs_top_platforms" height="150vw" width="200vw"></canvas> 
    </div>
</div>
<?php
if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate('');
    $platforms = $wpdb->get_results("SELECT platform, count(*) as nb
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' 
        GROUP BY 1 ORDER BY 2 DESC LIMIT 10");
    foreach ($platforms as $platform) {
        $type[]  = $platform->platform ;
        $nb[] = $platform->nb ;
    }
    $script_js = ' 
    
    const dataTopPlatforms= {
        labels: '.json_encode($type). ',
        datasets: [{
          label: "'. esc_html(__('Platforms', 'stats4wp')) .'",
          data: '. json_encode($nb). ',
          backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
          hoverOffset: 4
        }]
      };

    const optionsTopPlatforms = {
      responsive: true,
      plugins: {
          title: {
              display: false,
              text: "'. esc_html(__('Platforms TOP 10', 'stats4wp')) .'"
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

    const configTopPlatforms = {
      type: "doughnut",
      data: dataTopPlatforms,
      options: optionsTopPlatforms,
    };

    // render init block
    const myChartTopPlatforms = new Chart(
      document.getElementById("chartjs_top_platforms"),
      configTopPlatforms
    );

    ';
    wp_add_inline_script('chart-js',$script_js);

    unset($browsers, $type, $nb);
}