<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */

namespace STATS4WP\Api\Callbacks;

use STATS4WP\Core\BaseController;

class AdminCallbacks extends BaseController {



	public function adminDashboard() {
		return include_once "$this->plugin_path/templates/dashboard.php";
	}

	public function adminSettings() {
		return include_once "$this->plugin_path/templates/settings.php";
	}

	public function adminVisitors() {
		return include_once "$this->plugin_path/templates/visitors.php";
	}

	public function adminPages() {
		return include_once "$this->plugin_path/templates/pages.php";
	}

	public function adminCSVExport() {
		return include_once "$this->plugin_path/templates/cvs-export.php";
	}
}
