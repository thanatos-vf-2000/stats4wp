<?php
/**
 * @package STATS4WPPlugin
 * @version 1.6.0
 *
 * Description: Handles the admin-post actions used by the "API & Reports"
 * settings tab (regenerate the REST API key, send a test weekly email).
 */
namespace STATS4WP\Ui;

use STATS4WP\Core\BaseController;
use STATS4WP\Core\Cron;
use STATS4WP\Api\RestApi;

class ReportsSettings extends BaseController {




	const ACTION_REGENERATE_KEY = 'stats4wp_regenerate_api_key';

	const ACTION_SEND_TEST_REPORT = 'stats4wp_send_test_report';

	public function register() {
		add_action( 'admin_post_' . self::ACTION_REGENERATE_KEY, array( $this, 'handle_regenerate_api_key' ) );
		add_action( 'admin_post_' . self::ACTION_SEND_TEST_REPORT, array( $this, 'handle_send_test_report' ) );
	}

	/**
	 * Regenerate the REST API key and redirect back to the settings page.
	 */
	public function handle_regenerate_api_key() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'stats4wp' ) );
		}

		check_admin_referer( self::ACTION_REGENERATE_KEY );

		RestApi::regenerate_api_key();

		$this->redirect_with_notice( 'key-regenerated' );
	}

	/**
	 * Send a one-off weekly report email right now (bypassing the
	 * "weekly_report_enabled" toggle), and redirect back with a notice.
	 */
	public function handle_send_test_report() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'stats4wp' ) );
		}

		check_admin_referer( self::ACTION_SEND_TEST_REPORT );

		$sent = Cron::send_weekly_report( true );

		$this->redirect_with_notice( $sent ? 'report-sent' : 'report-failed' );
	}

	/**
	 * @param  string $notice
	 */
	private function redirect_with_notice( $notice ) {
		$url = add_query_arg(
			array(
				'page'             => STATS4WP_NAME . '_settings',
				'stats4wp-notice'  => $notice,
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $url . '#tab-5' );
		exit;
	}
}
