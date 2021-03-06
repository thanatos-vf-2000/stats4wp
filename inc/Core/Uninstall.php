<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 */
namespace STATS4WP\Core;

class Uninstall
{
    /**
     * Uninstall
     *
     * @param $network_wide
     */
    public static function uninstall()
    {
        global $wpdb;

        delete_option( STATS4WP_NAME.'_plugin' );
        if (is_multisite()) {

            $blog_ids = $wpdb->get_col("SELECT `blog_id` FROM $wpdb->blogs");
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                self::site_removal();
                restore_current_blog();
            }

        } else {
            self::site_removal();
        }
    }

    /**
     * Removes database options, user meta keys & tables
     */
    public static function site_removal()
    {
        global $wpdb;

        // Drop the tables
        foreach (DB::table() as $tbl) {
            $wpdb->query("DROP TABLE IF EXISTS {$tbl}");
        }
    }
}