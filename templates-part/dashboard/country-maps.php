<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.15
 *
 * Desciption: Contry Maps
 */

if (! defined('ABSPATH') ) {
    exit;
}

use STATS4WP\Core\Options;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

if (! isset($wpdb->stats4wp_visitor) ) {
    $wpdb->stats4wp_visitor = DB::table('visitor');
}
    $param = AdminGraph::getdate('');
    $locations = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT location, count(*) as nb FROM $wpdb->stats4wp_visitor 
		WHERE device NOT IN ('bot','')
		AND location NOT IN ('local','none')
		AND last_counter BETWEEN %s AND %s
		GROUP BY location
		ORDER by nb DESC",
            array(
                $param['from'],
                $param['to'],
            )
        )
    );
    wp_enqueue_script(
        'chart-js-script',
        STATS4WP_URL . 'assets/js/country-maps.js',
        array('jquery', 'chart-js'),
        null,
        true
    );
    // Préparer les données pour JavaScript
    $gdp_data = array();
    if (!empty($locations)) {
        foreach ($locations as $location) {
            $gdp_data[$location->location] = (int) $location->nb;
        }
    }
    $gdp_data["UNDEFINED"] = 0;

    // Passer les données à admin.js
    wp_localize_script(
        'chart-js-script', 'stats4wpData', array(
        'gdpData' => $gdp_data,
        'regionText' => esc_html__('Number', 'stats4wp'),
        )
    );
    ?>
    <div id ="stats4wp-maps-widget" class="postbox " >
        <div class="postbox-header">
            <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Users country maps for last year', 'stats4wp'); ?></h2>
        </div>
        <div id="world-map" style="width: 600px; height: 400px"></div>
    </div>
