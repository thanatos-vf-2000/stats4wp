<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;
use STATS4WP\Api\TimeZone;
use STATS4WP\Core\Options;

?>
<div id="stats4wp-topvisitors-widget" class="postbox ">
    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php _e('Top 10 visitors today', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
    <table width="100%" class="widefat table-stats stats4wp-report-table">
        <tbody>
            <tr>
                <td><?php _e('ID', 'stats4wp'); ?></td>
                <td><?php _e('Views', 'stats4wp'); ?></td>
                <td><?php _e('IP', 'stats4wp'); ?></td>
                <td><?php _e('Browser', 'stats4wp'); ?></td>
                <td><?php _e('OS', 'stats4wp'); ?></td>
                <td><?php _e('Type', 'stats4wp'); ?></td>
            </tr>
            <?php
            $top_visitors = $wpdb->get_results("SELECT *
                FROM ". DB::table('visitor') ." 
                WHERE last_counter='" . TimeZone::getCurrentDate('Y-m-d') . "' 
                AND device!='bot' 
                ORDER BY hits  DESC LIMIT 10");
            $i=1;
            foreach ($top_visitors as $top_visitor) {
                $views = $top_visitor->hits;
                $ip = (Options::get_option('anonymize_ips') == true) ? __('None', 'stats4wp') : $top_visitor->ip;
                $browser = $top_visitor->agent .' - '. $top_visitor->agent_v;
                $os = $top_visitor->platform .' - '. $top_visitor->platform_v;
                $device = $top_visitor->device;
                echo '<tr>
                    <td>'. esc_html($i) .'</td>
                    <td>'. esc_html($views) .'</td>
                    <td>'. esc_html($ip) .'</td>
                    <td>'. esc_html($browser) .'</td>
                    <td>'. esc_html($os) .'</td>
                    <td>'. esc_html($device) .'</td>
                </tr>';
                $i++;
            }
            unset($top_visitors, $i, $views, $ip, $browser, $os);
            ?>
        </tbody>
    </table>
    </div>
</div>
