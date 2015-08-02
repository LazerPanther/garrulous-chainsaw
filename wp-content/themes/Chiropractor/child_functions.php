<?php
add_action('wp_enqueue_scripts', 'dequeue_function');
function dequeue_function() {
    
	wp_dequeue_style( 'wpm_reset_css' );
	wp_deregister_style( 'wpm_reset_css' );

}

if(class_exists( 'OT_Loader' )){ 

	function wpm_reset_function() {
		remove_filter('option_tree_settings_args', 'wpm_options_background_dummy', 20);
		add_filter('option_tree_settings_args', 'wpm_options_background', 20);
		
		remove_filter('option_tree_settings_args', 'wpm_options_typhography_dummy', 40);
		add_filter('option_tree_settings_args', 'wpm_options_typhography', 40);
		
		remove_filter('option_tree_settings_args', 'wpm_options_navigation_dummy', 60);
		add_filter('option_tree_settings_args', 'wpm_options_navigation', 60);
		
		remove_filter('option_tree_settings_args', 'wpm_options_featured_advanced_dummy', 95);
		add_filter('option_tree_settings_args', 'wpm_options_featured_advanced', 95);
		
		remove_filter('option_tree_settings_args', 'wpm_options_translation_dummy', 160);
		add_filter('option_tree_settings_args', 'wpm_options_translation', 160);
		
		remove_filter('option_tree_settings_args', 'wpm_options_footer_dummy', 180);
		add_filter('option_tree_settings_args', 'wpm_options_footer', 180);
		
	}
	add_action( 'init', 'wpm_reset_function' );
}

add_theme_support( 'woocommerce' );


function wpm_custom_style_function2(){
include ('assets/css/child-css.php');	
}
add_action('wpm_custom_style_hook','wpm_custom_style_function2', 11);
?>