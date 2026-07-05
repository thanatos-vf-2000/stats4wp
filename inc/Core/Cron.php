<?php
/**
 * @package STATS4WPPlugin
 * @version 1.5.0
 */
namespace STATS4WP\Core;

class Cron {




	/**
	 * Hook name of the scheduled event.
	 *
	 * @var string
	 */
	const EVENT = 'stats4wp_daily_cleanup';

	public function register() {
		add_action( self::EVENT, array( $this, 'purge_old_data' ) );

		if ( ! wp_next_scheduled( self::EVENT ) ) {
			wp_schedule_event( time(), 'daily', self::EVENT );
		}
	}

	/**
	 * Delete visitor/page rows older than the configured retention period.
	 *
	 * Runs once a day. Does nothing when "data_retention_days" is 0
	 * (the default), so existing installs keep their current behaviour
	 * unless the site owner explicitly opts in.
	 */
	public function purge_old_data() {
		$retention_days = (int) Options::get_option( 'data_retention_days' );

		if ( $retention_days <= 0 ) {
			return;
		}

		global $wpdb;

		$before = gmdate( 'Y-m-d', strtotime( '-' . $retention_days . ' days' ) );

		$wpdb->stats4wp_visitor = DB::table( 'visitor' );
		$wpdb->stats4wp_pages   = DB::table( 'pages' );

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->stats4wp_visitor WHERE `last_counter` < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$before
			)
		);

		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->stats4wp_pages WHERE `date` < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$before
			)
		);

		do_action( 'stats4wp_after_data_purge', $before, $retention_days );
	}

	/**
	 * Unschedule the cron event. Called on plugin deactivation.
	 */
	public static function unschedule() {
		$timestamp = wp_next_scheduled( self::EVENT );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::EVENT );
		}
	}
}
