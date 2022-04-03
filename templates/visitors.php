<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.2
 * 
 * Desciption: Admin Page Visitor
 */

use STATS4WP\Api\AdminGraph;

self::get_template('header');
settings_errors();

AdminGraph::select_date();
?>

<?php

if (isset($_GET['spage'])) {
    foreach(glob(STATS4WP_PATH.'/templates-part/visitor/' . $_GET['spage'] . '-*.php') as $file) {
        $spage_type = basename($file, '.php');
        self::get_template(array('visitor/'.sanitize_text_field($spage_type)));
    }
    self::get_template(array('visitor/'.sanitize_text_field($_GET['spage']), 'footer'));
} else {
    self::get_template(array('visitor/nb-users', 'visitor/dashboard', 'visitor/agent', 'visitor/os', 'visitor/device', 'visitor/location','visitor/lang', 'footer'));
}
?>