<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */

if (! defined('ABSPATH') ) {
    exit;
}


use STATS4WP\Core\DB;
use STATS4WP\Api\AdminGraph;

$page_local = ( isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '' );
if ('stats4wp_plugin' === $page_local ) {
    $data = 'all';
} else {
    $data = '';
}

if (DB::exist_row('visitor') ) {
    $param = AdminGraph::getdate($data);
    if (! isset($wpdb->stats4wp_visitor) ) {
        $wpdb->stats4wp_visitor = DB::table('visitor');
    }
    ?>
    <div class="stats4wp-dashboard">
        <div class="stats4wp-rows">
            <div class="stats4wp-inline width46 ">
            <canvas  id="chartjs_lang" height="300vw" width="400vw"></canvas> 
                <?php
                $languages      = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT language, count(*) as nb FROM {$wpdb->stats4wp_visitor} 
                WHERE device NOT IN ('bot','')
                AND last_counter BETWEEN %s AND %s
                GROUP BY language
                ORDER by nb DESC",
                        $param['from'],
                        $param['to']
                    )
                );
                $language_total = array_sum(array_column($languages, 'nb'));
                $language_nb    = 0;
                $language_list  = '<table class="widefat table-stats stats4wp-report-table">
                <tbody>
                    <tr>
                        <td style="width: 1%;"></td>
                        <td>' . esc_html(__('Languages', 'stats4wp')) . '</td>
                        <td style="width: 20%;">' . esc_html(__('Users', 'stats4wp')) . '</td>
                        <td style="width: 20%;">' . esc_html(__('% Users', 'stats4wp')) . '</td>
                    </tr>';
                foreach ( $languages as $language ) {
                    if ($language_nb < 10 ) {
                        $lang[] = $language->language;
                        $nb[]   = ( null === $language->nb ) ? 0 : $language->nb;
                    }
                    $language_nb++;
                    $tr_class       = ( 0 === $language_nb % 2 ) ? 'stats4wp-bg' : '';
                    $percent        = round($language->nb * 100 / $language_total, 2);
                    $language_list .= '<tr class="' . esc_attr($tr_class) . '"><td>' . esc_html($language_nb) . '</td><td>' . esc_html(substr($language->language, 0, 50)) . '</td><td class="stats4wp-right">' . esc_html(number_format($language->nb, 0, ',', ' ')) . '</td><td class="stats4wp-left stats4wp-nowrap"><div class="stats4wp-percent" style="width:' . esc_attr($percent) . '%;"></div>' . esc_html($percent) . '%</td></tr>';
                }
                $language_list .= '</tbody>
                    </table>';
                $script_js      = '
                const dataLang= {
                    labels:' . wp_json_encode($lang) . ',
                    datasets: [{
                        label: "' . esc_html(__('Languages', 'stats4wp')) . '",
                        data:' . wp_json_encode($nb) . ',
                        backgroundColor: ["#36a2eb","#f67019","#f53794","#537bc4","#acc236","#166a8f","#00a950","#58595b","#8549ba","#4dc9f6"],
                    }]
                };
            
                const optionsLang = {
                    responsive: false,
                    plugins: {
                        title: {
                          display: true,
                          text: "' . esc_html(__('Language TOP 10', 'stats4wp')) . '"
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
            
                const configLang = {
                  type: "doughnut",
                  data: dataLang,
                  options: optionsLang,
                };
            
                // render init block
                const myChartLang = new Chart(
                  document.getElementById("chartjs_lang"),
                  configLang
                );
                
                ';
                wp_add_inline_script('chart-js', $script_js);
                unset($lang, $nb);
                ?>
            </div>
            <div class="stats4wp-inline width46">
                <div class="stats4wp-language">
                    <?php echo wp_kses_post($language_list); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
