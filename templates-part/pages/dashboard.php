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

$page_local = ( isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '' );
if ('stats4wp_plugin' === $page_local ) {
    $data = 'all';
} else {
    $data = '';
}

if (DB::exist_row('visitor') ) {
    $param = AdminGraph::getdate($data);
    ?>
    <div class="stats4wp-dashboard">
        <div class="stats4wp-rows">
            <div class="stats4wp-inline width94 border">
                <p class="title"><?php echo esc_html(__('Type', 'stats4wp')); ?></p>
                <?php
                if (! isset($wpdb->stats4wp_pages) ) {
                    $wpdb->stats4wp_pages = DB::table('pages');
                }
                $uri_types       = $wpdb->get_results($wpdb->prepare("SELECT type,count(*) as nb FROM {$wpdb->stats4wp_pages} where type!='unknown' AND date BETWEEN %s AND %s group by type order by 2 DESC", $param['from'], $param['to']));
                $uri_types_total = array_sum(array_column($uri_types, 'nb'));
                $uri_types_nb    = 0;
                echo '<p>';
                foreach ( $uri_types as $uri_type ) {
                    $uri_types_nb++;
                    $percent = round($uri_type->nb * 100 / $uri_types_total);
                    if ($uri_types_nb < 10 && $percent > 0 ) {
                        echo esc_html($uri_type->type) . esc_html(" ($percent&#37;) - ");
                    }
                }
                echo esc_html(__('All', 'stats4wp')) . '</p>';
                ?>
            </div>
        </div>
    </div>
    <?php
}
