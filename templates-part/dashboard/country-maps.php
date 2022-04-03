<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.2
 * 
 * Desciption: Contry Maps
 */

use STATS4WP\Core\Options;
use STATS4WP\Core\DB;

if (Options::get_option('geochart') == true ) {

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
      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
      <script type="text/javascript">
        google.charts.load('current', {
          'packages':['geochart'],
        });
        google.charts.setOnLoadCallback(drawRegionsMap);

        function drawRegionsMap() {
          var data = google.visualization.arrayToDataTable([
            ['Country', '<?php _e('Users', 'stats4wp'); ?>'],
            <?php
            foreach ( $locations as $location ) {
              echo '[\'' . $location->location . '\', '. $location->nb .'],';
            }
            ?>
          ]);

          var options = {};

          var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

          chart.draw(data, options);
        }
      </script>
          <div id="regions_div" style="width: 100%"></div>
      </div>
  </div>
  <?php
}