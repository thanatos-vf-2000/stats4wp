<?php
/**
 *
 * @package STATS4WPPlugin
 * @version 1.4.14
 */


if (! defined('ABSPATH') ) {
    exit;
}

?>

<ul id="stats4wp-menu" >
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'agent' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=agent" ><?php echo esc_html(__('Navigator', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'os' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=os" ><?php echo esc_html(__('Operating system', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'device' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=device" ><?php echo esc_html(__('Device', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'location' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=location" ><?php echo esc_html(__('Location', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'lang' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=lang" ><?php echo esc_html(__('Language', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'nb-users' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=nb-users" ><?php echo esc_html(__('Number of users', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'hits' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=hits" ><?php echo esc_html(__('Hits', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'referred' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=referred" ><?php echo esc_html(__('Referred', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'by-hours' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=by-hours" ><?php echo esc_html(__('Visitors by hours', 'stats4wp')); ?></a></li>
    <li><a class="<?php echo ( isset($_GET['spage']) && sanitize_text_field(wp_unslash($_GET['spage'])) === 'bots' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_visitors&spage=bots" ><?php echo esc_html(__('Bots', 'stats4wp')); ?></a></li>
</ul>
