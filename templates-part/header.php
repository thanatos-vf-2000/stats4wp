<?php
/**
 * @package STATS4WPPlugin
 * @version 1.4.14
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap">
	<h1>Statistics For WordPress</h1>
	<?php
	self::get_template( array( 'visitor/bare', 'pages/bare' ) );
