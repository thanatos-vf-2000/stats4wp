<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 *
 * Desciption: Settings tables
 */

use STATS4WP\Core\DB;
use STATS4WP\Core\Args;

global $wpdb;

$arg[0] = Args::getARGValue('submit-delete-tables', false, function ($val) {
    return $val;
});
$arg[1] = Args::getARGValue('delete-day', false, function ($val) {
    return $val;
});

if ($arg[0] == __("Purge tables", 'stats4wp')) {
    $wpdb->query("DELETE FROM ".DB::table('visitor'). ' WHERE last_counter<="' . $arg[1] . '"');
    $wpdb->query("DELETE FROM ".DB::table('pages'). ' WHERE date<="' . $arg[1] . '"');
    echo '<div class="stats4wp-rows notice notice-success">' . esc_html($arg[1]) . ' - ' . __('Purged tables with succes.', 'stats4wp'). '</div>';
}

$nb_visitors = (int)$wpdb->get_var("SELECT COUNT(*) FROM ". DB::table('visitor'));
$min_visitors = $wpdb->get_var("SELECT MIN(last_counter) FROM ". DB::table('visitor'));
$nb_pages = (int)$wpdb->get_var("SELECT COUNT(*) FROM ". DB::table('pages'));
$min_pages = $wpdb->get_var("SELECT MIN(date) FROM ". DB::table('pages'));

?>
<div class="stats4wp-dashboard">
    <div>
<?php
echo '<table class="widefat striped health-check-table">
    <thead>
        <tr>
            <th>'. __('Tables', 'stats4wp') .'</th>
            <th>'. __('Number of rows', 'stats4wp') .'</th>
            <th>'. __('Min day', 'stats4wp') .'</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>'. __('Visitor', 'stats4wp') .'</td>
            <td>'. esc_html($nb_visitors) .'</td>
            <td>'. esc_html($min_visitors) .'</td>
        </tr>
        <tr>
            <td>'. __('Pages', 'stats4wp') .'</td>
            <td>'. esc_html($nb_pages) .'</td>
            <td>'. esc_html($min_pages) .'</td>
        </tr>
    </tbody>
    </table>';

$delele_day = date("Y-m-d", strtotime('-1 years'));
if ($delele_day < $min_visitors) {
    $delele_day = $min_visitors;
}
echo '<form method="GET" action="' .admin_url('admin.php') . '">
        <input type="hidden" name="page" value="stats4wp_settings"/>
        <label>'. _e('Before: ', 'stats4wp'). '
            <input type="date" name="delete-day" value="' . $delele_day .'"/>
        </label>';
    submit_button(__("Purge tables", 'stats4wp'), 'primary', 'submit-delete-tables', false);
    echo '</form>';
?>
    </div>
</div>
