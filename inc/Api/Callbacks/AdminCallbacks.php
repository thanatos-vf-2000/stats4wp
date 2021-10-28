<?php 
/**
 * @package  STATS4WPPlugin
 */
namespace STATS4WP\Api\Callbacks;

use STATS4WP\Core\BaseController;

class AdminCallbacks extends BaseController
{
	public function adminDashboard()
	{
		return require_once( "$this->plugin_path/templates/dashboard.php" );
	}

	public function adminSettings()
	{
		return require_once( "$this->plugin_path/templates/settings.php" );
	}

	public function adminVisitors()
	{
		return require_once( "$this->plugin_path/templates/visitors.php" );
	}

	public function adminPages()
	{
		return require_once( "$this->plugin_path/templates/pages.php" );
	}

}