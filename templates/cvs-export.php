<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 *
 * Desciption: CVS Export date
 */

global $wpdb ;
use STATS4WP\Core\DB;

self::get_template(array('header'));

?>
<div class="wp-clearfix"></div>
<div class="metabox-holder" id="overview-widgets">
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
            <tr>
                <th></th>
                <th colspan="2" ><?php _e('Number of rows', 'stats4wp'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (array('visitor', 'pages') as $t) {
                $num = $wpdb->get_row("SELECT count(*) as nb 
                        FROM ". DB::table($t));
                switch ($t) {
                    case 'visitor';
                        $field = 'last_counter';
                        break;
                    case 'pages':
                        $field = 'date';
                        break;
                }
                $years = $wpdb->get_results("SELECT DISTINCT(YEAR(" . $field . ")) as y FROM ". DB::table($t) ." 
                ORDER BY 1 ASC");
                echo "<tr>
                        <td>" . esc_html($t). "</td>
                        <td>" . esc_html($num->nb). "</td>
                        <td><a href=\"?page=stats4wp_cvsexport&report=". $t ."\" target=\"_blank\"> ". __('Export All', 'stats4wp') ."</a>";
                foreach ($years as $year) {
                    echo " - <a href=\"?page=stats4wp_cvsexport&report=". $t ."&year=" . $year->y . "\" target=\"_blank\"> ". $year->y ."</a>";
                }
                echo "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<?php
self::get_template(array('footer'));
?>
