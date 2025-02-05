<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */

namespace STATS4WP\Ui;

use STATS4WP\Core\BaseController;
use STATS4WP\Api\SettingsApi;
use STATS4WP\Api\Callbacks\AdminCallbacks;
use STATS4WP\Core\DB;

/**
 * Class CSVExport
 */
class CSVExport extends BaseController
{

    public $callbacks;

    public $subpages = array();

    public $settings;

    public $separator;

    public function register()
    {
        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->setSubpages();

        $this->settings->add_sub_pages($this->subpages)->register();

        $this->separator = ';';
        if (isset($_GET['report']) ) {
            $csv = $this->generate_csv(sanitize_text_field(wp_unslash($_GET['report'])));

            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/octet-stream');
            if (isset($_GET['year']) ) {
                header('Content-Disposition: attachment; filename="Export_' . sanitize_text_field(wp_unslash($_GET['report'])) . '_' . sanitize_text_field(wp_unslash($_GET['year'])) . '.csv";');
            } else {
                header('Content-Disposition: attachment; filename="Export_' . sanitize_text_field(wp_unslash($_GET['report'])) . '.csv";');
            }
            header('Content-Transfer-Encoding: binary');

            printf('%s', esc_html($csv));
            exit;
        }
    }

    public function setSubpages()
    {
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
    public function generate_csv( $table )
    {
        global $wpdb;

        switch ( $table ) {
        case 'visitor':
            $field = 'last_counter';
            break;
        case 'pages':
            $field = 'date';
            break;
        }

        $wpdb->stats4wp_tmp = DB::table($table);
        $csv_output         = '';                                           // Assigning the variable to store all future CSV file's data

        $result = $wpdb->get_results("SHOW COLUMNS FROM $wpdb->stats4wp_tmp");   // Displays all COLUMN NAMES under 'field' column in records returned

        if (count($result) > 0 ) {
            foreach ( $result as $row ) {
                $csv_output = $csv_output . $row->field . $this->separator;
            }
            $csv_output = substr($csv_output, 0, -1);               // Removing the last separator, because thats how CSVs work
        }
        $csv_output .= "\n";

        if (isset($_GET['year']) ) {
            $wpdb->stats4wp_y = ' where YEAR(' . $field . ')=' . sanitize_text_field(wp_unslash($_GET['year']));
        } else {
            $wpdb->stats4wp_y = '';
        }

        $values = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->stats4wp_tmp %s", $wpdb->stats4wp_y));       // This here

        foreach ( $values as $rowr ) {
            $fields      = array_values((array) $rowr);                  // Getting rid of the keys and using numeric array to get values
            $csv_output .= implode($this->separator, $fields);      // Generating string with field separator
            $csv_output .= "\n";    // Yeah...
        }

        return $csv_output; // Back to constructor
    }
}
