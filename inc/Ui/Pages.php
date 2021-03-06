<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.0.0
 */

namespace STATS4WP\Ui;

use STATS4WP\Core\BaseController;
use STATS4WP\Api\SettingsApi;
use STATS4WP\Api\Callbacks\AdminCallbacks;

/**
* 
*/
class Pages extends BaseController
{
    public $callbacks;

	public $subpages = array();

	public function register()
	{
		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->setSubpages();

		$this->settings->addSubPages( $this->subpages )->register();
	}

	public function setSubpages()
	{
		$this->subpages = array(
			array(
				'parent_slug' => STATS4WP_NAME.'_plugin', 
				'page_title' => 'Pages', 
				'menu_title' => 'Pages', 
				'capability' => 'manage_options', 
				'menu_slug' => STATS4WP_NAME.'_pages', 
				'callback' => array( $this->callbacks, 'adminPages' )
			)
		);
	}
}