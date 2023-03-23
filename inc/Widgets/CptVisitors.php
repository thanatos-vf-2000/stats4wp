<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.1
 */
namespace STATS4WP\Widgets;

use STATS4WP\Core\DB;
use STATS4WP\Core\Options;

class CptVisitors
{
    public function register()
    {
        add_action('widgets_init', array($this, 'register_cpt_visitors_widget'));
    }

    public function register_cpt_visitors_widget()
    {
        register_widget('STATS4WP\Widgets\stats4wp_cpt_visitors_widget');
    }
}


class stats4wp_cpt_visitors_widget extends \WP_Widget
{
    private $widget_fields;

    private $display  = array(
        1   =>  'today',
        2   =>  'yesterday',
        3   =>  'week',
        4   =>  'month',
        5   =>  'year',
        6   =>  'total'
    );

    function __construct()
    {
        parent::__construct(
            'stats4wp_cpt_visitors_widget',
            esc_html__('Widget Number of visitors', 'stats4wp'),
            array( 'description' => esc_html__('Display number of visitors', 'stats4wp'),
                'panels_icon' => 'dashicons dashicons-screenoptions',
                'classname' => 'stats4wp_cpt_visitors_widget',
                )
        );
    }

    public function widget($args, $instance)
    {

        global $wpdb;
        _e($args['before_widget']);
        //title
        if ($instance[ 'title' ] <>'') {
            echo '<h5 class="widget-title">'.__('Number of visitors', 'stats4wp'). '</h5>';
        }
        // Output generated fields
        $user_online = $wpdb->get_row("SELECT COUNT(*) as nb FROM ". DB::table('useronline'));
        ?>
        <table width="100%" class="widefat table-stats stats4wp-summary-stats">
            <tbody>
                <tr>
                    <th><?php _e('Online users', 'stats4wp'); ?>:</th>
                    <th colspan="2" id="th-colspan">
                        <span><?php echo esc_html($user_online->nb); ?></span>
                    </th>
                </tr>
                <tr>
                    <th width="60%"></th>
                    <th class="th-center"><?php _e('Visitors', 'stats4wp'); ?></th>
                    <th class="th-center"><?php _e('Visits', 'stats4wp'); ?></th>
                </tr>
                <?php
                $nb=0;
                while (++$nb < 7) {
                    switch ($nb) {
                        case 1:
                            $to = $from = date("Y-m-d");
                            $title = __('Today', 'stats4wp');
                            break;
                        case 2:
                            $from = $to = date("Y-m-d", strtotime('-1 days'));
                            $title = __('Yesterday', 'stats4wp');
                            break;
                        case 3:
                            $to = date("Y-m-d");
                            $from = date("Y-m-d", strtotime('-7 days'));
                            $title = __('Last 7 Days (Week)', 'stats4wp');
                            break;
                        case 4:
                            $to = date("Y-m-d");
                            $from = date("Y-m-d", strtotime('-1 months'));
                            $title = __('Last 30 Days (Month)', 'stats4wp');
                            break;
                        case 5:
                            $to = date("Y-m-d");
                            $from = date("Y-m-d", strtotime('-1 years'));
                            $title = __('Last 365 Days (Year)', 'stats4wp');
                            break;
                        case 6:
                            $to = date("Y-m-d");
                            $from = '1999-01-01';
                            $title = __('Total', 'stats4wp');
                            break;
                    }
                    if ($instance[ $this->display[$nb] ] == 'checked') {
                        $summary_users = $wpdb->get_row("SELECT count(*) as visitors,SUM(hits) as visits 
                            FROM ". DB::table('visitor').
                            " WHERE device!='bot' 
                            AND last_counter BETWEEN '". $from ."' AND '". $to."'");
                        
                        echo '<tr>
                            <th>'.esc_html($title).': </th>
                            <th class="th-center">
                                <span>'.esc_html($summary_users->visitors).'</span>
                            </th>
                            <th class="th-center">
                                <span>'.esc_html($summary_users->visits).'</span>
                            </th>
                        </tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        <?php

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = (isset($instance[ 'title' ])) ? $instance[ 'title' ] : __('Default Title', 'stats4wp');
        
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
        foreach ($this->display as $key => $value) {
            $checked = (isset($instance[ $value ]) && $instance[ $value ] == 'checked') ? 'checked' : '' ;
            ?>
            <p>
                <input type="checkbox" id="<?php echo $this->get_field_id($value); ?>" name="<?php echo $this->get_field_name($value); ?>" value="checked" <?php echo $checked; ?> >
                <label for="scales"><?php esc_html($value); ?></label>
            </p>
            <?php
        }
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = ( ! empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        foreach ($this->display as $key => $value) {
            $instance[$value] = ( ! empty($new_instance[$value]) && $new_instance[$value]=='checked' ) ? 'checked' : '';
        }
        return $instance;
    }
}
