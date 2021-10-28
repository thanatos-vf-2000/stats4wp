<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 * 
 * Desciption: Admin Page Pages
 */

use STATS4WP\Api\AdminGraph;

self::get_template('header');
settings_errors();

AdminGraph::select_date();
?>

<?php
if (isset($_GET['spage'])) {
    self::get_template(array('pages/'.sanitize_text_field($_GET['spage']), 'footer'));
} else { 
    self::get_template(array('pages/top', 'footer'));
}

?>