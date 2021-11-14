<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.1.0
 * 
 * Desciption: Admin Page Pages
 */

use STATS4WP\Api\AdminGraph;
use STATS4WP\Core\DB;
?>

<div class="rows stats4wp-dashboard-header">
    <p><?php echo esc_html(__('Statistics', 'stats4wp'). ' '. date('Y'));?></p>
    <?php
    
    AdminGraph::select_date_dashboard();
    if (DB::ExistRow('visitor')) {
        $data = AdminGraph::getdate('local');
        echo '<p class="stats4wp-min">'. esc_html($data['from']) . ' - '. esc_html($data['to']) . '</p>';
    } else {
        echo '<p class="stats4wp-min">'. esc_html(__('No data found to visitor.','stats4wp')). '</p>';
    }
    ?>
</div>
