<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.5
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>


<ul id="stats4wp-menu" >
	<li><a class="<?php echo ( isset( $_GET['spage'] ) && sanitize_text_field( wp_unslash( $_GET['spage'] ) ) === 'top' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_pages&spage=top" ><?php echo esc_html( __( 'Top Pages', 'stats4wp' ) ); ?></a></li>
	<li><a class="<?php echo ( isset( $_GET['spage'] ) && sanitize_text_field( wp_unslash( $_GET['spage'] ) ) === 'type' ) ? 'active' : ''; ?>" href="/wp-admin/admin.php?page=stats4wp_pages&spage=type" ><?php echo esc_html( __( 'Type', 'stats4wp' ) ); ?></a></li>
</ul>
