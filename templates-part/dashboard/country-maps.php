<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.1
 *
 * Desciption: Contry Maps
 */

use STATS4WP\Core\Options;
use STATS4WP\Core\DB;

if (Options::get_option('geochart') == true) {
    $locations = $wpdb->get_results("SELECT location, count(*) as nb FROM ". DB::table('visitor') ." 
    WHERE device NOT IN ('bot','')
    AND location NOT IN ('local','none')
    AND last_counter BETWEEN '".  date("Y-m-d", strtotime('-1 years')) ."' AND '". date("Y-m-d") ."'
    GROUP BY location
    ORDER by nb DESC");
    ?>
  <div id ="stats4wp-maps-widget" class="postbox " >
      <div class="postbox-header">
          <h2 class="hndle ui-sortable-handle"><?php _e('Users country maps for last year', 'stats4wp'); ?></h2>
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
            ["Country", "' . __("Users", "stats4wp") . '"], ';

        foreach ($locations as $location) {
            $script_js .=  '[\'' . esc_html($location->location) . '\', '. esc_html($location->nb) .'],';
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
          <div id="regions_div" style="width: 100%"></div>
      </div>
  </div>
    <?php
}
