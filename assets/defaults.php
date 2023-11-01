<?php
/**
 * Default variables
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   STATS4WPPlugin
 * @author    Franck VANHOUCKE <ct4gg@ginkgos.net>
 * @copyright 2021-2023 Copyright 2023, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later
 * @version   1.4.5 GIT:https://github.com/thanatos-vf-2000/WordPress
 * @link      https://ginkgos.net
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'anonymize_ips'    => array(
		'title'   => __( 'Anonymize IP.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'ip_method'        => array(
		'title'   => __( 'IP methode', 'stats4wp' ),
		'message' => __( 'chose _SERVER.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'listField',
		'choices' => array(
			'REMOTE_ADDR'              => __( 'REMOTE_ADDR', 'stats4wp' ),
			'HTTP_CLIENT_IP'           => __( 'HTTP_CLIENT_IP', 'stats4wp' ),
			'HTTP_X_FORWARDED_FOR'     => __( 'HTTP_X_FORWARDED_FOR', 'stats4wp' ),
			'HTTP_X_FORWARDED'         => __( 'HTTP_X_FORWARDED', 'stats4wp' ),
			'HTTP_FORWARDED_FOR'       => __( 'HTTP_FORWARDED_FOR', 'stats4wp' ),
			'HTTP_FORWARDED'           => __( 'HTTP_FORWARDED', 'stats4wp' ),
			'HTTP_X_REAL_IP'           => __( 'HTTP_X_REAL_IP', 'stats4wp' ),
			'HTTP_X_CLUSTER_CLIENT_IP' => __( 'HTTP_X_CLUSTER_CLIENT_IP', 'stats4wp' ),
		),
	),
	'addsearchwords'   => array(
		'title'   => __( 'Add search words', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'store_ua'         => array(
		'title'   => __( 'Store User Agent.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'check_online'     => array(
		'title'   => __( 'Max time user Online check', 'stats4wp' ),
		'message' => __( 'Delete user in table useronile after xxx seconds (default 120).', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'TextField',
	),
	'top_page'         => array(
		'title'   => __( 'Number of result in top pages', 'stats4wp' ),
		'message' => __( '(default 10).', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'TextField',
	),
	'disableadminstat' => array(
		'title'   => __( 'Disabled statistics for the administration part (/wp-admin/).', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
);
