<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.15
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

    $param = AdminGraph::getdate($data);
if (! isset($wpdb->stats4wp_visitor) ) {
    $wpdb->stats4wp_visitor = DB::table('visitor');
}
    $locations = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT location, count(*) as nb FROM {$wpdb->stats4wp_visitor} 
			WHERE device NOT IN ('bot','')
			AND location NOT IN ('local','none')
			AND last_counter BETWEEN %s AND %s
			GROUP BY location
			ORDER by nb DESC",
            $param['from'],
            $param['to']
        )
    );
    // Charger le script admin.js
    wp_enqueue_script(
        'chart-js-location',
        STATS4WP_URL . 'assets/js/location-maps.js',
        array('jquery', 'chart-js'),
        null,
        true
    );

    // Générer les données GDP en PHP
    $gdp_data = array();
    if (!empty($locations)) {
        foreach ($locations as $location) {
            $gdp_data[$location->location] = (int) $location->nb;
        }
    }
    $gdp_data["UNDEFINED"] = 0;

    // Passer les données à admin.js
    wp_localize_script(
        'chart-js-location', 'stats4wpData', array(
        'gdpData' => $gdp_data,
        'regionText' => esc_html__('Number', 'stats4wp'),
        )
    );
    ?>
  <div id ="stats4wp-maps-widget" class="postbox " >
        <div class="postbox-header">
            <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Location map', 'stats4wp'); ?></h2>
        </div>
        <div id="world-map" style="width: 600px; height: 400px"></div>
  </div>
