<?php
/**
 * @package  STATS4WPPlugin
 * @Version 1.1.0
 * 
 * Desciption: Admin Page
 */

 ?>

<?php 
self::get_template(array('header', 'dashboard/header'));
?>
<div class="wp-clearfix"></div>
<div class="metabox-holder" id="overview-widgets">
    <div class="postbox-container" id="stats4wp-postbox-container-1">
        <div id="side-sortables" class="meta-box-sortables ui-sortable">
            <?php 
                self::get_template(array('dashboard/summary', 'dashboard/top-10-browsers', 'dashboard/top-platforms', 'dashboard/top-10-devices', 'dashboard/users-online'));
            ?>
        </div>
    </div>
    <div class="postbox-container" id="stats4wp-postbox-container-2">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
        <?php 
            self::get_template(array('dashboard/nb-users-hits', 'dashboard/top-10-pages', 'dashboard/top-10-visitors-today', 'dashboard/recent-visitors'));
        ?>
        </div>
    </div>
</div>
<?php 
self::get_template(array('footer'));
?>

