<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.9
 *
 * Desciption: Settings tables
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Core\DB;
use STATS4WP\Core\Args;

global $wpdb;

$arg[0] = Args::get_arg_value(
	'submit-delete-tables',
	false,
	function ( $val ) {
		return $val;
	}
);
$arg[1] = Args::get_arg_value(
	'delete-day',
	false,
	function ( $val ) {
		return $val;
	}
);
if ( ! isset( $wpdb->stats4wp_pages ) ) {
	$wpdb->stats4wp_pages = DB::table( 'pages' );}
if ( ! isset( $wpdb->stats4wp_visitor ) ) {
	$wpdb->stats4wp_visitor = DB::table( 'visitor' );}
if ( esc_html__( 'Purge tables', 'stats4wp' ) === $arg[0] ) {
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->stats4wp_visitor} WHERE last_counter<=%s", $arg[1] ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->stats4wp_pages} WHERE date<=%s", $arg[1] ) );
	echo '<div class="stats4wp-rows notice notice-success">' . esc_html( $arg[1] ) . ' - ' . esc_html__( 'Purged tables with succes.', 'stats4wp' ) . '</div>';
}

$nb_visitors  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->stats4wp_visitor}" );
$min_visitors = $wpdb->get_var( "SELECT MIN(last_counter) FROM {$wpdb->stats4wp_visitor}" );
$nb_pages     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->stats4wp_pages}" );
$min_pages    = $wpdb->get_var( "SELECT MIN(date) FROM {$wpdb->stats4wp_pages}" );

?>
<div class="stats4wp-dashboard">
	<div>
<?php
echo '<table class="widefat striped health-check-table">
    <thead>
        <tr>
            <th>' . esc_html__( 'Tables', 'stats4wp' ) . '</th>
            <th>' . esc_html__( 'Number of rows', 'stats4wp' ) . '</th>
            <th>' . esc_html__( 'Min day', 'stats4wp' ) . '</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>' . esc_html__( 'Visitor', 'stats4wp' ) . '</td>
            <td>' . esc_html( $nb_visitors ) . '</td>
            <td>' . esc_html( $min_visitors ) . '</td>
        </tr>
        <tr>
            <td>' . esc_html__( 'Pages', 'stats4wp' ) . '</td>
            <td>' . esc_html( $nb_pages ) . '</td>
            <td>' . esc_html( $min_pages ) . '</td>
        </tr>
    </tbody>
    </table>';

$delele_day = gmdate( 'Y-m-d', strtotime( '-1 years' ) );
if ( $delele_day < $min_visitors ) {
	$delele_day = $min_visitors;
}
echo '<form method="GET" action="' . esc_html( admin_url( 'admin.php' ) ) . '">
        <input type="hidden" name="page" value="stats4wp_settings"/>
        <label>' . esc_html_e( 'Before: ', 'stats4wp' ) . '
            <input type="date" name="delete-day" value="' . esc_html( $delele_day ) . '"/>
        </label>';
	submit_button( esc_html__( 'Purge tables', 'stats4wp' ), 'primary', 'submit-delete-tables', false );
	echo '</form>';
?>
	</div>
</div>
