<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.1
 *
 * Desciption: Location Maps
 */

use STATS4WP\Core\Options;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

$page = (isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '');
if ($page == 'stats4wp_plugin') {
    $data = 'all';
} else {
    $data ='';
}

if (Options::get_option('geochart') == true) {
    $param = AdminGraph::getdate($data);
    $languages = $wpdb->get_results("SELECT UPPER(language) as language, count(*) as nb FROM ". DB::table('visitor') ." 
    WHERE device NOT IN ('bot','')
    AND language not in ('','*','#','q=')
    AND last_counter BETWEEN '".  $param['from'] ."' AND '". $param['to'] ."'
    GROUP BY language
    ORDER by nb DESC");
    ?>
  <div id ="stats4wp-maps-widget" class="postbox " >
      <div class="postbox-header">
          <h2 class="hndle ui-sortable-handle"><?php _e('Users language', 'stats4wp'); ?></h2>
      </div>
      <div class="inside">
      <?php
        if (isset($script_js)) {
            unset($script_js);
        }
        $script_js = '
        google.charts.load("current", {
          "packages":["geochart"],
        });
        google.charts.setOnLoadCallback(drawRegionsMap);

        function drawRegionsMap() {
          var data = google.visualization.arrayToDataTable([
            ["Country", "'. __("Users", "stats4wp") . '"],';

        foreach ($languages as $language) {
            $script_js .= '[\'' . esc_html($language->language) . '\', '. esc_html($language->nb) .'],';
        }

        $script_js .= '
          ]);

          var options = {};

          var chart = new google.visualization.GeoChart(document.getElementById("regions_div"));

          chart.draw(data, options);
        }
      ';
        wp_add_inline_script('google-loader', $script_js, 'after');
        ?>
          <div id="regions_div" style="width: 450px; height: 250px;" class="stats4wp-maps"></div>
      </div>
  </div>
    <?php
}
