<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 *
 * Desciption: Admin Page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


self::get_template( array( 'header', 'dashboard/header' ) );
?>
<div class="wp-clearfix"></div>
<div class="metabox-holder" id="overview-widgets">
	<div class="postbox-container" id="stats4wp-postbox-container-1">
		<div id="side-sortables" class="meta-box-sortables ui-sortable">
			<?php
				self::get_template( array( 'dashboard/summary', 'dashboard/top-10-browsers', 'dashboard/top-platforms', 'dashboard/top-10-devices', 'dashboard/users-online' ) );
			?>
		</div>
	</div>
	<div class="postbox-container" id="stats4wp-postbox-container-2">
		<div id="normal-sortables" class="meta-box-sortables ui-sortable">
		<?php
		self::get_template( array( 'dashboard/country-maps', 'dashboard/nb-users-hits', 'dashboard/visitors', 'dashboard/top-10-pages', 'dashboard/top-10-visitors-today', 'dashboard/recent-visitors' ) );
		?>
		</div>
	</div>
</div>
<?php
self::get_template( array( 'footer' ) );
?>
