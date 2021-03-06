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
class Visitors extends BaseController
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
				'page_title' => 'Visitors', 
				'menu_title' => 'Visitors', 
				'capability' => 'manage_options', 
				'menu_slug' => STATS4WP_NAME.'_visitors', 
				'callback' => array( $this->callbacks, 'adminVisitors' )
			)
		);
	}
}