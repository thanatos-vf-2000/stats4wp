<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */
namespace STATS4WP\Core;

class Install {



	/**
	 * Install
	 *
	 * @param $network_wide
	 */
	public static function install( $network_wide ) {
		// Create MySQL Table
		self::create_table( $network_wide );
	}

	/**
	 * Adding new MYSQL Table in Activation Plugin
	 *
	 * @param $network_wide
	 */
	public static function create_table( $network_wide ) {
		global $wpdb;

		if ( is_multisite() && $network_wide ) {
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::table_sql();
				restore_current_blog();
			}
		} else {
			self::table_sql();
		}
	}

	/**
	 * Create Database Table
	 */
	public static function table_sql() {
		// Load dbDelta WordPress
		self::load_db_delta();

		// Charset Collate
		$collate = DB::charset_collate();

		// Visitor Table
		$create_visitor_table = ( '
					CREATE TABLE ' . DB::table( 'visitor' ) . " (
						ID bigint(20) NOT NULL AUTO_INCREMENT,
						last_counter date NOT NULL,
                        hour time NOT NULL,
						referred text NOT NULL,
						agent varchar(50) NOT NULL,
                        agent_v varchar(50),
						platform varchar(50),
						platform_v varchar(50),
                        engine varchar(50),
						engine_v varchar(50),
                        device varchar(50),
                        manufacturer varchar(50),
                        model varchar(50),
						ip varchar(60) NOT NULL,
						location varchar(10),
						user_id BIGINT(40) NOT NULL,
                        language varchar(2),
                        UAString varchar(190),
                        hits int(11),
						PRIMARY KEY  (ID),
						UNIQUE KEY date_ip_agent (last_counter,ip,agent(50),platform(50)),
                        KEY `SELSTAT` (`device`, `last_counter`)
					) {$collate}" );
		dbDelta( $create_visitor_table );

		// Pages Table
		$create_pages_table = ( '
					CREATE TABLE ' . DB::table( 'pages' ) . " (
					    page_id BIGINT(20) NOT NULL AUTO_INCREMENT,
						uri varchar(190) NOT NULL,
						type varchar(180) NOT NULL,
						date date NOT NULL,
						count int(11) NOT NULL,
						id int(11) NOT NULL,
                        KEY `UPDSTATS` (`date`,`type`,`id`),
                        KEY `SELDATE` (`date`),
						PRIMARY KEY (`page_id`)
					) {$collate}" );
		dbDelta( $create_pages_table );

		// Users Online Table
		$create_user_online_table = ( '
					CREATE TABLE ' . DB::table( 'useronline' ) . " (
						ID bigint(20) NOT NULL AUTO_INCREMENT,
	  					ip varchar(60) NOT NULL,
						created int(11),
						timestamp int(10) NOT NULL,
						date datetime NOT NULL,
						referred text CHARACTER SET utf8 NOT NULL,
						agent varchar(255) NOT NULL,
						platform varchar(255),
						version varchar(255),
						location varchar(10),
						`user_id` BIGINT(48) NOT NULL,
						`page_id` BIGINT(48) NOT NULL,
						`type` VARCHAR(100) NOT NULL,
						PRIMARY KEY  (ID)
					) {$collate}" );
		dbDelta( $create_user_online_table );
	}

	/**
	 * Load WordPress dbDelta Function
	 */
	public static function load_db_delta() {
		if ( ! function_exists( 'dbDelta' ) ) {
			include ABSPATH . 'wp-admin/includes/upgrade.php';
		}
	}
}
