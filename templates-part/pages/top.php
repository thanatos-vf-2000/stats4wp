<?php

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
            <div class="stats4wp-top stats4wp-inline border">
                <?php
                $uris = $wpdb->get_results("SELECT type,uri,sum(count) as nb FROM ". DB::table('pages') ." where type!='unknown' AND date BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' group by type,uri order by 3 DESC");
                $uris_total = array_sum(array_column($uris, 'nb'));
                $uri_nb=0;
                ?>
                <table>
                    <thead>
                        <tr>
                            <th><?php echo esc_html(__('Type', 'stats4wp')); ?></th>
                            <th><?php echo esc_html(__('URI', 'stats4wp')); ?></th>
                            <th><?php echo esc_html(__('Call', 'stats4wp')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                foreach ( $uris as $uri ) {
                    $uri_nb++;
                    $percent = round($uri->nb * 100 / $uris_total);
                    if ($uri_nb < Options::get_option('top_page') && $percent > 0 ) echo '<tr><td>' . esc_html($uri->type). '</td><td>' . esc_html($uri->uri). "</td><td>$percent%</td></tr>";
                }
                ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}