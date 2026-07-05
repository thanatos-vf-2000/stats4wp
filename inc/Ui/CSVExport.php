<?php
/**
 * @package STATS4WPPlugin
 * @version 1.5.0
 */

namespace STATS4WP\Ui;

use STATS4WP\Core\BaseController;
use STATS4WP\Api\SettingsApi;
use STATS4WP\Api\Callbacks\AdminCallbacks;
use STATS4WP\Core\DB;

/**
 * Class CSVExport
 */
class CSVExport extends BaseController {



	public $callbacks;

	public $subpages = array();

	public $settings;

	public $separator;

	public function register() {
		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->setSubpages();

		$this->settings->add_sub_pages( $this->subpages )->register();

		$this->separator = ';';
		if ( isset( $_GET['report'] ) ) {
			$csv = $this->generate_csv( sanitize_text_field( wp_unslash( $_GET['report'] ) ) );

			header( 'Pragma: public' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Cache-Control: private', false );
			header( 'Content-Type: application/octet-stream' );
			if ( isset( $_GET['year'] ) ) {
				header( 'Content-Disposition: attachment; filename="Export_' . sanitize_text_field( wp_unslash( $_GET['report'] ) ) . '_' . sanitize_text_field( wp_unslash( $_GET['year'] ) ) . '.csv";' );
			} else {
				header( 'Content-Disposition: attachment; filename="Export_' . sanitize_text_field( wp_unslash( $_GET['report'] ) ) . '.csv";' );
			}
			header( 'Content-Transfer-Encoding: binary' );

			// Note: this is a raw CSV file download, not HTML output, so it must
			// NOT be passed through esc_html() (which corrupts accented characters,
			// quotes and ampersands and breaks the file for spreadsheet software).
			echo "\xEF\xBB\xBF"; // UTF-8 BOM so Excel opens accented characters correctly.
			echo $csv; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			exit;
		}
	}

	public function setSubpages() {
		$this->subpages = array(
			array(
				'parent_slug' => STATS4WP_NAME . '_plugin',
				'page_title'  => 'CSV Export',
				'menu_title'  => 'CSV Export',
				'capability'  => 'manage_options',
				'menu_slug'   => STATS4WP_NAME . '_cvsexport',
				'callback'    => array( $this->callbacks, 'adminCSVExport' ),
			),
		);
	}

	/**
	 * Converting data to CSV
	 */
	public function generate_csv( $table ) {
		global $wpdb;

		// Whitelist of exportable tables: also protects against an arbitrary
		// table name reaching DB::table() / the SQL below.
		$allowed_fields = array(
			'visitor' => 'last_counter',
			'pages'   => 'date',
		);

		if ( ! array_key_exists( $table, $allowed_fields ) ) {
			return '';
		}
		$field = $allowed_fields[ $table ];

		$wpdb->stats4wp_tmp = DB::table( $table );
		$csv_output         = '';                                           // Assigning the variable to store all future CSV file's data

		$result = $wpdb->get_results( "SHOW COLUMNS FROM $wpdb->stats4wp_tmp" );   // Displays all COLUMN NAMES under 'field' column in records returned

		if ( count( $result ) > 0 ) {
			$headers = array();
			foreach ( $result as $row ) {
				$headers[] = $this->escape_csv_field( $row->field );
			}
			$csv_output .= implode( $this->separator, $headers );
		}
		$csv_output .= "\n";

		/**
		 * Filter by year: the year is cast to an integer before being used in
		 * the query, so it can never be used to inject arbitrary SQL.
		 */
		if ( isset( $_GET['year'] ) ) {
			$year   = absint( wp_unslash( $_GET['year'] ) );
			$values = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM $wpdb->stats4wp_tmp WHERE YEAR($field) = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$year
				)
			);
		} else {
			$values = $wpdb->get_results( "SELECT * FROM $wpdb->stats4wp_tmp" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		foreach ( $values as $rowr ) {
			$fields = array_values( (array) $rowr );                  // Getting rid of the keys and using numeric array to get values
			$fields = array_map( array( $this, 'escape_csv_field' ), $fields );
			$csv_output .= implode( $this->separator, $fields );      // Generating string with field separator
			$csv_output .= "\n";
		}

		return $csv_output; // Back to constructor
	}

	/**
	 * Escape a single value for safe inclusion in the CSV file (RFC 4180).
	 *
	 * Wraps the value in double quotes and doubles any internal quote as soon
	 * as it contains the separator, a quote, or a line break, so that field
	 * values coming from user-controlled data (referrer, user agent, URI...)
	 * can never break out of their column or corrupt the file.
	 *
	 * @param  mixed $value Raw value coming from the database.
	 * @return string
	 */
	private function escape_csv_field( $value ) {
		$value = (string) $value;

		if ( '' === $value ) {
			return $value;
		}

		if ( false !== strpbrk( $value, $this->separator . "\"\n\r" ) ) {
			$value = '"' . str_replace( '"', '""', $value ) . '"';
		}

		return $value;
	}
}
