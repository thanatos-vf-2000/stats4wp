<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.7
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

?>
<div id ="stats4wp-nb-users-hits-widget" class="postbox " >
	<div class="postbox-header">
		<h2 class="hndle ui-sortable-handle"><?php esc_html_e( 'Number of Users and Hits', 'stats4wp' ); ?></h2>
	</div>
	<div class="inside">
		<canvas  id="chartjs_nb_users_hits" height="300vw" width="800vw"></canvas> 
	</div>
</div>
<?php
if ( DB::exist_row( 'visitor' ) ) {
	if ( ! isset( $wpdb->stats4wp_visitor ) ) {
		$wpdb->stats4wp_visitor = DB::table( 'visitor' );}
	$param = AdminGraph::getdate( '' );
	switch ( $param['group'] ) {
		case 1:
			$visitors   = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits 
                FROM $wpdb->stats4wp_visitor 
                where device!='bot' 
                AND last_counter BETWEEN %s AND %s 
                GROUP BY 1 ORDER BY 1 ASC",
					array(
						$param['from'],
						$param['to'],
					)
				)
			);
			$char_title = __( 'Number of users and Hits per days', 'stats4wp' );
			break;
		case 2:
			$visitors   = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT CONCAT(YEAR(last_counter),' - ',WEEK(last_counter)) as last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits 
                FROM $wpdb->stats4wp_visitor 
                where device!='bot' 
                AND last_counter BETWEEN %s AND %s 
                GROUP BY 1 ORDER BY 1 ASC",
					array(
						$param['from'],
						$param['to'],
					)
				)
			);
			$char_title = __( 'Number of users and Hits per weeks', 'stats4wp' );
			break;
		case 3:
			$visitors   = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT CONCAT(YEAR(last_counter),' - ',MONTH(last_counter)) as last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits 
                FROM $wpdb->stats4wp_visitor 
                where device!='bot' 
                AND last_counter BETWEEN %s AND %s 
                GROUP BY 1 ORDER BY 1 ASC",
					array(
						$param['from'],
						$param['to'],
					)
				)
			);
			$char_title = __( 'Number of users and Hits per months', 'stats4wp' );
			break;
		case 4:
			$visitors   = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT CONCAT(YEAR(last_counter),' - ',QUARTER(last_counter)) as last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits
                FROM $wpdb->stats4wp_visitor 
                where device!='bot' 
                AND last_counter BETWEEN %s AND %s 
                GROUP BY 1 ORDER BY 1 ASC",
					array(
						$param['from'],
						$param['to'],
					)
				)
			);
			$char_title = __( 'Number of users and Hits per quarter', 'stats4wp' );
			break;
		case 5:
			$visitors   = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT YEAR(last_counter) as last_counter, COUNT(*) as user, ROUND(AVG(hits),2) as hits
                FROM $wpdb->stats4wp_visitor 
                where device!='bot' 
                AND last_counter BETWEEN %s AND %s 
                GROUP BY 1 ORDER BY 1 ASC",
					array(
						$param['from'],
						$param['to'],
					)
				)
			);
			$char_title = __( 'Number of users and Hits per Years', 'stats4wp' );
			break;
	}
	foreach ( $visitors as $visitor ) {
		$day[]   = $visitor->last_counter;
		$users[] = $visitor->user;
		$hits[]  = $visitor->hits;
	}

	$script_js = ' 
    const dataNbUsersHits = {
        labels:' . wp_json_encode( $day ) . ',
        datasets: [{
            label: "' . esc_html( __( 'Users', 'stats4wp' ) ) . '",
            borderColor: "#FF6384",
            fill: false,
            pointRadius: [0],
            pointHitRadius: [0],
            cubicInterpolationMode: "monotone",
            tension: 0.4,
            backgroundColor: [
               "#FF6384"
            ],
            data:' . wp_json_encode( $users ) . ',
        },
        {
            label: "' . esc_html( __( 'Hits', 'stats4wp' ) ) . '",
            borderColor: "#36A2EB",
            fill: false,
            pointRadius: [0],
            pointHitRadius: [0],
            cubicInterpolationMode: "monotone",
            tension: 0.4,
            backgroundColor: [
               "#36A2EB"
            ],
            data:' . wp_json_encode( $hits ) . ',
        }]
    };

    const optionsNbUsersHits = {
        responsive: true,
        plugins: {
            title: {
              display: false,
              text: "' . esc_html( $char_title ) . '"
            },
          },
        legend: {
            display: true,
            position: "bottom",
            labels: {
                fontColor: "#05419ad6",
                fontFamily: "Circular Std Book",
                fontSize: 14,
            }
        },
    };

    const configNbUsersHits = {
    type: "line",
    data: dataNbUsersHits,
    options: optionsNbUsersHits,
    };

    // render init block
    const myChartNbUsersHits = new Chart(
    document.getElementById("chartjs_nb_users_hits"),
    configNbUsersHits
    );
    
    ;';
	wp_add_inline_script( 'chart-js', $script_js );
	unset( $day, $users, $hits, $visitors );
}
