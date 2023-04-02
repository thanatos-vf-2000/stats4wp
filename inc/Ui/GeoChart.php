<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.2
 */

namespace STATS4WP\Ui;

use STATS4WP\Core\BaseController;

use STATS4WP\Core\Options;

/**
* GeoChart
*/
class GeoChart extends BaseController
{
    public function register()
    {
        add_action('init', array( $this, 'init' ));
        global $pagenow;
        $admin_pages = [ 'admin.php', 'index.php', 'plugins.php', 'tools.php' ];
        if (Options::get_option('geochart') == false && in_array($pagenow, $admin_pages)) {
            add_action('admin_notices', array( $this, 'geochart_plugins' ));
        }
    }

    public function init()
    {
        add_action('wp_ajax_stats4wp_remove_geochart', array( $this, 'dismiss_geochart' ));
    }

    /**
     * Display Geochart plugins notice.
     */
    public function geochart_plugins()
    {
        if (! get_option('stats4wp_remove_geochart')) {
            ?>
            <div id="stats4wp-dismiss-geochart" class="notice notice-info is-dismissible">
                <div class="stats4wp-geochart-img">
                <img src="<?php echo esc_attr(STATS4WP_URL); ?>/assets/images/geochart.png" alt="GeoChart" title="GeoChart">
                </div>
                <div class="stats4wp-geochart-txt">
                    <p>
                        <?php
                        $link = 'https://developers.google.com/maps/terms';
                        printf(__('%1$s v1.3.2 add a map feature using Google Geochart, see %2$s.', 'stats4wp'), '<em>Stats4WP</em>', '<a href="' . esc_attr($link) . '"><em>Google Maps Terms of Service</em></a>');
                        ?>
                    </p>
                    <p><?php echo esc_html('Enable maps in the plugin options.', 'stats4wp');?> <a href="<?php echo admin_url('admin.php?page=stats4wp_settings'); ?>"><?php echo esc_html('Settings.', 'stats4wp');?></a></p>
                </div>
            </div>
            <?php
        }
    }

    /**
     * Ajax callback to disable the geochart notice permanently.
     */
    public function dismiss_geochart()
    {
        wp_send_json_success(
            array(
                        'notice_removed' => update_option('stats4wp_remove_geochart', true),
                )
        );
        exit;
    }
}
