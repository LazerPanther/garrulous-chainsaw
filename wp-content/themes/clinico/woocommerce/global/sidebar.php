<?php
/**
 * Sidebar
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version	 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	$woo_sidebar = cws_get_option('def-woo-sidebar');
	dynamic_sidebar($woo_sidebar);

?>