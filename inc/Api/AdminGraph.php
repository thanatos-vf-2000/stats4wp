<?php
/**
 * Admin Graph
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   STATS4WPPlugin
 * @author    Franck VANHOUCKE <ct4gg@ginkgos.net>
 * @copyright 2021-2023 Copyright 2023, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later
 * @version   1.4.14 GIT:https://github.com/thanatos-vf-2000/WordPress
 * @link      https://ginkgos.net
 */

namespace STATS4WP\Api;

use STATS4WP\Core\DB;

/**
 * Class AdminGraph
 */
class AdminGraph
{

    const ARG_FROM            = 'stats4wp-from';
    const ARG_TO              = 'stats4wp-to';
    const ARG_INTERVAL        = 'stats4wp-interval';
    const ARG_INTERVAL_FLAG   = 'stats4wp-interval-flag';
    const ARG_DASHBOARD_DATA  = 'stats4wp-data';
    const ARG_DASHBOARD_GROUP = 'stats4wp-group';

    public static $date_range = array(
    10  => '10 Days',
    20  => '20 Days',
    30  => '30 Days',
    60  => '2 Months',
    90  => '3 Months',
    180 => '6 Months',
    270 => '9 Months',
    365 => '1 Year',
    730 => '2 Years',
    );

    public static $date_dashboard = array(
    1 => '14 Days',
    2 => '1 Month',
    3 => '2 Months',
    4 => '3 Months',
    5 => '1 Year',
    6 => '2 Years',
    7 => 'All',
    );

    public static $date_group = array(
    1 => 'Day',
    2 => 'Week',
    3 => 'Month',
    4 => 'Trimester',
    5 => 'Year',
    );


    /**
     * Form date
     */
    public static function select_date()
    {
        global $wp;
        ?>
        <form method="GET" action="<?php esc_url(admin_url('admin.php')); ?>">
        <input type="hidden" name="page" value="<?php printf('%s', esc_html(isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : 'stats4wp_visitors')); ?>"/>
        <?php
        if (isset($_GET['spage']) ) {
            echo '<input type="hidden" name="spage" value="' . esc_attr(sanitize_text_field(wp_unslash($_GET['spage']))) . '"/>';
        }
        ?>
        <?php wp_nonce_field('stats4wp-opt', 'stats4wp-verif'); ?>
            <table class="form-table-visitor" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('From', 'stats4wp'); ?>: </th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php esc_html_e('From: ', 'stats4wp'); ?></span></legend><label for="date_from">
                            <input type="date" id="<?php printf('%s', esc_html(self::ARG_FROM)); ?>" name="<?php printf('%s', esc_html(self::ARG_FROM)); ?>" value="<?php printf('%s', esc_html(self::get_var(self::ARG_FROM))); ?>" <?php printf('%s', esc_html(( self::get_var(self::ARG_INTERVAL_FLAG) === true ) ? 'disabled' : '')); ?> /></label>
                        </fieldset>
                    </td>
                    <th scope="row"><?php esc_html_e('To', 'stats4wp'); ?>: </th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php esc_html_e('To: ', 'stats4wp'); ?></span></legend><label for="date_to">
                            <input type="date" id="<?php printf('%s', esc_html(self::ARG_TO)); ?>" name="<?php printf('%s', esc_html(self::ARG_TO)); ?>" value="<?php printf('%s', esc_html(self::get_var(self::ARG_TO))); ?>" /></label>
                        </fieldset>
                    </td>
                    <th scope="row"><?php esc_html_e('Day interval', 'stats4wp'); ?></th>
                    <td>
                        <select name="<?php printf('%s', esc_html(self::ARG_INTERVAL)); ?>" id="<?php printf('%s', esc_html(self::ARG_INTERVAL)); ?>" <?php printf('%s', esc_html(( self::get_var(self::ARG_INTERVAL_FLAG) === true ) ? '' : 'disabled')); ?>>
        <?php
        foreach ( self::$date_range as $k => $v ) {
            $display_name = stripslashes($v);
            if (self::get_var(self::ARG_INTERVAL === $k) ) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo '<option ' . esc_attr($selected) . ' value="' . esc_attr($k) . '">' . esc_html($display_name) . '</option>';
        }
        ?>
                        </select>
                        <input type="checkbox" id="<?php printf('%s', esc_html(self::ARG_INTERVAL_FLAG)); ?>" name="<?php printf('%s', esc_html(self::ARG_INTERVAL_FLAG)); ?>" value="flag" <?php printf('%s', esc_html(( self::get_var(self::ARG_INTERVAL_FLAG) === true ) ? 'checked' : '')); ?> >
                    </td>
                </tr>
            </table>
            <p><?php submit_button(__('Refresh', 'stats4wp'), 'primary', 'submit-date', false); ?></p>
            </form>
        <?php
    }

