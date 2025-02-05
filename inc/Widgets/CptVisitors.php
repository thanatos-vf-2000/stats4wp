<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */
namespace STATS4WP\Widgets;

use STATS4WP\Core\DB;
use STATS4WP\Core\Options;

class CptVisitors
{

    public function register()
    {
        add_action('widgets_init', array( $this, 'register_cpt_visitors_widget' ));
    }

    public function register_cpt_visitors_widget()
    {
        register_widget('STATS4WP\Widgets\stats4wp_cpt_visitors_widget');
    }
}


class stats4wp_cpt_visitors_widget extends \WP_Widget
{

    private $widget_fields;

    private $display = array(
    1 => 'today',
    2 => 'yesterday',
    3 => 'week',
    4 => 'month',
    5 => 'year',
    6 => 'total',
    );

    function __construct()
    {
        parent::__construct(
            'stats4wp_cpt_visitors_widget',
            __('Widget Number of visitors', 'stats4wp'),
            array(
            'description' => __('Display number of visitors', 'stats4wp'),
            'panels_icon' => 'dashicons dashicons-screenoptions',
            'classname'   => 'stats4wp_cpt_visitors_widget',
            )
        );
    }

    public function widget( $args, $instance )
    {

        global $wpdb;
        printf('%s', esc_html($args['before_widget']));
        // title
        if ('' !== $instance['title'] ) {
            printf('<h5 class="widget-title"> %s </h5>', esc_html__('Number of visitors', 'stats4wp'));
        }
        // Output generated fields
        $local_table = DB::table('useronline');
        $user_online = $wpdb->get_row($wpdb->prepare('SELECT COUNT(*) as nb FROM %s', $local_table));
        ?>
        <table width="100%" class="widefat table-stats stats4wp-summary-stats">
            <tbody>
                <tr>
                    <th><?php esc_html_e('Online users', 'stats4wp'); ?>:</th>
                    <th colspan="2" id="th-colspan">
                        <span><?php echo esc_html($user_online->nb); ?></span>
                    </th>
                </tr>
                <tr>
                    <th width="60%"></th>
                    <th class="th-center"><?php esc_html_e('Visitors', 'stats4wp'); ?></th>
                    <th class="th-center"><?php esc_html_e('Visits', 'stats4wp'); ?></th>
                </tr>
        <?php
        $nb = 0;
        while ( ++$nb < 7 ) {
            switch ( $nb ) {
            case 1:
                $to    = $from = gmdate('Y-m-d');
                $title = __('Today', 'stats4wp');
                break;
            case 2:
                $from  = $to = gmdate('Y-m-d', strtotime('-1 days'));
                $title = __('Yesterday', 'stats4wp');
                break;
            case 3:
                $to    = gmdate('Y-m-d');
                $from  = gmdate('Y-m-d', strtotime('-7 days'));
                $title = __('Last 7 Days (Week)', 'stats4wp');
                break;
            case 4:
                $to    = gmdate('Y-m-d');
                $from  = gmdate('Y-m-d', strtotime('-1 months'));
                $title = __('Last 30 Days (Month)', 'stats4wp');
                break;
            case 5:
                $to    = gmdate('Y-m-d');
                $from  = gmdate('Y-m-d', strtotime('-1 years'));
                $title = __('Last 365 Days (Year)', 'stats4wp');
                break;
            case 6:
                $to    = gmdate('Y-m-d');
                $from  = '1999-01-01';
                $title = __('Total', 'stats4wp');
                break;
            }
            if ('checked' === $instance[ $this->display[ $nb ] ] ) {
                if (! isset($wpdb->stats4wp_visitor) ) {
                    $wpdb->stats4wp_visitor = DB::table('visitor');
                }
                $summary_users = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT count(*) as visitors,SUM(hits) as visits 
                            FROM $wpdb->stats4wp_visitor
							 WHERE device!='bot' 
                            AND last_counter BETWEEN %s AND %s",
                        array(
                        $from,
                        $to,
                        )
                    )
                );

                    echo '<tr>
                            <th>' . esc_html($title) . ': </th>
                            <th class="th-center">
                                <span>' . esc_html($summary_users->visitors) . '</span>
                            </th>
                            <th class="th-center">
                                <span>' . esc_html($summary_users->visits) . '</span>
                            </th>
                        </tr>';
            }
        }
        ?>
                </tbody>
            </table>
        <?php

        echo esc_html($args['after_widget']);
    }

    public function form( $instance )
    {
        $title = ( isset($instance['title']) ) ? $instance['title'] : __('Default Title', 'stats4wp');

        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
        foreach ( $this->display as $key => $value ) {
            $checked = ( isset($instance[ $value ]) && 'checked' === $instance[ $value ] ) ? 'checked' : '';
            ?>
            <p>
                <input type="checkbox" id="<?php echo esc_attr($this->get_field_id($value)); ?>" name="<?php echo esc_attr($this->get_field_name($value)); ?>" value="checked" <?php echo esc_attr($checked); ?> >
                <label for="scales"><?php esc_html($value); ?></label>
            </p>
            <?php
        }
    }

    public function update( $new_instance, $old_instance )
    {
        $instance          = array();
        $instance['title'] = ( ! empty($new_instance['title']) ) ? wp_strip_all_tags($new_instance['title']) : '';
        foreach ( $this->display as $key => $value ) {
            $instance[ $value ] = ( ! empty($new_instance[ $value ]) && 'checked' === $new_instance[ $value ] ) ? 'checked' : '';
        }
        return $instance;
    }
}
