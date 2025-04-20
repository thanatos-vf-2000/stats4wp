<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 *
 * Desciption: Admin Page Visitor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Api\AdminGraph;

self::get_template( 'header' );
settings_errors();

AdminGraph::select_date();

if ( isset( $_GET['spage'] ) ) {
	foreach ( glob( STATS4WP_PATH . '/templates-part/visitor/' . sanitize_text_field( wp_unslash( $_GET['spage'] ) ) . '-*.php' ) as $file ) {
		$spage_type = basename( $file, '.php' );
		self::get_template( array( 'visitor/' . sanitize_text_field( $spage_type ) ) );
	}
	self::get_template( array( 'visitor/' . sanitize_text_field( wp_unslash( $_GET['spage'] ) ), 'footer' ) );
} else {
	self::get_template( array( 'visitor/nb-users', 'visitor/dashboard', 'visitor/agent', 'visitor/os', 'visitor/device', 'visitor/location', 'visitor/lang', 'footer' ) );
}
