<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 */
global $wpdb;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

?>
<div id="stats4wp-browsers-widget" class="postbox ">
    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php _e('Devices', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
        <canvas  id="chartjs_top_devices" height="150vw" width="200vw"></canvas> 
    </div>
</div>

<?php
if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate('');
    $devices = $wpdb->get_results("SELECT device, count(*) as nb
        FROM ". DB::table('visitor') ." 
        where device!='bot' 
        AND last_counter BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' 
        GROUP BY 1 ORDER BY 2 DESC LIMIT 10");
    foreach ($devices as $device) {
        $type[]  = ($device->device == '') ? __('Unknown', 'stats4wp') : $device->device ;
        $nb[] = $device->nb ;
    }
    $script_js = ' 
    
    const dataTopDevices = {
        labels:'.json_encode($type). ',
        datasets: [{
            label: "'. esc_html(__('Devices', 'stats4wp')) .'",
            data:'. json_encode($nb). ',
            backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
        }]
      };

    const optionsTopDevices = {
    responsive: true,
    plugins: {
        title: {
            display: false,
            text: "'. esc_html(__('Devices', 'stats4wp')) .'"
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

    const configTopDevices = {
    type: "bar",
    data: dataTopDevices,
    options: optionsTopDevices,
    };

    // render init block
    const myChartTopDevices = new Chart(
    document.getElementById("chartjs_top_devices"),
    configTopDevices
    );
    ';
    wp_add_inline_script('chart-js', $script_js);

    unset($devices, $type, $nb);
}
