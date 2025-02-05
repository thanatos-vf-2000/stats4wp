<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 *
 * Desciption: Location Maps
 */


if (! defined('ABSPATH') ) {
    exit;
}


use STATS4WP\Core\Options;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

$page_local = ( isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '' );
if ('stats4wp_plugin' === $page_local ) {
    $data = 'all';
} else {
    $data = '';
}


    $param       = AdminGraph::getdate($data);
    $local_table = DB::table('visitor');
    $locations   = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT location, count(*) as nb FROM %s
        WHERE device IN ('bot','')
        AND location NOT IN ('local','none')
        AND last_counter BETWEEN %s AND %s
        GROUP BY location
        ORDER by nb DESC",
            array(
                $local_table,
                $param['from'],
                $param['to'],
            )
        )
    );
    ?>
  <div id ="stats4wp-maps-widget" class="postbox " >
        <div class="postbox-header">
            <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Bots location map', 'stats4wp'); ?></h2>
        </div>
        <div id="world-map" style="width: 600px; height: 400px"></div>
        <script type="text/javascript">
        function defered(method) {
            if (window.jQuery && window.jQuery.fn.vectorMap) {
                method();
            } else {
                setTimeout(function() { defered(method) }, 50);
            }
        }
        defered(function () {
            console.log("jQuery is now loaded");
            jQuery(function ($) {
                $(function(){
                    $('#world-map').vectorMap({map: 'world_mill',
                        series: {
                            regions: [{
                            values: gdpData,
                            scale: ['#C8EEFF', '#0071A4'],
                            normalizeFunction: 'polynomial'
                            }]
                        },
                        onRegionTipShow: function(e, el, code){
                            el.html(el.html()+' (<?php echo esc_html__('Number', 'stats4wp'); ?> - '+gdpData[code]+')');
                        }
                    });
                });
            });
        });

            var gdpData = {
            <?php

            foreach ( $locations as $location ) {
                echo '"' . esc_html($location->location) . '":' . esc_html($location->nb) . ',';
            }
            ?>
        "UNDEFINED": 0,};
        </script>
  </div>
