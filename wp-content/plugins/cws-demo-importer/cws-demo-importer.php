<?php
/*
Plugin Name: CWS Demo Importer
Plugin URI: http://creaws.com/
Description: internal use for creaws themes only.
Text Domain: cws_demo_imp
Version: 1.0.0
*/

if (!defined('CWS_DEMO_IMP_PLUGIN_NAME'))
	define('CWS_DEMO_IMP_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('CWS_DEMO_IMP_PLUGIN_DIR'))
	define('CWS_DEMO_IMP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . CWS_DEMO_IMP_PLUGIN_NAME);

if (!defined('CWS_DEMO_IMP_PLUGIN_URL'))
	define('CWS_DEMO_IMP_PLUGIN_URL', WP_PLUGIN_URL . '/' . CWS_DEMO_IMP_PLUGIN_NAME);


add_action( 'admin_init', 'register_importers' );

function register_importers() {
	register_importer( 'cws_demo_imp', __( 'CWS Demo Importer', 'cws_demo_imp' ), __( 'Import CWS theme\'s demo content.', 'cws_demo_imp'), 'cws_importer' );
}

function cws_importer() {
	// includes
	require 'importer.php';
	// Dispatch
	$importer = new WP_CWS_Demo_Importer();
	$importer->dispatch();
}
?>