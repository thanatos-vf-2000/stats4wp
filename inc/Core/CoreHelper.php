<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 */
namespace STATS4WP\Core;

class CoreHelper
{
    /**
     * Check is Login Page
     *
     * @return bool
     */
    public static function is_login_page()
    {

        // Check From global WordPress
        if (isset($GLOBALS['pagenow']) and in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
            return true;
        }

        // Check Native php
        $protocol   = strpos(strtolower(sanitize_text_field($_SERVER['SERVER_PROTOCOL'])), 'https') === false ? 'http' : 'https';
        $host       = sanitize_text_field($_SERVER['HTTP_HOST']);
        $script     = sanitize_text_field($_SERVER['SCRIPT_NAME']);
        $currentURL = $protocol . '://' . $host . $script;
        $loginURL   = wp_login_url();
        if ($currentURL == $loginURL) {
            return true;
        }

        return false;
    }

    /**
     * Remove Query String From Url
     *
     * @param $url
     * @return bool|string
     */
    public static function RemoveQueryStringUrl($url)
    {
        return substr($url, 0, strrpos($url, "?"));
    }
}
