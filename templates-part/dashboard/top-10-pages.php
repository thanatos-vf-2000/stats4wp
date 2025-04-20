<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;
use STATS4WP\Api\TimeZone;

?>
<div id="stats4wp-pages-widget" class="postbox ">
	<div class="postbox-header"><h2 class="hndle ui-sortable-handle"><?php esc_html_e( 'Top 10 Pages today', 'stats4wp' ); ?></h2>
	</div>
	<div class="inside">
		<table width="100%" class="widefat table-stats stats4wp-report-table stats4wp-table-fixed">
			<tbody>
				<tr>
					<td width="10%"><?php esc_html_e( 'ID', 'stats4wp' ); ?></td>
					<td width="40%"<?php esc_html_e( 'Title', 'stats4wp' ); ?>></td>
					<td width="40%"><?php esc_html_e( 'Link', 'stats4wp' ); ?></td>
					<td width="10%"><?php esc_html_e( 'Visits', 'stats4wp' ); ?></td>
				</tr>
				<?php
				if ( ! isset( $wpdb->stats4wp_pages ) ) {
					$wpdb->stats4wp_pages = DB::table( 'pages' );
				}
				$local_date = TimeZone::get_current_date( 'Y-m-d' );
				$top_pages  = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT *
                    FROM $wpdb->stats4wp_pages
                    WHERE date=%s
                    AND type='page' 
                    ORDER BY count  DESC LIMIT 10",
						$local_date
					)
				);
				$i          = 1;
				foreach ( $top_pages as $top_page ) {
					$title_local = get_the_title( $top_page->id );
					$link_local  = $top_page->uri;
					$nb          = $top_page->count;
					echo '<tr>
                    <td style="text-align: left;">' . esc_html( $i ) . '</td>
                    <td style="text-align: left;"><span title="' . esc_attr( $title_local ) . '" class="stats4wp-cursor-default stats4wp-text-wrap">' . esc_html( $title_local ) . '</span></td>
                    <td style="text-align: left;"><a href="' . esc_url( $link_local ) . '" title="Page d’accueil" target="_blank">' . esc_html( $link_local ) . '</a></td>
                    <td style="text-align: left" class="stats4wp-text-danger">' . esc_html( $nb ) . '</td>
                </tr>';
					++$i;
				}
				unset( $top_page, $i, $title_local, $link_local, $nb );
				?>
			</tbody>
		</table>
	</div>
</div>
