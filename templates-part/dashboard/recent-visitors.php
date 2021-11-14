<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.1.0
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;
use STATS4WP\Api\TimeZone;
use STATS4WP\Core\Options;

?>
<div id="stats4wp-recentvisitors-widget" class="postbox ">
    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php _e('Recent visitors', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
    <table width="100%" class="widefat table-stats stats4wp-report-table">
        <tbody>
            <tr>
                <td><?php _e('Browser', 'stats4wp'); ?></td>
                <td><?php _e('Day', 'stats4wp'); ?></td>
                <td><?php _e('IP', 'stats4wp'); ?></td>
                <td><?php _e('Referring', 'stats4wp'); ?></td>
            </tr>
            <?php
            $recent_visitors = $wpdb->get_results("SELECT *
                FROM ". DB::table('visitor') ." 
                WHERE device!='bot' 
                ORDER BY id  DESC LIMIT 10");
            $i=1;
            foreach($recent_visitors as $recent_visitor) {
                $browser = $recent_visitor->agent;
                $day = date(get_option( 'date_format' ), strtotime($recent_visitor->last_counter));
                $ip = (Options::get_option('anonymize_ips') == true) ? __('None', 'stats4wp') : $recent_visitor->ip;
                $referred_txt = parse_url($recent_visitor->referred, PHP_URL_HOST);
                $referred = $recent_visitor->referred;
                echo '<tr>
                        <td style="text-align: left">
                            <img src="' . STATS4WP_URL . '/assets/images/browser/'. esc_attr($browser) .'.png" alt="'. esc_attr($browser) .'" class="log-tools" title="'. esc_attr($browser) .'">
                        </td>
                        <td style="text-align: left">' . esc_html($day) . '</td>
                        <td style="text-align: left">'. esc_html($ip) .'</td>
                        <td style="text-align: left"><a href="'. esc_url($referred) .'" title="'. esc_attr($referred) .'">'. esc_html($referred_txt) .'</a></td>
                    </tr>';
                $i++;

            }
            unset($recent_visitors, $i, $browser, $day, $ip, $referred_txt, $referred);
            ?>
            <tr>
                <td style="text-align: left">
                    <a href="http://dev-ginkgos.net.test/wp-admin/admin.php?page=stats4wp_overview_page&amp;agent=Chrome" title="Chrome"><img src="http://dev-ginkgos.net.test/apps/wp-statistics/assets/images/browser/Chrome.png" alt="Chrome" class="log-tools" title="Chrome"></a>
                </td>
                <td style="text-align: left">11 novembre 2021</td>
                <td style="text-align: left"><a href="http://dev-ginkgos.net.test/wp-admin/admin.php?page=stats4wp_visitors_page&amp;ip=172.20.64.1">172.20.64.1</a></td>
                <td style="text-align: left"><a href="http://dev-ginkgos.net.test" title="http://dev-ginkgos.net.test">dev-ginkgos.net.test</a></td>
            </tr>
        </tbody>
    </table>
    </div>
</div>