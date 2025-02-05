<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
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
    public static function get_referer_url()
    {
        return ( isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])) : '' );
    }

    /**
     * Return the referrer link for the current user.
     *
     * @return array|bool|string
     */
    public static function get()
    {

        // Get Default
        $referred = self::get_referer_url();

        // Sanitize Referer Url
        $referred = esc_sql(wp_strip_all_tags($referred));

        // If Referer is Empty then use same WebSite Url
        if (empty($referred) ) {
            $referred = get_bloginfo('url');
        }

        // Check Search Engine
        if (Options::get_option('addsearchwords', false) ) {
            // Check to see if this is a search engine referrer
            $se_info = SearchEngine::get_by_url($referred);
            if (is_array($se_info) ) {
                // If we're a known SE, check the query string
                if ('' !== $se_info['tag'] ) {
                    $result = SearchEngine::get_by_query_string($referred);

                    // If there were no search words, let's add the page title
                    if ('' === $result || SearchEngine::$error_found === $result ) {
                        $result = wp_title('', false);
                        if ('' !== $result ) {
                               $referred = esc_url(add_query_arg($se_info['querykey'], urlencode('~"' . $result . '"'), $referred));
                        }
                    }
                }
            }
        }

        return apply_filters('stats4wp_user_referer', $referred);
    }
}
