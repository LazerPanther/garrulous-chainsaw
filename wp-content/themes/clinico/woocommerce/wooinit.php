<?php 


//Declare Woo Support
	add_theme_support('woocommerce');	

// Posts per Page

function show_products_per_page() {
	return (int)cws_get_option('woo-num-products');
}

add_filter('loop_shop_per_page', 'show_products_per_page', 20 );

// Font Settings

function woocommerce_header_font_filter (){
	$out = "";
	$font_array = cws_get_option('header-font');
	if (isset($font_array)) {
		$out .= 'div.woocommerce form p.form-row label:not(.checkbox),
					.woocommerce-tabs form p label,
					ul.woocommerce-error li,
					.woocommerce-tabs .tabs li a,
					#comments .comment_container,
					.woocommerce .order .order-total,
					#searchform label.screen-reader-text,
					.widget_shopping_cart_content p,
					.woocommerce .woocommerce-tabs .shop_attributes th,
					.woocommerce #content h1.page-title{color:' . $font_array["color"] . '}';
	}	
	echo $out;
}

add_action ( 'header_font_hook', 'woocommerce_header_font_filter' );

function woocommerce_body_font_filter (){
	$out = "";
	$font_array = cws_get_option('body-font');
	if (isset($font_array)) {
		$out .= 'ul.product_list_widget li>*,
				.woocommerce .toggle_sidebar .switcher{line-height:' . $font_array["line-height"] . '}';
	}	
	echo $out;
}

add_action ( 'body_font_hook', 'woocommerce_body_font_filter' );
	
//disable woocomerece stylesheets
	add_filter( 'woocommerce_enqueue_styles', '__return_false' );	

//declare woocomerece custom theme stylesheets
function wp_enqueue_woocommerce_style(){
	global $wp_styles;
	wp_register_style( 'woocommerce', THEME_URI . '/woocommerce/css/woocommerce.css', array( 'main' ) );
	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_style( 'woocommerce' );
		$wp_styles->add_data( 'woocommerce', 'rtl', true );

	}
}
add_action( 'wp_enqueue_scripts', 'wp_enqueue_woocommerce_style' );

function wp_enqueue_woocommerce_script(){
	wp_register_script( 'cws_woo', THEME_URI . '/woocommerce/js/woocommerce.js' );
	if ( class_exists( 'woocommerce' ) ) {
		wp_enqueue_script( 'cws_woo' );
	}
}
add_action( 'wp_enqueue_scripts', 'wp_enqueue_woocommerce_script' );	

	
// Change the breadcrumb delimiter from '/' to '>'
	add_filter( 'woocommerce_breadcrumb_defaults', 'my_change_breadcrumb_delimiter' );
	function my_change_breadcrumb_delimiter( $defaults ) {
		$defaults['delimiter'] = ' » ';
		return $defaults;
	}
	
//Reposition WooCommerce breadcrumb 
	function woocommerce_remove_breadcrumb(){
	remove_action( 
		'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
	}
	add_action(
		'woocommerce_before_main_content', 'woocommerce_remove_breadcrumb'
	);

	function woocommerce_custom_breadcrumb(){
		woocommerce_breadcrumb();
	}
	add_action( 'woo_custom_breadcrumb', 'woocommerce_custom_breadcrumb' );	
	
//Remove Page tile from the Archive 

	function override_page_title() {
		return false;
	}
	add_filter('woocommerce_show_page_title', 'override_page_title');

	
// Hook in on activation
	add_action( 'init', 'happykids_woocommerce_image_dimensions', 1 );

//Define image sizes
	function happykids_woocommerce_image_dimensions() {
  	$catalog = array(
		'width' 	=> '270',	// px
		'height'	=> '270',	// px
		'crop'		=> 1 		// true
	);

	$single = array(
		'width' 	=> '300',	// px
		'height'	=> '300',	// px
		'crop'		=> 1 		// true
	);

	$thumbnail = array(
		'width' 	=> '90',	// px
		'height'	=> '90',	// px
		'crop'		=> 0 		// false
	);	

	// Image sizes
	update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
	update_option( 'shop_single_image_size', $single ); 		// Single product image
	update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
}



?>