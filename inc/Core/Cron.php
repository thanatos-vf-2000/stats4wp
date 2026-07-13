<?php
/**
 * @package STATS4WPPlugin
 * @version 1.6.0
 */
namespace STATS4WP\Core;

class Cron {




	/**
	 * Hook name of the daily cleanup scheduled event.
	 *
	 * @var string
	 */
	const EVENT_CLEANUP = 'stats4wp_daily_cleanup';

	/**
	 * Hook name of the weekly email report scheduled event.
	 *
	 * @var string
	 */
	const EVENT_REPORT = 'stats4wp_weekly_report';

	/**
	 * Custom cron recurrence used by the weekly report.
	 *
	 * @var string
	 */
	const SCHEDULE_WEEKLY = 'stats4wp_weekly';

	public function register() {
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) ); // phpcs:ignore WordPress.WP.CronInterval.CronSchedulesInterval

		add_action( self::EVENT_CLEANUP, array( $this, 'purge_old_data' ) );
		if ( ! wp_next_scheduled( self::EVENT_CLEANUP ) ) {
			wp_schedule_event( time(), 'daily', self::EVENT_CLEANUP );
		}

		add_action( self::EVENT_REPORT, array( $this, 'send_weekly_report' ) );
		if ( ! wp_next_scheduled( self::EVENT_REPORT ) ) {
			// Send it Monday mornings: schedule the first run at the next
			// Monday 08:00 (site time), then repeat every 7 days.
			$first_run = strtotime( 'next monday 08:00', current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
			wp_schedule_event( $first_run, self::SCHEDULE_WEEKLY, self::EVENT_REPORT );
		}
	}

	/**
	 * Register the "stats4wp_weekly" recurrence, since WordPress only ships
	 * hourly/twicedaily/daily by default.
	 *
	 * @param  array $schedules
	 * @return array
	 */
	public function add_cron_schedules( $schedules ) {
		$schedules[ self::SCHEDULE_WEEKLY ] = array(
			'interval' => WEEK_IN_SECONDS,
			'display'  => stats4wp_t( 'Once Weekly (stats4wp)', 'stats4wp' ),
		);

		return $schedules;
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
	 * Build the data set used by the weekly email report: totals and top 5
	 * lists for the last 7 full days (yesterday included), same conventions
	 * (bot/local exclusion) as the dashboard widgets.
	 *
	 * @return array
	 */
	public static function get_weekly_report_data() {
		global $wpdb;

		$to   = gmdate( 'Y-m-d', strtotime( '-1 day' ) );
		$from = gmdate( 'Y-m-d', strtotime( '-7 days' ) );

		$wpdb->stats4wp_visitor = DB::table( 'visitor' );
		$wpdb->stats4wp_pages   = DB::table( 'pages' );

		$summary = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT COUNT(ID) as visitors, COALESCE(SUM(hits),0) as visits
				FROM $wpdb->stats4wp_visitor
				WHERE device != 'bot'
					AND location != 'local'
					AND last_counter BETWEEN %s AND %s",
				$from,
				$to
			)
		);

		$top_pages = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT uri, SUM(count) as hits
				FROM $wpdb->stats4wp_pages
				WHERE type != 'unknown'
					AND date BETWEEN %s AND %s
				GROUP BY uri
				ORDER BY hits DESC
				LIMIT 5",
				$from,
				$to
			)
		);

		$top_countries = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT location, COUNT(ID) as nb
				FROM $wpdb->stats4wp_visitor
				WHERE device != 'bot'
					AND location != 'local'
					AND location != 'none'
					AND last_counter BETWEEN %s AND %s
				GROUP BY location
				ORDER BY nb DESC
				LIMIT 5",
				$from,
				$to
			)
		);

		return array(
			'from'          => $from,
			'to'            => $to,
			'visitors'      => (int) ( isset( $summary->visitors ) ? $summary->visitors : 0 ),
			'visits'        => (int) ( isset( $summary->visits ) ? $summary->visits : 0 ),
			'top_pages'     => $top_pages,
			'top_countries' => $top_countries,
		);
	}

	/**
	 * Send the weekly stats report by email.
	 *
	 * @param  bool $force Bypass the "weekly_report_enabled" option check.
	 *                     Used by the "send test email now" button.
	 * @return bool
	 */
	public static function send_weekly_report( $force = false ) {
		if ( ! $force && true !== Options::get_option( 'weekly_report_enabled' ) ) {
			return false;
		}

		$to = Options::get_option( 'weekly_report_email' );
		if ( empty( $to ) ) {
			$to = get_option( 'admin_email' );
		}
		if ( empty( $to ) || ! is_email( $to ) ) {
			return false;
		}

		$data              = self::get_weekly_report_data();
		$data['site_name'] = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$data['site_url']  = home_url( '/' );
		$data['dashboard'] = admin_url( 'admin.php?page=' . STATS4WP_NAME . '_plugin' );

		$template = STATS4WP_PATH . 'templates-part/email/weekly-report.php';
		if ( ! file_exists( $template ) ) {
			return false;
		}

		ob_start();
		include $template;
		$body = ob_get_clean();

		$subject = sprintf(
			/* translators: 1: site name, 2: report start date, 3: report end date */
			stats4wp_t( '[%1$s] Weekly stats report (%2$s - %3$s)', 'stats4wp' ),
			$data['site_name'],
			$data['from'],
			$data['to']
		);

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$sent = wp_mail( $to, $subject, $body, $headers );

		do_action( 'stats4wp_after_weekly_report_sent', $sent, $to, $data );

		return $sent;
	}

	/**
	 * Unschedule every cron event. Called on plugin deactivation.
	 */
	public static function unschedule() {
		foreach ( array( self::EVENT_CLEANUP, self::EVENT_REPORT ) as $event ) {
			$timestamp = wp_next_scheduled( $event );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, $event );
			}
		}
	}
}
