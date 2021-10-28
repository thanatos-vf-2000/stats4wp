<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 */

namespace STATS4WP\Api;

use STATS4WP\Core\DB;


/**
* 
*/
class AdminGraph
{
    const ARG_FROM = "stats4wp-from";
	const ARG_TO = "stats4wp-to";
    const ARG_INTERVAL = "stats4wp-interval";
    const ARG_INTERVAL_FLAG = "stats4wp-interval-flag";

    public static $date_range = array(
        10  => '10 Days',
        20  => '20 Days',
        30  => '30 Days',
        60  => '2 Months',
        90  => '3 Months',
        180 => '6 Months',
        270 => '9 Months',
        365 => '1 Year'
    );

   
    /**
     * Form date
     */
    public static function select_date()
    {
        global $wp;
        ?>
        <form method="GET" action="<?php echo admin_url('admin.php'); ?>">
        <input type="hidden" name="page" value="<?php echo esc_attr((isset($_GET['page'])? sanitize_text_field($_GET['page']) : 'stats4wp_visitors'));?>"/>
        <?php
        if (isset($_GET['spage'])) { echo '<input type="hidden" name="spage" value="' . esc_attr($_GET['spage']) .'"/>';}
        ?>
            <?php wp_nonce_field('stats4wp-opt', 'stats4wp-verif'); ?>
            <table class="form-table-visitor" role="presentation">
                <tr>
                    <th scope="row"><?php  _e( 'From: ' , 'stats4wp'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php  _e( 'From: ' , 'stats4wp'); ?></span></legend><label for="date_from">
                            <input type="date" id="<?php echo esc_attr(self::ARG_FROM); ?>" name="<?php echo esc_attr(self::ARG_FROM); ?>" value="<?php echo esc_attr(self::get_var(self::ARG_FROM)); ?>" <?php echo esc_attr((self::get_var(self::ARG_INTERVAL_FLAG) == true) ? 'disabled' : ''); ?> /></label>
                        </fieldset>
                    </td>
                    <th scope="row"><?php _e( 'To: ' , 'stats4wp'); ?></th>
                    <td>
                        <fieldset><legend class="screen-reader-text"><span><?php  _e( 'To: ' , 'stats4wp'); ?></span></legend><label for="date_to">
                            <input type="date" id="<?php echo esc_attr(self::ARG_TO); ?>" name="<?php echo esc_attr(self::ARG_TO); ?>" value="<?php echo esc_attr(self::get_var(self::ARG_TO)); ?>" /></label>
                        </fieldset>
                    </td>
                    <th scope="row"><?php _e( 'Day interval' , 'stats4wp'); ?></th>
                    <td>
                        <select name="<?php echo esc_attr(self::ARG_INTERVAL); ?>" id="<?php echo esc_attr(self::ARG_INTERVAL); ?>" <?php echo esc_attr((self::get_var(self::ARG_INTERVAL_FLAG) == true) ? '' : 'disabled'); ?>>
                            <?php
                            foreach ( self::$date_range as $k => $v ) {
                                $display_name  = stripslashes($v);
                                if ($k == self::get_var(self::ARG_INTERVAL)) {
                                    $selected='selected="selected"';
                                } else {
                                    $selected='';
                                }
                                echo '<option '. esc_attr($selected) .' value="'. esc_attr($k) .'">'. esc_html($display_name) .'</option>';
                            }
                            ?>
                        </select>
                        <input type="checkbox" id="<?php echo esc_attr(self::ARG_INTERVAL_FLAG); ?>" name="<?php echo esc_attr(self::ARG_INTERVAL_FLAG); ?>" value="flag" <?php echo esc_attr((self::get_var(self::ARG_INTERVAL_FLAG) == true) ? 'checked' : ''); ?> >
                    </td>
                </tr>
            </table>
            <p><?php submit_button( __("Refresh", 'stats4wp'), 'primary', 'submit-date',false); ?></p>
            </form>
        <?php
    }

    /**
     * Read Var in $GET or default value
     */
    public static function get_var($var)
    {
        if ($var == self::ARG_INTERVAL_FLAG) {
            if (isset($_GET[self::ARG_INTERVAL_FLAG])) return ($_GET[self::ARG_INTERVAL_FLAG] = "flag") ? true : false;
            return false;
        }
        if(isset($_GET[$var])) return $_GET[$var];
        switch ($var) {
            case self::ARG_INTERVAL:
                return 10;
                break;
            case self::ARG_TO:
                return date("Y-m-d");
                break;
            case self::ARG_FROM:
                return date("Y-m-d", strtotime('-'. self::get_var(self::ARG_INTERVAL). ' days'));
                break;
        }
        return false;
    }

    /**
     * Get date and interval type
     */
    public static function getdate($type = 'all')
    {
        global $wpdb;
        if ($type == 'all') {
            $all_date = $wpdb->get_row("SELECT min(last_counter) as minimum, max(last_counter) as maximum FROM ". DB::table('visitor'));
            $from = $all_date->minimum;
            $to = $all_date->maximum;
        } else {
            $to = self::get_var(self::ARG_TO);
            if (self::get_var(self::ARG_INTERVAL_FLAG)) {
                $dt = new \DateTime($to);
                $from = date("Y-m-d", strtotime('-'. self::get_var(self::ARG_INTERVAL). ' days', $dt->getTimestamp()));
            } else {
                $from = self::get_var(self::ARG_FROM);
            }
        }
        $f = new \DateTime($from);
        $t = new \DateTime($to);
        $interval = date_diff($t,$f)->days;
        if ($interval <= 30) {
            $interval = 'days';
        } elseif ($interval <= 150) {
            $interval = 'weeks';
        } else {
            $interval='month';
        }
        
        return array('from' => $from,
            'to'        => $to,
            'interval'  => $interval);
        
    }
    
}