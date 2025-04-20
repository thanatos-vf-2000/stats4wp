<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.17
 *
 * Desciption: Location Maps
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


use STATS4WP\Core\Options;
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

$page_local = ( isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '' );
if ( 'stats4wp_plugin' === $page_local ) {
	$data = 'all';
} else {
	$data = '';
}

	$param = AdminGraph::getdate( $data );
if ( ! isset( $wpdb->stats4wp_visitor ) ) {
	$wpdb->stats4wp_visitor = DB::table( 'visitor' );
}
	$languages = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT UPPER(language) as language, count(*) as nb FROM {$wpdb->stats4wp_visitor} 
    WHERE device NOT IN ('bot','')
    AND language not in ('','*','#','q=')
    AND last_counter BETWEEN %s AND %s
    GROUP BY language
    ORDER by nb DESC",
			$param['from'],
			$param['to']
		)
	);
	// Charger le script admin.js
	wp_enqueue_script(
		'chart-js-lang',
		STATS4WP_URL . 'assets/js/lang-maps.js',
		array( 'jquery', 'chart-js' ),
		STATS4WP_VERSION,
		true
	);

	// Générer les données GDP en PHP
	$gdp_data = array();
	if ( ! empty( $languages ) ) {
		foreach ( $languages as $language ) {
			$gdp_data[ $language->language ] = (int) $language->nb;
		}
	}
	$gdp_data['UNDEFINED'] = 0;

	// Passer les données à admin.js
	wp_localize_script(
		'chart-js-lang',
		'stats4wpData',
		array(
			'gdpData'    => $gdp_data,
			'regionText' => esc_html__( 'Number', 'stats4wp' ),
		)
	);
	?>
	<div id ="stats4wp-maps-widget" class="postbox " >
		<div class="postbox-header">
			<h2 class="hndle ui-sortable-handle"><?php esc_html_e( 'Users language', 'stats4wp' ); ?></h2>
		</div>
		<div id="world-map" style="width: 600px; height: 400px"></div>
	</div>

