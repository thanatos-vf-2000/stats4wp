<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 */
namespace STATS4WP\Api;

use STATS4WP\Core\Options;

class Referred
{
    /**
     * Get referer URL
     *
     * @return string
     */
    public static function getRefererURL()
    {
        return (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
    }

    /**
     * Return the referrer link for the current user.
     *
     * @return array|bool|string
     */
    public static function get()
    {

        // Get Default
        $referred = self::getRefererURL();

        // Sanitize Referer Url
        $referred = esc_sql(strip_tags($referred));

        // If Referer is Empty then use same WebSite Url
        if (empty($referred)) {
            $referred = get_bloginfo('url');
        }

        // Check Search Engine
        if (Options::get_option('addsearchwords', false)) {

            // Check to see if this is a search engine referrer
            $SEInfo = SearchEngine::getByUrl($referred);
            if (is_array($SEInfo)) {

                // If we're a known SE, check the query string
                if ($SEInfo['tag'] != '') {
                    $result = SearchEngine::getByQueryString($referred);

                    // If there were no search words, let's add the page title
                    if ($result == '' || $result == SearchEngine::$error_found) {
                        $result = wp_title('', false);
                        if ($result != '') {
                            $referred = esc_url(add_query_arg($SEInfo['querykey'], urlencode('~"' . $result . '"'), $referred));
                        }
                    }
                }
            }
        }

        return apply_filters('stats4wp_user_referer', $referred);
    }
}