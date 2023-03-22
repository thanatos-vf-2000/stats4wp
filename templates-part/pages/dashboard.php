<?php
use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

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
            <div class="stats4wp-inline width94 border">
                <p class="title"><?php echo esc_html(__('Type', 'stats4wp')); ?></p>
                <?php
                $uri_types = $wpdb->get_results("SELECT type,count(*) as nb FROM ". DB::table('pages') ." where type!='unknown' AND date BETWEEN '". $param['from'] ."' AND '". $param['to'] ."' group by type order by 2 DESC");
                $uri_types_total = array_sum(array_column($uri_types, 'nb'));
                $uri_types_nb=0;
                echo '<p>';
                foreach ($uri_types as $uri_type) {
                    $uri_types_nb++;
                    $percent = round($uri_type->nb * 100 / $uri_types_total);
                    if ($uri_types_nb <10 && $percent > 0) {
                        echo esc_html($uri_type->type). " ($percent%) - ";
                    }
                }
                echo esc_html(__('All', 'stats4wp')).'</p>';
                ?>
            </div>
        </div>
    </div>
    <?php
}