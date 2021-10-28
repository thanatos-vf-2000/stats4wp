<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 */
namespace STATS4WP\Api;
use STATS4WP\Core\Options;


class IP
{
    /**
     * Default User IP
     *
     * @var string
     */
    public static $default_ip = '127.0.0.1';

    /**
     * Default Private SubNets
     *
     * @var array
     */
    public static $private_SubNets = array('10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', '127.0.0.1/24', 'fc00::/7');

    /**
     * List Of Common $_SERVER for get Users IP
     *
     * @var array
     */
    public static $ip_methods_server = array('REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_REAL_IP', 'HTTP_X_CLUSTER_CLIENT_IP');

    /**
     * Default $_SERVER for Get User Real IP
     *
     * @var string
     */
    public static $default_ip_method = 'REMOTE_ADDR';

    /**
     * what is Method $_SERVER for get User Real IP
     */
    public static function getIPMethod()
    {
        $ip_method = Options::get_option('ip_method');
        return ($ip_method != false ? $ip_method : self::$default_ip_method);
    }

    /**
     * Returns the current IP address of the remote client.
     *
     * @return bool|string
     */
    public static function getIP()
    {

        // Set Default
        $ip = false;

        // Get User IP Methods
        $ip_method = self::getIPMethod();

        // Check isset $_SERVER
        if (isset($_SERVER[$ip_method])) {
            $ip = sanitize_text_field($_SERVER[$ip_method]);
        }

        /**
         * This Filter Used For Custom $_SERVER String
         */
        $ip = apply_filters('stats4wp_sanitize_user_ip', $ip);

        // Sanitize For HTTP_X_FORWARDED
        foreach (explode(',', $ip) as $user_ip) {
            $user_ip = trim($user_ip);
            if (self::isIP($user_ip) != false) {
                $ip = $user_ip;
            }
        }

        // If no valid ip address has been found, use default ip.
        if (false === $ip) {
            $ip = self::$default_ip;
        }

        return apply_filters('stats4wp_user_ip', $ip);
    }

    /**
     * Store User IP To Database
     */
    public static function StoreIP()
    {

        //Get User ip
        $user_ip = self::getIP();

        // use 127.0.0.1 If no valid ip address has been found.
        if (false === $user_ip) {
            return self::$default_ip;
        }

        // If the anonymize IP enabled for GDPR.
        if (Options::get_option('anonymize_ips') == true) {
            $user_ip = substr($user_ip, 0, strrpos($user_ip, '.')) . '.0';
        }

        return $user_ip;
    }

    /**
     * Check Validation IP
     *
     * @param $ip
     * @return bool
     */
    public static function isIP($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /** ip_in_range
    * This function takes 2 arguments, an IP address and a "range" in several
    * different formats.
    * Network ranges can be specified as:
    * 1. Wildcard format:     1.2.3.*
    * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
    * 3. Start-End IP format: 1.2.3.0-1.2.3.255
    * The function will return true if the supplied IP is within the range.
    * Note little validation is done on the range inputs - it expects you to
    * use one of the above 3 formats.
    */
    public static Function ip_in_range($ip, $range) {
        if (strpos($range, '/') !== false) {
        // $range is in IP/NETMASK format
        list($range, $netmask) = explode('/', $range, 2);
        if (strpos($netmask, '.') !== false) {
            // $netmask is a 255.255.0.0 format
            $netmask = str_replace('*', '0', $netmask);
            $netmask_dec = ip2long($netmask);
            return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
        } else {
            // $netmask is a CIDR size block
            // fix the range argument
            $x = explode('.', $range);
            while(count($x)<4) $x[] = '0';
            list($a,$b,$c,$d) = $x;
            $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
            $range_dec = ip2long($range);
            $ip_dec = ip2long($ip);
    
            # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
            #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));
    
            # Strategy 2 - Use math to create it
            $wildcard_dec = pow(2, (32-$netmask)) - 1;
            $netmask_dec = ~ $wildcard_dec;
    
            return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
        }
        } else {
        // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
        if (strpos($range, '*') !==false) { // a.b.*.* format
            // Just convert to A-B format by setting * to 0 for A and 255 for B
            $lower = str_replace('*', '0', $range);
            $upper = str_replace('*', '255', $range);
            $range = "$lower-$upper";
        }
    
        if (strpos($range, '-')!==false) { // A-B format
            list($lower, $upper) = explode('-', $range, 2);
            $lower_dec = (float)sprintf("%u",ip2long($lower));
            $upper_dec = (float)sprintf("%u",ip2long($upper));
            $ip_dec = (float)sprintf("%u",ip2long($ip));
            return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
        }
    
        if (WP_DEBUG) error_log('Range argument is not in 1.2.3.4/24 or 1.2.3.4/255.255.255.0 format');
        return false;
        }
    
    }

  /**
     * Check IP Has The Custom IP Range List
     *
     * @param $ip
     * @param array $range
     * @return bool
     * @throws \Exception
     */
    public static function CheckIPRange($range = array(), $ip = false)
    {

        // Get User IP
        $ip = ($ip === false ? IP::getIP() : $ip);

        // Check List
        foreach ($range as $list) {
            $contains_ip = self::ip_in_range($ip,$list);

            if ($contains_ip) {
                return true;
            }
        }

        return false;
    }
}