    /**
     * Read Var in $GET or default value
     */
    public static function get_var( $var )
    {
        global $wpdb;
        if (self::ARG_INTERVAL_FLAG === $var ) {
            if (isset($_GET[ self::ARG_INTERVAL_FLAG ]) ) {
                $flag = sanitize_text_field(wp_unslash($_GET[ self::ARG_INTERVAL_FLAG ]));
                return ( 'flag' === $flag ) ? true : false;
            }
            return false;
        }
        if (isset($_GET[ $var ]) ) {
            return sanitize_text_field(wp_unslash($_GET[ $var ]));
        }
        switch ( $var ) {
        case self::ARG_INTERVAL:
            return 10;
                break;
        case self::ARG_TO:
            return gmdate('Y-m-d');
                break;
        case self::ARG_FROM:
            if (isset($_GET['page']) && sanitize_text_field(wp_unslash($_GET['page'])) === 'stats4wp_plugin' ) {
                switch ( self::get_var(self::ARG_DASHBOARD_DATA) ) {
                case 1:
                    return gmdate('Y-m-d', strtotime('-14 days'));
                break;
                case 2:
                    return gmdate('Y-m-d', strtotime('-1 months'));
                        break;
                case 3:
                    return gmdate('Y-m-d', strtotime('-2 months'));
                        break;
                case 4:
                    return gmdate('Y-m-d', strtotime('-3 months'));
                        break;
                case 5:
                    return gmdate('Y-m-d', strtotime('-1 years'));
                        break;
                case 6:
                    return gmdate('Y-m-d', strtotime('-2 years'));
                        break;
                case 7:
                    if (! isset($wpdb->stats4wp_visitor) ) {
                        $wpdb->stats4wp_visitor = DB::table('visitor');
                    }
                    $visitor = $wpdb->get_row("SELECT min(last_counter) as minimum FROM $wpdb->stats4wp_visitor");
                    return $visitor->minimum;
                        break;
                }
            } else {
                return gmdate('Y-m-d', strtotime('-' . self::get_var(self::ARG_INTERVAL) . ' days'));
            }
            break;
        case self::ARG_DASHBOARD_DATA:
            return 1;
                break;
        case self::ARG_DASHBOARD_GROUP:
            return 1;
                break;
        }
        return false;
    }

    /**
     * Get date and interval type
     */
    public static function getdate( $type = 'all' )
    {
        global $wpdb;
        if ('all' === $type ) {
            if (! isset($wpdb->stats4wp_visitor) ) {
                $wpdb->stats4wp_visitor = DB::table('visitor');
            }
            $all_date = $wpdb->get_row("SELECT min(last_counter) as minimum, max(last_counter) as maximum FROM $wpdb->stats4wp_visitor");
            $from     = $all_date->minimum;
            $to       = $all_date->maximum;
        } else {
            $to = self::get_var(self::ARG_TO);
            if (self::get_var(self::ARG_INTERVAL_FLAG) ) {
                $dt   = new \DateTime($to);
                $from = gmdate('Y-m-d', strtotime('-' . self::get_var(self::ARG_INTERVAL) . ' days', $dt->getTimestamp()));
            } else {
                $from = self::get_var(self::ARG_FROM);
            }
        }
        $f        = new \DateTime($from);
        $t        = new \DateTime($to);
        $interval = date_diff($t, $f)->days;
        if ($interval <= 30 ) {
            $interval = 'days';
        } elseif ($interval <= 150 ) {
            $interval = 'weeks';
        } else {
            $interval = 'month';
        }

        return array(
        'from'     => $from,
        'to'       => $to,
        'interval' => $interval,
        'group'    => self::get_var(self::ARG_DASHBOARD_GROUP),
        );
    }

    /**
     * Select date Dashboard
     */
    public static function select_date_dashboard()
    {
        ?>
        <form method="GET" action="<?php esc_url(admin_url('admin.php')); ?>">
        <input type="hidden" name="page" value="<?php printf('%s', esc_html(( isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : 'stats4wp_visitors' ))); ?>"/>
        <?php wp_nonce_field('stats4wp-opt', 'stats4wp-verif'); ?>
            <table class="form-table-visitor" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Data last', 'stats4wp'); ?>: </th>
                    <td>
                        <select name="<?php printf('%s', esc_html(self::ARG_DASHBOARD_DATA)); ?>" id="<?php printf('%s', esc_html(self::ARG_DASHBOARD_DATA)); ?>" >
        <?php
        foreach ( self::$date_dashboard as $k => $v ) {
            $display_name = stripslashes($v);
            if (self::get_var(self::ARG_DASHBOARD_DATA) === $k ) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo '<option ' . esc_attr($selected) . ' value="' . esc_attr($k) . '">' . esc_html($display_name) . '</option>';
        }
        ?>
                        </select>
                    </td>
                    <th scope="row"><?php esc_html_e('Display by', 'stats4wp'); ?>: </th>
                    <td>
                        <select name="<?php printf('%s', esc_html(self::ARG_DASHBOARD_GROUP)); ?>" id="<?php printf('%s', esc_html(self::ARG_DASHBOARD_GROUP)); ?>" >
          <?php
            foreach ( self::$date_group as $k => $v ) {
                $display_name = stripslashes($v);
                if (self::get_var(self::ARG_DASHBOARD_GROUP) === $k ) {
                       $selected = 'selected="selected"';
                } else {
                    $selected = '';
                }
                echo '<option ' . esc_attr($selected) . ' value="' . esc_attr($k) . '">' . esc_html($display_name) . '</option>';
            }
            ?>
                        </select>
                    </td>
                    <td><?php submit_button(__('Refresh', 'stats4wp'), 'primary', 'submit-date', false); ?></td>
                </tr>
            </table>
            </form>
        <?php
    }
}
