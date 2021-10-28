<div class="rows stats4wp-dashboard-header">
    <p><?php echo esc_html(__('Statistics', 'stats4wp'). ' '. date('Y'));?></p>
    <?php
    use STATS4WP\Core\DB;
    if (DB::ExistRow('visitor')) {
        $visitor = $wpdb->get_row("SELECT min(last_counter) as minimum, max(last_counter) as maximum FROM ". DB::table('visitor'));
        echo '<p class="stats4wp-min">'. esc_html($visitor->minimum) . ' - '. esc_html($visitor->maximum) . '</p>';
    } else {
        echo '<p class="stats4wp-min">'. esc_html(__('No data found to visitor.','stats4wp')). '</p>';
    }
    ?>
</div>
