<?php
/*
Plugin Name: CWS Builder
Plugin URI: http://pb.creaws.com/
Description: internal use for CreaWS themes only.
Text Domain: cws_pb
Version: 1.1.6
*/

define( 'CWS_PB_VERSION', '1.1.6' );
define( 'CWS_PB_REQUIRED_WP_VERSION', '3.9' );

if (!defined('CWS_PB_THEME_DIR'))
	define('CWS_PB_THEME_DIR', ABSPATH . 'wp-content/themes/' . get_template());

if (!defined('CWS_PB_HOST'))
	define('CWS_PB_HOST', 'http://up.cwsthemes.com/cwsbuilder');

if (!defined('CWS_PB_PLUGIN_NAME'))
	define('CWS_PB_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('CWS_PB_PLUGIN_DIR'))
	define('CWS_PB_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CWS_PB_PLUGIN_NAME);

if (!defined('CWS_PB_PLUGIN_URL'))
	define('CWS_PB_PLUGIN_URL', WP_PLUGIN_URL . '/' . CWS_PB_PLUGIN_NAME);

require_once CWS_PB_PLUGIN_DIR . '/shortcodes.php';

function admin_scripts ($hook) {
	global $typenow;
	global $wp_styles;

	$is_richedit = get_user_option('rich_editing', get_current_user_id());

	if ( ('post-new.php' === $hook || 'post.php' === $hook) && 'page' === $typenow  && 'true' == $is_richedit ) {
		if (wp_script_is('editor-expand')) {
			// starting WP4.0, this script mess things up here
			wp_dequeue_script('editor-expand');
		}
		wp_enqueue_media();
		//wp_enqueue_script( 'yui', 'http://yui.yahooapis.com/3.18.1/build/yui/yui-min.js', '', '', true );
		wp_enqueue_script( 'yui', CWS_PB_PLUGIN_URL . '/yui/yui-min.js', '', '', true );
		wp_enqueue_style( 'yui-css', CWS_PB_PLUGIN_URL . '/yui/12.css' );
		wp_enqueue_script( 'pb-js', CWS_PB_PLUGIN_URL . '/pb.js', '', CWS_PB_VERSION, true );
		wp_enqueue_style( 'cws-pb', CWS_PB_PLUGIN_URL . '/cws-pb.css' );
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
		$wp_styles->add_data('cws-pb', 'rtl', true);

		add_filter('the_editor', 'cws_content');
		add_filter('the_editor_content', 'cws_ed_content');
	}
}

//add_filter('the_editor', 'cws_content');
//add_filter('the_editor_content', 'cws_ed_content');

add_action( 'admin_enqueue_scripts', 'admin_scripts', 11);


function cws_ed_content($a) {
	echo '<div id="cws-pb-cont" style="display:none">';
	echo $a;
	echo '</div>';
	return $a;
}

function cws_content ( $content ) {
	preg_match("/<textarea[^>]*id=[\"']([^\"']+)\"/", $content, $matches);
	$id = $matches[1];
	if( $id !== "content" )
		return $content;
	ob_start();
	include_once( CWS_PB_PLUGIN_DIR . '/pb.php' );
	return $content . ob_get_clean();
}

add_filter( 'pre_set_site_transient_update_plugins', 'cws_check_for_update_pb' );
set_transient('update_plugins', 24);

function cws_check_for_update_pb($transient) {
	if (empty($transient->checked))
		return $transient;
	$pb_path = CWS_PB_PLUGIN_NAME . '/' . CWS_PB_PLUGIN_NAME . '.php';

	$result = wp_remote_get(CWS_PB_HOST . '/cws-pb.php');
	if ( isset($result->errors) ) {
		return $transient;
	} else {
		if (200 == $result['response']['code']) {
			$resp = json_decode($result['body']);
			if ( version_compare( CWS_PB_VERSION, $resp->new_version, '<' ) ) {
				$transient->response[$pb_path] = $resp;
			}
		}
	}
	return $transient;
}

$file   = basename( __FILE__ );
$folder = basename( dirname( __FILE__ ) );
$hook = "in_plugin_update_message-{$folder}/{$file}";

function cws_plugins_api($res, $action = null, $args = null) {
	if ( ($action == 'plugin_information') && isset($args->slug) && ($args->slug == CWS_PB_PLUGIN_NAME) ) {
		$result = wp_remote_get(CWS_PB_HOST . '/cws-pb.php?info=1');
		if (200 == $result['response']['code']) {
			$res = json_decode($result['body'], true);
			$res = (object) array_map(__FUNCTION__, $res);
		}
	}
	return $res;
}

add_filter('plugins_api', 'cws_plugins_api', 20, 3);
?>
