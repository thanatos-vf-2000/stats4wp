<?php
/**
 *
 * @package STATS4WPPlugin
 * @version 1.4.14
 */
namespace STATS4WP\Api;

class User
{

    /**
     * Check User is Logged in WordPress
     *
     * @return mixed
     */
    public static function is_login()
    {
        return \is_user_logged_in();
    }

    /**
     * Get Current User ID
     *
     * @return int
     */
    public static function get_user_id()
    {
        $user_id = 0;
        if (self::is_login() === true ) {
            $user_id = \get_current_user_id();
        }

        return apply_filters('stats4wp_user_id', $user_id);
    }
}
