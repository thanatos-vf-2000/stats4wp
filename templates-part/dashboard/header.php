<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.15
 *
 * Desciption: Admin Page Pages
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Api\AdminGraph;
use STATS4WP\Core\DB;

if ( ! isset( $wpdb->stats4wp_visitor ) ) {
	$wpdb->stats4wp_visitor = DB::table( 'visitor' );
}
$visitors_month = $wpdb->get_row(
	"SELECT count(*) as visitors, COALESCE(SUM(hits),0) as pages
    FROM $wpdb->stats4wp_visitor 
    where device!='bot' and location != 'local'
    and last_counter BETWEEN date_add(date_add(LAST_DAY(CURRENT_DATE()),interval 1 DAY),interval -1 MONTH) AND CURRENT_DATE()"
);

$visitors_month_past = $wpdb->get_row(
	"SELECT count(*) as visitors, COALESCE(SUM(hits),0) as pages
    FROM $wpdb->stats4wp_visitor 
    where device!='bot' and location != 'local'
    and last_counter BETWEEN date_add(date_add(LAST_DAY((CURRENT_DATE() -INTERVAL 1 MONTH)),interval 1 DAY),interval -1 MONTH) AND (CURRENT_DATE() -INTERVAL 1 MONTH)"
);

$last_hour = $wpdb->get_row(
	"SELECT count(*) as visitors, COALESCE(SUM(hits),0) as pages
    FROM $wpdb->stats4wp_visitor 
    where device!='bot' and location != 'local'
    AND last_counter = CURRENT_DATE()
    AND hour > (CURRENT_TIME() - INTERVAL 1 hour)"
);

$month_visitors['d'] = ( $visitors_month_past->visitors > 0 ) ? $visitors_month->visitors - $visitors_month_past->visitors : 0;
$month_visitors['p'] = ( $visitors_month_past->visitors > 0 ) ? round( $visitors_month->visitors * 100 / $visitors_month_past->visitors - 100, 2 ) : 0;
if ( 0 === $month_visitors['p'] ) {
	$class_visitors       = 'neutral';
	$month_visitors['p'] .= '+';
	$month_visitors['m']  = __( 'less than previous period', 'stats4wp' );
} elseif ( $month_visitors['p'] > 0 ) {
	$class_visitors       = 'up';
	$month_visitors['p'] .= '+';
	$month_visitors['m']  = __( 'more than previous period', 'stats4wp' );
} else {
	$class_visitors      = 'down';
	$month_visitors['m'] = __( 'less than previous period', 'stats4wp' );
}

$month_pages['d'] = ( $visitors_month_past->pages > 0 ) ? $visitors_month->pages - $visitors_month_past->pages : 0;
$month_pages['p'] = ( $visitors_month_past->pages > 0 ) ? round( $visitors_month->pages * 100 / $visitors_month_past->pages - 100, 2 ) : 0;
if ( 0 === $month_pages['p'] ) {
	$class_pages       = 'neutral';
	$month_pages['p'] .= '+';
	$month_pages['m']  = __( 'less than previous period', 'stats4wp' );
} elseif ( $month_pages['p'] > 0 ) {
	$class_pages       = 'up';
	$month_pages['p'] .= '+';
	$month_pages['m']  = __( 'more than previous period', 'stats4wp' );
} else {
	$class_pages      = 'down';
	$month_pages['m'] = __( 'less than previous period', 'stats4wp' );
}
?>

<div class="rows stats4wp-dashboard-header">
	<p><?php echo esc_html( __( 'Statistics', 'stats4wp' ) . ' ' . gmdate( 'Y' ) ); ?></p>
	<div class="stats4wp-db-container">
		<div class="stats4wp-db-box fade">
			<div class="stats4wp-db-label"><?php esc_html_e( 'Total visitors', 'stats4wp' ); ?></div>
			<div class="stats4wp-db-amount"><?php echo esc_html( number_format( $visitors_month->visitors, 0, ',', ' ' ) ); ?> <span class="<?php echo esc_attr( $class_visitors ); ?>"><?php echo esc_html( $month_visitors['p'] ); ?>%</span></div>
			<div class="stats4wp-db-compare"><span><?php echo esc_html( $month_visitors['d'] . ' ' . $month_visitors['m'] ); ?></span></div>
		</div>
		<div class="stats4wp-db-box fade">
			<div class="stats4wp-db-label"><?php esc_html_e( 'Total pageviews', 'stats4wp' ); ?></div>
			<div class="stats4wp-db-amount"><?php echo esc_html( number_format( $visitors_month->pages, 0, ',', ' ' ) ); ?> <span class="<?php echo esc_attr( $class_pages ); ?>"><?php echo esc_html( $month_pages['p'] ); ?>%</span></div>
			<div class="stats4wp-db-compare"><span><?php echo esc_html( $month_pages['d'] . ' ' . $month_pages['m'] ); ?></span></div>
		</div>
		<div class="stats4wp-db-box fade">
			<div class="stats4wp-db-label"><?php esc_html_e( 'Realtime visitors', 'stats4wp' ); ?></div>
			<div class="stats4wp-db-amount"><?php echo esc_html( number_format( $last_hour->visitors, 0, ',', ' ' ) ); ?></div>
			<div class="stats4wp-db-compare"><span><span><?php esc_html_e( 'visitors in the last hour', 'stats4wp' ); ?></span></span></div>
		</div>
		<div class="stats4wp-db-box fade">
			<div class="stats4wp-db-label"><?php esc_html_e( 'Realtime pageviews', 'stats4wp' ); ?></div>
			<div class="stats4wp-db-amount"><?php echo esc_html( number_format( $last_hour->pages, 0, ',', ' ' ) ); ?></div>
			<div class="stats4wp-db-compare"><span><span><?php esc_html_e( 'pageviews in the last hour', 'stats4wp' ); ?></span></span></div>
		</div>
	</div>
	<?php
	unset( $visitors_month, $visitors_month_past, $month_visitors, $class_visitors, $month_pages, $class_pages, $last_hour );

	AdminGraph::select_date_dashboard();
	if ( DB::exist_row( 'visitor' ) ) {
		$data = AdminGraph::getdate( 'local' );
		echo '<p class="stats4wp-min">' . esc_html( $data['from'] ) . ' - ' . esc_html( $data['to'] ) . '</p>';
	} else {
		echo '<p class="stats4wp-min">' . esc_html( __( 'No data found to visitor.', 'stats4wp' ) ) . '</p>';
	}
	?>
</div>
