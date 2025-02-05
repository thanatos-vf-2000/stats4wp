<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
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

        delete_option(STATS4WP_NAME . '_plugin');
        if (is_multisite() ) {
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ( $blog_ids as $blog_id ) {
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

        if (is_multisite() && $network_wide ) {
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            switch_to_blog($blog_id);
            // Drop the tables
            foreach ( DB::table() as $tbl ) {
                $wpdb->stats4wp_tmp = $tbl;
                $wpdb->query("DROP TABLE IF EXISTS $wpdb->stats4wp_tmp");
            }
            restore_current_blog();
        } else {
            // Drop the tables
            foreach ( DB::table() as $tbl ) {
                $wpdb->stats4wp_tmp = $tbl;
                $wpdb->query("DROP TABLE IF EXISTS $wpdb->stats4wp_tmp");
            }
        }
    }
}
