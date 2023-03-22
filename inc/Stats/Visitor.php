<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.0
 */
namespace STATS4WP\Stats;

use STATS4WP\Api\IP;
use STATS4WP\Api\UserAgent;
use STATS4WP\Api\TimeZone;
use STATS4WP\Api\User;
use STATS4WP\Api\Referred;
use STATS4WP\Api\GeoIP;

use STATS4WP\Core\DB;
use STATS4WP\Core\Options;

class Visitor
{

    public function register()
    {
        if (Options::get_option('version') == STATS4WP_VERSION) {
            add_action('init', array( $this,'visitor'));
        }
    }

    public function visitor()
    {
        global $wpdb;
        
        // Get User IP
        $user_ip = IP::StoreIP();

        // Get User Agent
        $user_agent = UserAgent::getUserAgent();

        //Check Exist This User in Current Day
        $same_visitor = self::exist_ip_in_day($user_ip, $user_agent);

        // If we have a new Visitor in Day
        if (!$same_visitor) {
            // Prepare Visitor information
            $language = substr(sanitize_text_field($_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 2);
            $visitor = array(
                'last_counter'  => TimeZone::getCurrentDate('Y-m-d'),
                'hour'          => date("H:i:s"),
                'referred'      => Referred::get(),
                'agent'         => $user_agent['browser'],
                'agent_v'       => $user_agent['b-version'],
                'platform'      => $user_agent['platform'],
                'platform_v'    => $user_agent['p-version'],
                'engine'        => $user_agent['engine'],
                'engine_v'      => $user_agent['e-version'],
                'device'        => $user_agent['device'],
                'manufacturer'  => $user_agent['device-manufacturer'],
                'model'         => $user_agent['device-model'],
                'ip'            => $user_ip,
                'location'      => GeoIP::getCountry($user_ip),
                'user_id'       => User::get_user_id(),
                'language'      => ($language == '') ? '#' : $language,
                'UAString'      => (Options::get_option('store_ua') == true ? UserAgent::getHttpUserAgent() : ''),
                'hits'          => 1,
            );
            $visitor = apply_filters('stats4wp_visitor_information', $visitor);

            //Save Visitor TO DB
            $visitor_id = self::save_visitor($visitor);
        } else {
            //Get Current Visitor ID
            $visitor_id = $same_visitor->ID;
            // Update Visitor Count in DB
            $wpdb->query($wpdb->prepare('UPDATE `' . DB::table('visitor') . '` SET `hits` = `hits` + %d WHERE `ID` = %d', 1, $visitor_id));
        }
    }
    /**
     * Check This ip has recorded in Custom Day
     *
     * @param $ip
     * @param $date
     * @return bool
     */
    public static function exist_ip_in_day($ip, $agent, $date = false)
    {
        global $wpdb;
        $visitor = $wpdb->get_row("SELECT * FROM `" . DB::table('visitor') . "` WHERE `last_counter` = '" . ($date === false ? TimeZone::getCurrentDate('Y-m-d') : $date) . "' AND `ip` = '{$ip}' AND `agent` = '" . $agent['browser']. "'");
        return (!$visitor ? false : $visitor);
    }

    /**
     * Save new Visitor To DB
     *
     * @param array $visitor
     * @return INT
     */
    public static function save_visitor($visitor = array())
    {
        global $wpdb;

        # Save to WordPress Database
        $insert = $wpdb->insert(
            DB::table('visitor'),
            $visitor
        );
        if (!$insert) {
            if (!empty($wpdb->last_error) && WP_DEBUG) {
                error_log($wpdb->last_error);
            }
        }

        # Get Visitor ID
        $visitor_id = $wpdb->insert_id;

        return $visitor_id;
    }
}
