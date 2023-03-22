<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 */

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;
use STATS4WP\Core\Options;

$page = (isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '');
if ($page == 'stats4wp_plugin') {
    $data = 'all';
} else {
    $data ='';
}

if (DB::ExistRow('visitor')) {
    $param = AdminGraph::getdate($data);
    ?>
    <div class="stats4wp-dashboard">
        <div class="stats4wp-rows">
            <div class="stats4wp-top stats4wp-inline stats4wp-width">
                <?php
                $uris = $wpdb->get_results("SELECT type,uri,sum(count) as nb FROM ". DB::table('pages') ." where type!='unknown' AND date BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' group by type,uri order by 3 DESC");
                $uris_total = array_sum(array_column($uris, 'nb'));
                $uri_nb=0;
                ?>
                <table class="widefat table-stats stats4wp-report-table">
                    <tbody>
                        <tr>
                            <td style="width: 1%;"></td>
                            <td style="width: 20%;"><?php echo esc_html(__('Type', 'stats4wp')); ?></td>
                            <td><?php echo esc_html(__('URI', 'stats4wp')); ?></td>
                            <td style="width: 20%;"><b><?php echo esc_html(number_format($uris_total, 0, ',', ' ')); ?></b></td>
                            <td style="width: 20%;"><?php echo esc_html(__('Call', 'stats4wp')); ?></td>
                        </tr>
                <?php
                foreach ($uris as $uri) {
                    $uri_nb++;
                    $percent = round($uri->nb * 100 / $uris_total, 2);
                    $tr_class = ($uri_nb % 2 == 0) ? "stats4wp-bg" : '';
                    if ($uri_nb < Options::get_option('top_page') && $percent > 0) {
                        echo '<tr class="' . esc_attr($tr_class) . '"><td>' . esc_html($uri_nb) . '.</td><td>' . esc_html($uri->type). '</td><td>' . esc_html($uri->uri). '</td><td class="stats4wp-right">'. esc_html(number_format($uri->nb, 0, ',', ' ')) .'</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr($percent) . '%;"></div>' . $percent . "%</td></tr>";
                    }
                }
                if ($uri_nb > Options::get_option('top_page')) {
                    echo '<tr><td colspan="5">' . __('Others ...', 'stats4wp'). '</td></tr>';
                }
                ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}
