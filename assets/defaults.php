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
 * @version   1.4.14 GIT:https://github.com/thanatos-vf-2000/WordPress
 * @link      https://ginkgos.net
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'anonymize_ips'    => array(
		'title'   => stats4wp_t( 'Anonymize IP.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'ip_method'        => array(
		'title'   => stats4wp_t( 'IP methode', 'stats4wp' ),
		'message' => stats4wp_t( 'chose _SERVER.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'listField',
		'choices' => array(
			'REMOTE_ADDR'              => stats4wp_t( 'REMOTE_ADDR', 'stats4wp' ),
			'HTTP_CLIENT_IP'           => stats4wp_t( 'HTTP_CLIENT_IP', 'stats4wp' ),
			'HTTP_X_FORWARDED_FOR'     => stats4wp_t( 'HTTP_X_FORWARDED_FOR', 'stats4wp' ),
			'HTTP_X_FORWARDED'         => stats4wp_t( 'HTTP_X_FORWARDED', 'stats4wp' ),
			'HTTP_FORWARDED_FOR'       => stats4wp_t( 'HTTP_FORWARDED_FOR', 'stats4wp' ),
			'HTTP_FORWARDED'           => stats4wp_t( 'HTTP_FORWARDED', 'stats4wp' ),
			'HTTP_X_REAL_IP'           => stats4wp_t( 'HTTP_X_REAL_IP', 'stats4wp' ),
			'HTTP_X_CLUSTER_CLIENT_IP' => stats4wp_t( 'HTTP_X_CLUSTER_CLIENT_IP', 'stats4wp' ),
		),
	),
	'addsearchwords'   => array(
		'title'   => stats4wp_t( 'Add search words', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'store_ua'         => array(
		'title'   => stats4wp_t( 'Store User Agent.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'check_online'     => array(
		'title'   => stats4wp_t( 'Max time user Online check', 'stats4wp' ),
		'message' => stats4wp_t( 'Delete user in table useronile after xxx seconds (default 120).', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'TextField',
	),
	'top_page'         => array(
		'title'   => stats4wp_t( 'Number of result in top pages', 'stats4wp' ),
		'message' => stats4wp_t( '(default 10).', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'TextField',
	),
	'disableadminstat' => array(
		'title'   => stats4wp_t( 'Disabled statistics for the administration part (/wp-admin/).', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'exclude_bots'        => array(
		'title'   => stats4wp_t( 'Exclude bots & crawlers', 'stats4wp' ),
		'message' => stats4wp_t( 'Stop recording visits from known bots/crawlers entirely (search engines, monitoring tools...). Off by default, since stats4wp already reports on bots separately in the "Bots" tab; enable this only if you want a smaller, bot-free database.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'exclude_roles'       => array(
		'title'   => stats4wp_t( 'Exclude logged-in roles', 'stats4wp' ),
		'message' => stats4wp_t( 'Comma-separated list of WordPress role slugs to exclude from stats, e.g. administrator,editor.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'TextField',
	),
	'respect_dnt'         => array(
		'title'   => stats4wp_t( 'Respect "Do Not Track"', 'stats4wp' ),
		'message' => stats4wp_t( 'Do not record visitors sending the Do Not Track (DNT) browser header.', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'checkboxField',
	),
	'data_retention_days' => array(
		'title'   => stats4wp_t( 'Automatic data retention (days)', 'stats4wp' ),
		'message' => stats4wp_t( 'Automatically delete visitor/page rows older than this many days. Set to 0 to disable (default).', 'stats4wp' ),
		'section' => STATS4WP_NAME . '_admin_index',
		'type'    => 'TextField',
	),
);
