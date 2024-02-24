<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.9
 *
 * Desciption: CVS Export date
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
use STATS4WP\Core\DB;

self::get_template( array( 'header' ) );

?>
<div class="wp-clearfix"></div>
<div class="metabox-holder" id="overview-widgets">
	<table class="wp-list-table widefat fixed striped table-view-list posts">
		<thead>
			<tr>
				<th></th>
				<th colspan="2" ><?php esc_html_e( 'Number of rows', 'stats4wp' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( array( 'visitor', 'pages' ) as $t ) {
				$wpdb->stats4wp_tmp = DB::table( $t );
				$num                = $wpdb->get_row(
					"SELECT count(*) as nb 
                    FROM $wpdb->stats4wp_tmp"
				);
				switch ( $t ) {
					case 'visitor':
						$field = 'last_counter';
						break;
					case 'pages':
						$field = 'date';
						break;
				}
				$years = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT DISTINCT(YEAR(%s)) as y FROM $wpdb->stats4wp_tmp 
                    ORDER BY 1 ASC",
						$field
					)
				);
				echo '<tr>
                        <td>' . esc_html( $t ) . '</td>
                        <td>' . esc_html( $num->nb ) . '</td>
                        <td><a href="?page=stats4wp_cvsexport&report=' . esc_attr( $t ) . '" target="_blank"> ' . esc_html__( 'Export All', 'stats4wp' ) . '</a>';
				foreach ( $years as $yeardata ) {
					echo ' - <a href="?page=stats4wp_cvsexport&report=' . esc_attr( $t ) . '&year=' . esc_attr( $yeardata->y ) . '" target="_blank"> ' . esc_attr( $yeardata->y ) . '</a>';
				}
				echo '</td></tr>';
			}
			?>
		</tbody>
	</table>
</div>
<?php
self::get_template( array( 'footer' ) );
?>
