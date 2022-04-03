<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.2
 * 
 * Desciption: Location Maps
 */

use STATS4WP\Core\Options;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

if (Options::get_option('geochart') == true ) {
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
            foreach ( $languages as $language ) {
              echo '[\'' . $language->language . '\', '. $language->nb .'],';
            }
            ?>
          ]);

          var options = {};

          var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

          chart.draw(data, options);
        }
      </script>
          <div id="regions_div" style="width: 450px; height: 250px;" class="stats4wp-maps"></div>
      </div>
  </div>
  <?php
}