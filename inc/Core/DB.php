<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.3.7
 */
namespace STATS4WP\Core;

class DB
{

    /**
     * List Of wp-statistics Mysql Table
     *
     * @var array
     */
    public static $db_table = array(
        'visitor',
        'pages',
        'useronline'
    );

    /**
     * List of array exist
     * 
     * @var array
     */
    public static $db_table_exist = array();

    /**
     * Table name Structure in Database
     *
     * @var string
     */
    public static $tbl_name = '[prefix]stats4wp_[name]';

    /**
     * Get WordPress Table Collate
     *
     * @return mixed
     */
    public static function charset_collate()
    {
        global $wpdb;
        return $wpdb->get_charset_collate();
    }

    /**
     * Get WordPress Table Prefix
     */
    public static function prefix()
    {
        global $wpdb;
        return $wpdb->prefix;
    }

    /**
     * Get Table name
     *
     * @param $tbl
     * @return mixed
     */
    public static function getTableName($tbl)
    {
        return str_ireplace(array("[prefix]", "[name]"), array(self::prefix(), $tbl), self::$tbl_name);
    }

    /**
     * Check Exist Table in Database
     *
     * @param $tbl_name
     * @return bool
     */
    public static function ExistTable($tbl_name)
    {
        global $wpdb;
        return ($wpdb->get_var("SHOW TABLES LIKE '$tbl_name'") == $tbl_name);
    }
    
    /**
     * Table List 
     *
     * @param string $export
     * @param array $except
     * @return array|null|string
     */
    public static function table($export = 'all', $except = array())
    {

        # Create Empty Object
        $list = array();

        # Convert except String to array
        if (is_string($except)) {
            $except = array($except);
        }

        # Check Except List
        $mysql_list_table = array_diff(self::$db_table, $except);

        # Get List
        foreach ($mysql_list_table as $tbl) {

            # WP-Statistics table name
            $table_name = self::getTableName($tbl);

            if ($export == "all") {
                if (self::ExistTable($table_name)) {
                    $list[$tbl] = $table_name;
                }
            } else {
                $list[$tbl] = $table_name;
            }
        }

        # Export Data
        return ($export == 'all' ? $list : (array_key_exists($export, $list) ? $list[$export] : null));
    }

    /**
     * Test if exist Row in table 
     *
     * @param string $export
     * @param array $except
     * @return array|null|string
     */
    public static function ExistRow($tbl)
    {
        global $wpdb;
        if (! in_array($tbl, self::$db_table)) return false;
        if (in_array($tbl,self::$db_table_exist,true)) {
            $nb = self::$db_table_exist[$tbl];
        } else {
            $nbrows = $wpdb->get_row("SELECT count(*) as nb FROM " . DB::table($tbl));
            $nb = $nbrows->nb;
            self::$db_table_exist+= ["$tbl" => $nb];
        }
        
        if ($nb > 0 ) {
            return true;
        } else {
            return false;
        }
    }
}