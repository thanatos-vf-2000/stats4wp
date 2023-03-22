<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 */

namespace STATS4WP\Ui;

use STATS4WP\Core\BaseController;
use STATS4WP\Core\DB;

class DashboardWidgetAdmin extends BaseController
{
    public function register()
    {
        add_action('wp_dashboard_setup', array($this, 'custom_dashboard_widget'));
    }

    public function custom_dashboard_widget()
    {
        wp_add_dashboard_widget(
            'dashboard_widget_stats4wp',
            __('Statistics For Wordpress', 'stats4wp'),
            array($this, 'custom_dashboard_widget_content'),
            $control_callback = null
        );
    }

    public function custom_dashboard_widget_content()
    {
        global $wpdb;
        echo '<div class="activity-block"><h3>' . esc_html(__('Tables:', 'stats4wp')) . '</h3><ul>';

        foreach (DB::$db_table as $table) {
            if (DB::ExistRow($table)) {
                $nbrows = $wpdb->get_row("SELECT count(*) as nb FROM " . DB::table($table));
                $msg = $nbrows->nb;
            } else {
                $msg = __('No data found.', 'stats4wp');
            }
            printf('<li class="%1$s-count">%1$s: %2$s</li>', $table, $msg);
        }
        echo '</ul></div>
        <div class="activity-block"><h3>' . esc_html(__('User connected:', 'stats4wp')) . '</h3><ul>';
        if (DB::ExistRow('useronline')) {
            $useronlines = $wpdb->get_results("SELECT user_id, location FROM ". DB::table('useronline') ." 
                ORDER by user_id ASC");
            foreach ($useronlines as $useronline) {
                if ($useronline->user_id == 0) {
                    $username = __('Anonymous', 'stats4wp');
                } else {
                    $user = $wpdb->get_row("SELECT * from $wpdb->users where ID=" . $useronline->user_id);
                    $username = $user->user_login;
                }
               
                echo '<li>' . esc_html($username) .  ' (' . esc_html($useronline->location). ')</li>';
            }
        } else {
            echo '<li>' .  __('No Users.', 'stats4wp').'</li>';
        }

        echo '</ul></div>';
    }
}
