<?php
/**
 * @package STATS4WPPlugin
 * @version 1.0.0
 */
namespace STATS4WP\Core;

class SettingsLinks
{
    public function register()
    {
        add_filter("plugin_action_links", array( $this, 'settings_link' ), 10, 5);
    }

    /**
     * Adds the manage link in the plugins list
     *
     * @access global
     * @return string The manage link in the plugins list
     */
    public function settings_link($actions, $STATS4WP_FILE)
    {

        static $plugin;
 
        if (!isset($plugin)) {
            $plugin = plugin_basename(STATS4WP_FILE);
        }
        if ($plugin == $STATS4WP_FILE) {
            $plugin_data = get_plugin_data(STATS4WP_FILE);
            $actions[] = '<a href="admin.php?page=' . STATS4WP_NAME . '_settings">' . __('Settings') . '</a>';
            $actions[] =  '<a href="' . $plugin_data['PluginURI'] . '" target="_blank">' . __('Support') . '</a>';
            $actions[] =  '<a href="' . $plugin_data['AuthorURI'] . '" target="_blank">' . __('all GinkGos plugins ') . '</a>';
        }
            
        return $actions;
    }
}
