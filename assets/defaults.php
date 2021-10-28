<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 */
return array(
    'anonymize_ips' => array(
        'title'     => __('Anonymize IP.','stats4wp'),
        'section' => STATS4WP_NAME.'_admin_index',
        'type'      => 'checkboxField') ,
    'ip_method' => array(
        'title'     => __('IP methode','stats4wp'),
        'message'   => __('chose $_SERVER.', 'stats4wp'),
        'section'   => STATS4WP_NAME.'_admin_index',
        'type'      => 'listField' ,
        'choices'   => array( 
            'REMOTE_ADDR'     => __('REMOTE_ADDR','stats4wp'),
            'HTTP_CLIENT_IP'     => __('HTTP_CLIENT_IP','stats4wp'),
            'HTTP_X_FORWARDED_FOR'     => __('HTTP_X_FORWARDED_FOR','stats4wp'),
            'HTTP_X_FORWARDED'     => __('HTTP_X_FORWARDED','stats4wp'),
            'HTTP_FORWARDED_FOR'     => __('HTTP_FORWARDED_FOR','stats4wp'),
            'HTTP_FORWARDED'     => __('HTTP_FORWARDED','stats4wp'),
            'HTTP_X_REAL_IP'     => __('HTTP_X_REAL_IP','stats4wp'),
            'HTTP_X_CLUSTER_CLIENT_IP'     => __('HTTP_X_CLUSTER_CLIENT_IP','stats4wp')
            )
        ),
    'addsearchwords' => array(
        'title'     => __('Add search words','stats4wp'),
        'section'   => STATS4WP_NAME.'_admin_index',
        'type'      => 'checkboxField') ,
    'store_ua' => array(
        'title'     => __('Store User Agent.','stats4wp'),
        'section'   => STATS4WP_NAME.'_admin_index',
        'type'      => 'checkboxField'),
    'check_online'  => array(
        'title'     => __('Max time user Online check','stats4wp'),
        'message'   => __('Delete user in table useronile after xxx seconds (default 120).', 'stats4wp'),
        'section'   => STATS4WP_NAME.'_admin_index',
        'type'      => 'TextField'),
    'top_page'  => array(
        'title'     => __('Number of result in top pages','stats4wp'),
        'message'   => __('(default 10).', 'stats4wp'),
        'section'   => STATS4WP_NAME.'_admin_index',
        'type'      => 'TextField')
);