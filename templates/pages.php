<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.5
 *
 * Desciption: Admin Page Pages
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Api\AdminGraph;

self::get_template( 'header' );
settings_errors();

AdminGraph::select_date();

if ( isset( $_GET['spage'] ) ) {
	self::get_template( array( 'pages/' . sanitize_text_field( wp_unslash( $_GET['spage'] ) ), 'footer' ) );
} else {
	self::get_template( array( 'pages/top', 'footer' ) );
}
