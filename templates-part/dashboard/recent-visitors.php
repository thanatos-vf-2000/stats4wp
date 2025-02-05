<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */

if (! defined('ABSPATH') ) {
    exit;
}

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;
use STATS4WP\Api\TimeZone;
use STATS4WP\Core\Options;

?>
<div id="stats4wp-recentvisitors-widget" class="postbox ">
    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php esc_html_e('Recent visitors', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
    <table width="100%" class="widefat table-stats stats4wp-report-table">
        <tbody>
            <tr>
                <td><?php esc_html_e('Browser', 'stats4wp'); ?></td>
                <td><?php esc_html_e('Day', 'stats4wp'); ?></td>
                <td><?php esc_html_e('IP', 'stats4wp'); ?></td>
                <td><?php esc_html_e('Referring', 'stats4wp'); ?></td>
            </tr>
            <?php
            if (! isset($wpdb->stats4wp_visitor) ) {
                $wpdb->stats4wp_visitor = DB::table('visitor');
            }
            $recent_visitors = $wpdb->get_results(
                "SELECT *
                FROM $wpdb->stats4wp_visitor 
                WHERE device!='bot' 
                ORDER BY id  DESC LIMIT 10"
            );
            $i               = 1;
            foreach ( $recent_visitors as $recent_visitor ) {
                $browser      = $recent_visitor->agent;
                $day          = gmdate(get_option('date_format'), strtotime($recent_visitor->last_counter));
                $ip           = ( true === Options::get_option('anonymize_ips') ) ? __('None', 'stats4wp') : $recent_visitor->ip;
                $referred_txt = wp_parse_url($recent_visitor->referred, PHP_URL_HOST);
                $referred     = $recent_visitor->referred;
                echo '<tr>
                        <td style="text-align: left">
                            <img src="' . esc_attr(STATS4WP_URL) . '/assets/images/browser/' . esc_attr($browser) . '.png" alt="' . esc_attr($browser) . '" class="log-tools" title="' . esc_attr($browser) . '">
                        </td>
                        <td style="text-align: left">' . esc_html($day) . '</td>
                        <td style="text-align: left">' . esc_html($ip) . '</td>
                        <td style="text-align: left"><a href="' . esc_url($referred) . '" title="' . esc_attr($referred) . '">' . esc_html($referred_txt) . '</a></td>
                    </tr>';
                $i++;
            }
            unset($recent_visitors, $i, $browser, $day, $ip, $referred_txt, $referred);
            ?>
        </tbody>
    </table>
    </div>
</div>
