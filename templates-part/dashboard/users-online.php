<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.2
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;
use STATS4WP\Api\TimeZone;
use STATS4WP\Core\Options;

?>
<div id="stats4wp-useronline-widget" class="postbox ">
    <div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php _e('Users Online', 'stats4wp'); ?></h2>
    </div>
    <div class="inside">
        <table class="widefat table-stats stats4wp-report-table stats4wp-table-fixed">
            <tbody>
                <tr>
                    <td style="text-align: left;"><?php _e('IP', 'stats4wp'); ?></td>
                    <td width="35%" style="text-align: left;"><?php _e('Page', 'stats4wp'); ?></td>
                    <td style="text-align: left;"><?php _e('Référant', 'stats4wp'); ?></td>
                </tr>
                <?php
                $users_online = $wpdb->get_results("SELECT ip, referred, user_id, page_id  
                    FROM ". DB::table('useronline'));
                foreach ($users_online as $user_online) {
                    $ip = (Options::get_option('anonymize_ips') == true) ? __('None', 'stats4wp') : $user_online->ip;
                    $referred = parse_url($user_online->referred, PHP_URL_HOST);
                    $page_online = $wpdb->get_row("SELECT uri, type  
                        FROM ". DB::table('pages') . " 
                        WHERE date='" . TimeZone::getCurrentDate('Y-m-d') . "' 
                        AND page_id='" . $user_online->page_id . "' ");
                    if ($page_online == null) {
                        $page_txt = __('None', 'stats4wp');
                        $page_uri = __('None', 'stats4wp');
                    } else {
                        if (in_array($page_online->type, array("home", "loginpage"))) {
                            $page_txt = $page_online->type;
                            $page_uri = $page_online->uri;
                        } elseif ($page_online->type == "attachment") {
                            $page_txt = basename($page_online->uri);
                            $page_uri = $page_online->uri;
                        } else {
                            $page_txt = basename(dirname($page_online->uri), "/");
                            $page_uri = $page_online->uri;
                        }
                    }
                    
                    echo '<tr>
                            <td style="text-align: left !important">' . esc_html($ip) . '</td>
                            <td style="text-align: left !important;"><span class="stats4wp-text-wrap"><a href="' . esc_url($page_uri) . '" title="' . esc_attr($page_txt) . '" target="_blank" class="stats4wp-text-danger">' . esc_html($page_txt) . '</a></span></td>
                            <td style="text-align: left !important"><a href="' . esc_url($user_online->referred) . '" title="' . esc_attr($user_online->referred) . '">' . esc_html($referred) . '</a></td>
                        </tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
