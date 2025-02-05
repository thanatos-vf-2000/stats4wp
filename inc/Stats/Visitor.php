<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
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
        if (Options::get_option('version') === STATS4WP_VERSION ) {
            add_action('init', array( $this, 'visitor' ));
        }
    }

    public function visitor()
    {
        global $wpdb;

        // Get User IP
        $user_ip = IP::store_ip();

        // Get User Agent
        $user_agent = UserAgent::get_user_agent();

        // Check Exist This User in Current Day
        $same_visitor = self::exist_ip_in_day($user_ip, $user_agent);

        // If we have a new Visitor in Day
        if (! $same_visitor ) {
            // Prepare Visitor information
            if (! empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
                $language = substr(sanitize_text_field(wp_unslash($_SERVER['HTTP_ACCEPT_LANGUAGE'])), 0, 2);
            } else {
                $language = 'no';
            }
            $visitor = array(
            'last_counter' => TimeZone::get_current_gmdate('Y-m-d'),
            'hour'         => gmdate('H:i:s'),
            'referred'     => Referred::get(),
            'agent'        => $user_agent['browser'],
            'agent_v'      => $user_agent['b-version'],
            'platform'     => $user_agent['platform'],
            'platform_v'   => $user_agent['p-version'],
            'engine'       => $user_agent['engine'],
            'engine_v'     => $user_agent['e-version'],
            'device'       => $user_agent['device'],
            'manufacturer' => $user_agent['device-manufacturer'],
            'model'        => $user_agent['device-model'],
            'ip'           => $user_ip,
            'location'     => GeoIP::get_country($user_ip),
            'user_id'      => User::get_user_id(),
            'language'     => ( '' === $language ) ? '#' : $language,
            'UAString'     => ( true === Options::get_option('store_ua') ? UserAgent::get_http_user_agent() : '' ),
            'hits'         => 1,
            );
            $visitor = apply_filters('stats4wp_visitor_information', $visitor);

            // Save Visitor TO DB
            $visitor_id = self::save_visitor($visitor);
        } else {
            // Get Current Visitor ID
            $visitor_id = $same_visitor->ID;
            // Update Visitor Count in DB
            if (! isset($wpdb->stats4wp_visitor) ) {
                $wpdb->stats4wp_visitor = DB::table('visitor');
            }
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE $wpdb->stats4wp_visitor
					SET `hits` = `hits` + %d 
					WHERE `ID` = %d",
                    array(
                        1,
                        $visitor_id,
                    )
                )
            );
        }
    }
    /**
     * Check This ip has recorded in Custom Day
     *
     * @param  $ip
     * @param  $date
     * @return bool
     */
    public static function exist_ip_in_day( $ip, $agent, $date = false )
    {
        global $wpdb;
        if (! isset($wpdb->stats4wp_visitor) ) {
            $wpdb->stats4wp_visitor = DB::table('visitor');
        }
        $calc_date = ( $date === false ? TimeZone::get_current_gmdate('Y-m-d') : $date );
        $agent_c   = $agent['browser'];
        $visitor   = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $wpdb->stats4wp_visitor 
				WHERE `last_counter` = %s 
				AND `ip` = %s 
				AND `agent` = %s",
                array(
                    $calc_date,
                    $ip,
                    $agent_c,
                )
            )
        );
        return ( ! $visitor ? false : $visitor );
    }

    /**
     * Save new Visitor To DB
     *
     * @param  array $visitor
     * @return INT
     */
    public static function save_visitor( $visitor = array() )
    {
        global $wpdb;

        // Save to WordPress Database
        $insert = $wpdb->insert(
            DB::table('visitor'),
            $visitor
        );
        if (! $insert ) {
            if (! empty($wpdb->last_error) && WP_DEBUG ) {
                error_log($wpdb->last_error);
            }
        }

        // Get Visitor ID
        $visitor_id = $wpdb->insert_id;

        return $visitor_id;
    }
}
