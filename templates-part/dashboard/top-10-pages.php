<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.1.0
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;
use STATS4WP\Api\TimeZone;

?>
<div id="stats4wp-pages-widget" class="postbox ">
    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php _e('Top 10 Pages today', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
        <table width="100%" class="widefat table-stats stats4wp-report-table stats4wp-table-fixed">
            <tbody>
                <tr>
                    <td width="10%"><?php _e('ID', 'stats4wp'); ?></td>
                    <td width="40%"<?php _e('Title', 'stats4wp'); ?>></td>
                    <td width="40%"><?php _e('Link', 'stats4wp'); ?></td>
                    <td width="10%"><?php _e('Visits', 'stats4wp'); ?></td>
                </tr>
                <?php
                $top_pages = $wpdb->get_results("SELECT *
                    FROM ". DB::table('pages') ." 
                    WHERE date='" . TimeZone::getCurrentDate('Y-m-d') . "' 
                    AND type='page' 
                    ORDER BY count  DESC LIMIT 10");
                $i=1;
                foreach($top_pages as $top_page) {
                    $title = get_the_title($top_page->id);
                    $link = $top_page->uri;
                    $nb = $top_page->count;
                    echo '<tr>
                    <td style="text-align: left;">' . esc_html($i) . '</td>
                    <td style="text-align: left;"><span title="'. esc_attr($title) .'" class="stats4wp-cursor-default stats4wp-text-wrap">'. esc_html($title) .'</span></td>
                    <td style="text-align: left;"><a href="'. esc_url($link) .'" title="Page d’accueil" target="_blank">'. esc_html($link) .'</a></td>
                    <td style="text-align: left" class="stats4wp-text-danger">'. esc_html($nb) .'</td>
                </tr>';
                    $i++;
                }
                unset($top_page, $i, $title, $link, $nb);
                ?>
            </tbody>
        </table>
    </div>
</div>