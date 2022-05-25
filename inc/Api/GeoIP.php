<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.4
 */
namespace STATS4WP\Api;

use MaxMind\Db\Reader;


class GeoIP
{
    /**
     * Date of database GeoIpLitle User IP
     *
     * @var string
     */
    public static $geoip_date = '20220524';

    /**
     * Date of database GeoIpLitle User IP
     *
     * @var string
     */
    public static $geoip_file = STATS4WP_PATH .'db/GeoLite2-Country.mmdb';

    

    /**
     * Returns the current Country.
     *
     * @return string
     */
    public static function getCountry($ip)
    {
        if (IP::CheckIPRange(IP::$private_SubNets,$ip)) return 'local';
        $reader = new Reader( self::$geoip_file );
        $ipData = $reader->get($ip);
        if (WP_DEBUG) error_log(print_r($ipData,true));
        $reader->close();
        return (!$ipData['country']['iso_code'] ? 'none' : $ipData['country']['iso_code']);
    }
}