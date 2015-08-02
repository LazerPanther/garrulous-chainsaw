<?php
	global $wp_query;

	$theme = wp_get_theme();

	define('THEME_NAME', $theme->get( 'Name' ));
	define('THEME_SLUG', $theme->get( 'TextDomain' ));
	define('THEME_VERSION', $theme->get( 'Version' ));
	define('THEME_DIR', get_template_directory());
	define('THEME_URI', get_template_directory_uri());
	define('THEME_CSS_DIR', THEME_URI . '/css');
	define('THEME_JS_DIR', THEME_URI . '/js');
	define('THEME_CSS_URI', THEME_URI . '/css');
	define('THEME_CSS_BODY_COLOR', '#7c7c7c');
	define('THEME_COLOR', '#008fd5');
	define('THEME_BGCOLOR', '#4db1e2');
	define('CWS_GRID_SHORTCODES', 'cws-row,col,cws-widget');


	if ( ! isset( $content_width ) ) {
		$content_width = 896;
	}
	load_theme_textdomain(THEME_SLUG, TEMPLATEPATH .'/language');

	require_once (TEMPLATEPATH . '/framework/rc/framework.php');
	require_once (TEMPLATEPATH . '/framework/config.php');

	require_once('core/TwitterOAuth.php');

	// CWS PB settings
	function cws_get_pb_options() {
	 return array(
	  'modules' => array('text', 'tabs', 'accs', 'tcol', 'callout', 'blog', 'portfolio', 'tweet', 'ourteam'),
		'callout' => array(
			'options' => array (
				'icon_selection' => true,
				)
		),
	 );
	}

	set_transient('update_themes', 24*3600);
	add_filter( 'pre_set_site_transient_update_themes', 'cws_check_for_update' );

	function cws_check_for_update($transient) {
		if (empty($transient->checked)) { return $transient; }

		$theme_pc = cws_get_option('_theme_purchase_code');
		if (empty($theme_pc)) {
			add_action( 'admin_notices', 'cws_an_purchase_code' );
		}

		$result = wp_remote_get('http://up.cwsthemes.com/products-updater.php?pc=' . $theme_pc . '&tname=' . THEME_SLUG);
		if (!is_wp_error( $result ) ) {
			if (200 == $result['response']['code'] && 0 != strlen($result['body']) ) {
				$resp = json_decode($result['body'], true);
				$theme = wp_get_theme();
				if ( version_compare( $theme->get('Version'), $resp['new_version'], '<' ) ) {
					$transient->response[THEME_SLUG] = $resp;
				}
			} else {
				unset($transient->response[THEME_SLUG]);
			}
		}
		return $transient;
	}

	function cws_an_purchase_code() {
		echo "<div class='update-nag'>".__('Clinico theme notice: Please insert your Item Purchase Code to get the latest theme updates!', THEME_SLUG) ."</div>";
	}

function cws_fix_shortcodes_autop($content){
    $array = array (
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );

    $content = strtr($content, $array);
    return $content;
}
add_filter('the_content', 'cws_fix_shortcodes_autop');

function cws_getTweets($username = false, $count = 20, $options = false) {
	if ('0' != cws_get_option('turn-twitter')) {
		$config = array(
			'consumer_key' => trim( cws_get_option('tw-api-key') ),
			'consumer_secret' => trim( cws_get_option('tw-api-secret') ),
			'oauth_token' => trim( cws_get_option('tw-access-token') ),
			'oauth_token_secret' => trim( cws_get_option('tw-access-token-secret') ),
			'output_format' => 'object'
		);

		// Instantiate TwitterOAuth class with set tokens
		$tw = new TwitterOAuth($config);
		$params = array(
			'screen_name' => $username,
			'count' => $count,
			'exclude_replies' => false
		);
		$res = $tw->get('statuses/user_timeline', $params);
	} else {
		$res = null;
	}
	return $res;
}

require_once(THEME_DIR . '/core/plugins.php');

function cws_on_switch_theme() {
}

add_action('after_switch_theme', 'cws_on_switch_theme');

function cws_register_widgets( $cws_widgets ) {
	foreach ($cws_widgets as $w) {
		require_once (THEME_DIR . '/core/widgets/' . strtolower($w) . '.php');
		register_widget($w);
	}
}

function cws_after_setup_theme() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'menus' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(' widgets ');

	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
	add_theme_support( 'post-formats', array( 'audio', 'gallery', 'image', 'link', 'quote', 'video', 'table' ) );

	register_nav_menu( 'header-menu', __( 'Navigation Menu', THEME_SLUG ) );
	register_nav_menu( 'sidebar-menu', __( 'SideBar Menu', THEME_SLUG ) );
	add_theme_support( 'woocommerce' );


	cws_register_widgets( array(
		'CWS_Text',
		'CWS_Portfolio',
		'CWS_Latest_Posts',
		'CWS_Social_Links',
		'CWS_Benefits',
		'CWS_Archives',
		'CWS_Twitter',
		'CWS_Doc_Search',
		) );

	// metaboxes
	require_once("core/bfi_thumb.php");
	include_once('core/sc-settings.php');
	include_once('core/page-metaboxes.php');
	include_once('core/post-metaboxes.php');
	include_once('core/breadcrumbs.php');
	include_once('core/shortcodes.php');
	include_once('core/staff.php');

	//add_filter( 'wp_edit_nav_menu_walker', function( $class, $menu_id ) { return 'CWS_Walker_Nav_Menu_Edit'; }, 10, 2);

	$user = wp_get_current_user();
	$user_nav_adv_options = get_user_option( 'managenav-menuscolumnshidden', get_current_user_id() );
	if ( is_array($user_nav_adv_options) ) {
		$css_key = array_search('css-classes', $user_nav_adv_options);
		if (false !== $css_key) {
			unset($user_nav_adv_options[$css_key]);
			update_user_option($user->ID, 'managenav-menuscolumnshidden', $user_nav_adv_options,	true);
		}
	}
}
add_action('after_setup_theme', 'cws_after_setup_theme');


function cws_print_fa_select($fa_selected = '', $all = false) {
	require_once(ReduxFramework::$_dir . '/inc/fields/select/fa-icons.php');
	$output = '<option value=""></option>';
	if ($all){
			$icons = get_all_fa_icons();
		}
		else{
			$icons = get_font_fa_icons();
		}
	foreach ($icons as $icon) {
		$selected = $icon == $fa_selected ? ' selected="selected"' : '';
		$output .= '<option value="' . $icon . '"' . $selected . '>' . $icon . '</option>';
	}
	return $output;
}

function cws_print_procedures_select($selected = '') {
	$sel = explode(",", $selected);
	$procedures_root = get_terms('cws-staff-procedures', 'hide_empty=0&parent=0');
	$output = '';
	if (count($procedures_root) ) {
		$output = '<option value=""></option>';
		foreach ($procedures_root as $proc) {
			$selected = in_array($proc->term_id, $sel) ? ' selected="selected"' : '';
			// or $proc['description'] ?
			$output .= '<option value="' . $proc->term_id . '"' . $selected . '>' . $proc->name . '</option>';
		}
	}
	return $output;
}

function cws_print_events_select($selected = '') {
	$sel = explode(",", $selected);
	$events_root = get_terms('tribe_events_cat', 'hide_empty=0&parent=0');
	if (!is_wp_error( $events_root ) ) {
		$output = '';
		if (count($events_root) ) {
			$output = '<option value=""></option>';
			foreach ($events_root as $event) {
				$selected = in_array($event->term_id, $sel) ? ' selected="selected"' : '';
				// or $event['description'] ?
				$output .= '<option value="' . $event->term_id . '"' . $selected . '>' . $event->name . '</option>';
			}
		}
	} else {
		$output = '<option disabled>Events Calendar plugin is not installed</option>';
	}
	return $output;
}

function cws_admin_init( $hook ) {
	// these are needed always, well, almost
	wp_register_style('fa-icon', THEME_URI . '/css/font-awesome.css' );
	wp_register_style('admin-css', THEME_URI . '/core/css/mb-post-styles.css' );
	wp_enqueue_script( 'select2-js', ReduxFramework::$_url . '/assets/js/vendor/select2/select2.js', array('jquery') );
	wp_enqueue_style( 'select2-css', ReduxFramework::$_url . '/assets/js/vendor/select2/select2.css', false, '2.0.0' );
	wp_enqueue_style('admin-css');

	if ( 'edit.php' != $hook && 'post.php' != $hook && 'post-new.php' != $hook && 'nav-menus.php' != $hook ) {
		return;
	}
	wp_enqueue_script( 'popup', THEME_URI . '/core/popup.js', array('jquery') );

	wp_enqueue_style( 'wp-color-picker');
	wp_enqueue_script( 'wp-color-picker');
	wp_enqueue_script( 'popup');
}
add_action('admin_enqueue_scripts', 'cws_admin_init');

function cws_theme_enqueue_scripts() {
	wp_enqueue_script( 'jquery' );
	$scripts = array (
						'modernizr' => 'modernizr.js',
						'owl_carousel' => 'owl.carousel.js',
					  'isotope' => 'isotope.pkgd.min.js',
					  'fancybox' => 'jquery.fancybox.js',
					  'main' => 'scripts.js',
					  'retina' => 'retina_1.3.0.js',
					  'img_loaded' => 'imagesloaded.pkgd.min.js' );
	if ( '0' == cws_get_option('modernizr') ) {
		unset($scripts['modernizr']);
	}
	foreach ($scripts as $alias => $src) {
		wp_register_script ($alias, THEME_JS_DIR . "/$src", "1.0", true);
		wp_enqueue_script ($alias);
	}
}

add_action('wp_enqueue_scripts', 'cws_theme_enqueue_scripts');

function cws_theme_enqueue_styles(){
	global $wp_styles;
	if((is_admin() && !is_shortcode_preview()) || 'wp-login.php' == basename($_SERVER['PHP_SELF'])){
		return;
	}

	$styles =
		array(
			'main' => 'main.css',
			'layout' => 'layout.css',
			'font-awesome' => 'font-awesome.css',
			'fancybox' => 'jquery.fancybox.css'
		);

	foreach($styles as $key=>$sc)
	{
		 wp_register_style( $key, THEME_URI . '/css/' . $sc);
		 wp_enqueue_style( $key );
	}
	$wp_styles->add_data( 'layout', 'rtl', true );

	$is_custom_color = cws_get_option('is-custom-color');
	if ($is_custom_color != '1') {
		$style = cws_get_option('stylesheet');
		if (!empty($style)){
			wp_register_style( 'style-color', THEME_URI . '/css/' . $style . '.css');
			wp_enqueue_style( 'style-color' );
			$wp_styles->add_data( 'style-color', 'rtl', true );
		}
	}
}
add_action('wp_enqueue_scripts', 'cws_theme_enqueue_styles');

function cws_widgets_init(){
	$sidebars = cws_get_option('sidebars');
	if (!empty($sidebars) && function_exists('register_sidebars')) {
		foreach ($sidebars as $sb) {
			if ($sb) {
				register_sidebar( array(
					'name' => $sb,
					'id' => strtolower(preg_replace("/[^a-z0-9\-]+/i", "_", $sb)),
					'before_widget' => '<div class="cws-widget"><div>',
					'after_widget' => '</div></div>',
					'before_title' => '<div class="widget-title"><span>',
					'after_title' => '</span></div>',
					));
			}
		}
	}
}

add_action('widgets_init', 'cws_widgets_init');

//add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 12;' ), 20 );

	$args = array(
		'default-color' => '616262',
/*		'default-repeat' => 'repeat',
		'default-image' => get_template_directory_uri() . '/images/patterns/pattern1.png',*/
	);
	add_theme_support( 'custom-background', $args );

function cws_layout_class ($classes=array()){
	$boxed_layout = cws_get_option('boxed-layout');
	if ( $boxed_layout=='0' ){
		array_push( $classes, 'wide' );
	}
	return $classes;
}
add_filter('body_class','cws_layout_class');

function cws_switch_theme($newname, $newtheme) {
	wp_deregister_script( 'popup' );
}
add_action("switch_theme", "cws_switch_theme", 10 , 2);

if ( function_exists( 'add_image_size' ) ) {
	add_image_size( 'team-thumb-admin', 100 );
	add_image_size( 'team-230', 230 );
}

function cws_print_font_css($font_array) {
	$out = '';
	foreach ($font_array as $style=>$v) {
		if ($style != 'font-options' && $style != 'google' && $style != 'subsets' && $style != 'font-backup') {
			$out .= !empty($v) ? $style .':'.$v.';' : '';
		}
	}
	return $out;
}

function cws_print_header_font (){
	ob_start();
	do_action( 'header_font_hook' );
	return ob_get_clean();
}

add_action( 'header_font_hook', 'cws_header_font_filter' );

function cws_header_font_filter (){
	$out = "";
	$font_array = cws_get_option('header-font');
	if (isset($font_array)) {
		$out .= '.widget-title,
				.widget-title a,
				.tribe-events-list-event-title,
				#tribe-geo-results .tribe-events-list-event-title,
				#tribe-geo-options .tribe-events-list-event-title,
				.tribe-events-single-section-title,
				.tribe-events-map-event-title
										{' . cws_print_font_css($font_array) . '}';
		$out .= '#tribe-events-content .tribe-events-single-section-title{' . cws_print_font_css($font_array) . '}';
		$out .= '.comment-reply-title{' . cws_print_font_css($font_array) . '}';
		$out .= '.cws-widget ul>li>a{color:' . $font_array["color"] . '}';
		$out .= '.cws-widget .post-list .time-post{color:' . $font_array["color"] . '}';
		$out .= 'a:hover{color:' . $font_array["color"] . '}';
		$out .= '.news .cats{color:' . $font_array["color"] . '}';
		$out .= '.news h3>a{color:' . $font_array["color"] . '}';
		$out .= '.comments-part .comment_author{color:' . $font_array["color"] . '}';
		$out .= '.tribe-events-list-event-title{' . cws_print_font_css($font_array) . '}';
		$out .= '.tribe-events-list-event-title a{color:' . $font_array["color"] . ';';
		$out .=	isset($font_array["line-height"]) ? 'line-height:' . $font_array["line-height"] . ';' : '';
		$out .=	'}';
		$out .= '.accordion_title,
				.tab,
				.tab_widget.type-vertical .tab.active,
				.callout_widget .text_part,
				.milestone_content, .services .col_title,
				.pricing_table_column:hover .content_part,
				.pricing_table_column.active_table_column .content_part,
				#title-404,#text-404 .sel,
				.archive_item a, #lang_sel li>a,
				#lang_sel_click ul ul a,
				#lang_sel_click ul ul a:visited,
				#lang_sel_list .lang_sel_sel:hover,
				.services .details a:hover,
				.toggle_widget.type-2 .accordion_section.active a:hover,
				.tab_widget.type-vertical .tabs .tab.active
								{color:' . $font_array["color"] . '}';
		$out .= '#tribe-mini-calendar-month,
				h2.tribe-events-page-title a:hover,
				h2.tribe-events-page-title a:focus
								{color:' . $font_array["color"] . '}';
	}
	echo $out;
}

function cws_print_body_font (){
	ob_start();
	do_action( 'body_font_hook' );
	return ob_get_clean();
}

add_action( 'body_font_hook', 'cws_body_font_filter' );

function cws_body_font_filter (){
	$out = "";
	$font_array = cws_get_option('body-font');
	if (isset($font_array)) {
		$font_color = $font_array['color'];
		$out .= 'body{'. cws_print_font_css($font_array) . '}';
		$out .= '.wpcf7-response-output, .wpcf7-form-control-wrap{line-height:' . ( isset($font_array["line-height"]) ? $font_array["line-height"] : "initial" ) . "}";
		$out .= '.tribe-events-event-cost{color:' . $font_array['color'] .  '}';
		$out .= '.cws_language_bar #lang_sel a>*,
				div.woocommerce td.actions input[type="text"]{font-size:' . $font_array['font-size'] . '}';
	}
	echo $out;
}

function cws_process_fonts() {
	global $wp_scripts;
	$link_out = '';
	$out = '<style type="text/css" id="custom-fonts-css">';
	$font_array = cws_get_option('menu-font');
	if (isset($font_array)) {
		$out .= '.main-menu .menu-item, .main-menu .menu-item a, .mobile_menu_header{'. cws_print_font_css($font_array) . '}';
	}
	$out .= cws_print_header_font();
	$out .= cws_print_body_font();
	$is_custom_color = cws_get_option('is-custom-color');
	if ($is_custom_color == '1') {
		$theme_c = cws_get_option('theme-custom-color');
		$theme_ch = cws_get_option('theme-custom-hover-color');
		$file = file_get_contents( THEME_DIR . '/css/color-n.css');
		$new_css = str_replace(THEME_COLOR . '#', $theme_c, $file);
		$new_css = str_replace('#' . cws_Hex2RGB(THEME_COLOR) . '#', cws_Hex2RGB($theme_c), $new_css);
		$new_css = str_replace('#' . cws_Hex2RGB(THEME_BGCOLOR) . '#', cws_Hex2RGB($theme_ch), $new_css);
		$out .= str_replace(THEME_BGCOLOR . '#', $theme_ch, $new_css);
	}

	$out .= '</style>';
	echo $out;
}

function cws_Hex2RGB($hex) {
		$hex = str_replace('#', '', $hex);
		$color = '';

		if(strlen($hex) == 3) {
			$color = hexdec(substr($hex, 0, 1)) . ',';
			$color .= hexdec(substr($hex, 1, 1)) . ',';
			$color .= hexdec(substr($hex, 2, 1));
		}
		else if(strlen($hex) == 6) {
			$color = hexdec(substr($hex, 0, 2)) . ',';
			$color .= hexdec(substr($hex, 2, 2)) . ',';
			$color .= hexdec(substr($hex, 4, 2));
		}
		return $color;
	}

function is_woo() {
	global $woocommerce;
	return !empty($woocommerce) ? is_woocommerce() || is_cart() || is_checkout() : false;
}

function cws_show_logo() {
	$logo = cws_get_option('logo');
	if (isset($logo["url"])) {
		$logo_hw = cws_get_option('logo-dimensions');
		$logo_m = cws_get_option('logo-margin');
		$bfi_args = array();
		if (is_array($logo_hw)){
			foreach ($logo_hw as $key => $value) {
				if ( !empty($value) ){
					$bfi_args[$key] = $value;
				}
			}
		}
		$logo_spacing = "";
		if (is_array($logo_m)){
			foreach ($logo_m as $key => $value) {
				$logo_spacing .= $key . ":" . $value . "px;";
			}
		}
		$logo_src = count($bfi_args)>0 ? bfi_thumb($logo["url"],$bfi_args) : $logo["url"];
		if ( !empty($logo["url"]) ){
			?>
				<a class="logo" href="<?php echo home_url(); ?>"><img src="<?php echo count($bfi_args)>0 ? bfi_thumb($logo['url'],$bfi_args) : $logo['url']; ?>" <?php echo "style=$logo_spacing"; ?> alt /></a>
			<?php
		}
	}
}

// Function that Rounds To The Nearest Value. Needed for the pagenavi() function
function round_num ($num, $to_nearest) {
	return floor($num/$to_nearest)*$to_nearest;
}

function cws_GetSbClasses( $p_id = null ) {
	if ($p_id){
		$post_type = get_post_type($p_id);
		if ( in_array( $post_type, array( "page", "portfolio", "staff" ) ) ){
			$cws_stored_meta = get_post_meta ($p_id, 'cws-mb');
			$sidebar1 = $sidebar2 = $sidebar_pos = $sb_block = '';
			$page_type = "page";
				if ( isset( $cws_stored_meta[0] ) ){
					$sidebar_pos = $cws_stored_meta[0]['cws-mb-sb_layout'];
					if ($sidebar_pos == 'default'){
						if(isset($cws_stored_meta[0]['cws-mb-sb_override'])){
							$page_type = "blog";
						}
						else if(is_front_page()){
							$page_type = "home";
						}

						$sidebar_pos = cws_get_option("def-" . $page_type . "-layout");
						$sidebar1 = cws_get_option("def-" . $page_type . "-sidebar1");
						$sidebar2= cws_get_option("def-" . $page_type . "-sidebar2");

					}
					else{
						$sidebar1 = $cws_stored_meta[0]['cws-mb-sidebar1'];
						$sidebar2 = $cws_stored_meta[0]['cws-mb-sidebar2'];
					}
				}
				else{
					$page_type = "page";
					$sidebar_pos = cws_get_option("def-" . $page_type . "-layout");
					$sidebar1 = cws_get_option("def-" . $page_type . "-sidebar1");
					$sidebar2= cws_get_option("def-" . $page_type . "-sidebar2");
				}
			}
		else if ( in_array( $post_type, array('post','staff','portfolio') ) ){
			$sidebar_pos = cws_get_option("def-blog-layout");
			$sidebar1 = cws_get_option("def-blog-sidebar1");
			$sidebar2 = cws_get_option("def-blog-sidebar2");
		}
	}
	else if (is_home()){ 										/* default home page hasn't ID */
		$sidebar_pos = cws_get_option("def-home-layout");
		$sidebar1 = cws_get_option("def-home-sidebar1");
		$sidebar2 = cws_get_option("def-home-sidebar2");
	}
	else if ( is_category() || is_archive() ){
		$sidebar_pos = cws_get_option("def-blog-layout");
		$sidebar1 = cws_get_option("def-blog-sidebar1");
		$sidebar2 = cws_get_option("def-blog-sidebar2");
	}

	$ret = array();
	$ret['sidebar_pos'] = isset( $sidebar_pos ) ? $sidebar_pos : "";
	$ret['sidebar1'] = isset( $sidebar1 ) ? $sidebar1 : "";
	$ret['sidebar2'] = isset( $sidebar2 ) ? $sidebar2 : "";
	return $ret;
}

function cws_get_option($name) {
	$theme_options = get_option(THEME_SLUG);
	return isset($theme_options[$name]) ? $theme_options[$name] : null;
}

function cws_wp_widget_thumbnail_html( $thumbnail_id = null, $instance ) {
	global $content_width, $_wp_additional_image_sizes;

	$upload_iframe_src = esc_url( get_upload_iframe_src('image') );
	$set_thumbnail_link = '<p class="hide-if-no-js"><a title="' .
		esc_attr__( 'Set featured image' ) .
		'" href="%s" id="set-post-thumbnail" class="thickbox">%s</a></p>';
	$content = sprintf( $set_thumbnail_link, $upload_iframe_src, esc_html__( 'Set featured image' ) );

	if ( $thumbnail_id ) {
		$old_content_width = $content_width;
		$content_width = 266;
		if ( !isset( $_wp_additional_image_sizes['post-thumbnail'] ) )
			$thumbnail_html = wp_get_attachment_image( $thumbnail_id, array( $content_width, $content_width ) );
		else
			$thumbnail_html = wp_get_attachment_image( $thumbnail_id, 'post-thumbnail' );
		if ( !empty( $thumbnail_html ) ) {
			$ajax_nonce = wp_create_nonce( 'set_post_thumbnail-' );
			$content = sprintf( $set_thumbnail_link, $upload_iframe_src, $thumbnail_html );
			$content .= '<p class="hide-if-no-js"><a href="#" id="remove-post-thumbnail" onclick="WPRemoveThumbnail(\'' . $ajax_nonce . '\');return false;">' . esc_html__( 'Remove featured image' ) . '</a></p>';
		}
		$content_width = $old_content_width;
	}
	return apply_filters( 'admin_post_thumbnail_html', $content );
}

	function cws_widget_icon_selection ($args){
		extract($args);
		ob_start(); ?>
		<section class="icon-options" <?php echo ((isset($show_icon_options)) && ($show_icon_options == "on")) ? "" : $display_none; ?>>
			<ul class="redux-image-select">
			<li class="redux-image-select <?php echo $title_select == 'fa' ? 'selected' : '' ?>">
				<input id="<?php echo $_this->get_field_id('fa'); ?>" name="<?php echo $_this->get_field_name('title_select'); ?>" type="radio" value="fa"  <?php echo $title_select == 'fa'  ? 'checked' : '' ?>><i class="fa fa-flag fa-2x"></i></li>
			<li class="redux-image-select <?php echo $title_select == 'img' ? 'selected' : '' ?>">
				<input id="<?php echo $_this->get_field_id('img'); ?>" name="<?php echo $_this->get_field_name('title_select'); ?>" type="radio" value="img" <?php echo $title_select == 'img' ? 'checked' : '' ?>><i class="fa fa-picture-o fa-2x"></i></li>
			</ul>
			<div class='image-part'>
				<div class="img-wrapper" <?php echo $title_select != 'fa' ? $display_none : '' ?>>
					<select class="icons" placeholder="<?php _e('Pick an icon for this widget', THEME_SLUG); ?>" data-placeholder="<?php _e('Pick an icon for this widget', THEME_SLUG); ?>" name="<?php echo $_this->get_field_name('title_fa'); ?>" id="<?php echo $_this->get_field_id('title_fa'); ?>">
						<?php
							echo cws_print_fa_select($title_fa, true);
						?>
					</select>
				</div>
				<div class="img-wrapper" <?php echo $title_select != 'img' ? $display_none : '' ?>>
					<p>
					<a id="media-<?php echo $_this->get_field_id('title_img'); ?>" <?php echo $title_img ? $display_none : ''; ?>><?php _e('Click here to select image', THEME_SLUG); ?></a>
					<a id="remov-<?php echo $_this->get_field_id('title_img'); ?>" <?php echo !$title_img ? $display_none : ''; ?>><?php _e('Remove this image', THEME_SLUG); ?></a>
					<input class="image" style="visibility:hidden;" readonly id="<?php echo $_this->get_field_id('title_img'); ?>" name="<?php echo $_this->get_field_name('title_img'); ?>" type="text" value="<?php echo esc_attr($title_img); ?>" />
					<img id="img-<?php echo $_this->get_field_id('title_img'); ?>" src<?php echo $thumb_url; ?> alt />
					</p><p>
					<label for="<?php echo $_this->get_field_id('img_width'); ?>"><?php _e('Image width:', THEME_SLUG); ?></label>
					<input id="<?php echo $_this->get_field_id('img_width') ?>" name="<?php echo $_this->get_field_name('img_width'); ?>" type="text" placeholder="px" value="<?php echo !empty($img_width) ? $img_width : ''; ?>" style="width: 50px;" />
					</p>
				</div>
			</div>
			<div>
				<a class="reset_icon_options"><?php _e("Reset icon options", THEME_SLUG); ?></a>
			</div>
		</section>
		<?php ob_end_flush();
	}

	function cws_widget_icon_rendering ($args){
	extract(shortcode_atts(
		array(
			'title_select' => null,
			'title_fa' => null,
			'title_img' => null,
			'img_width' => '65'
		), $args));
		if (($title_select == 'fa') && (!empty($title_fa))) {
			echo '<div class="widget-icon icon"><i class="fa fa-' . $title_fa .' fa-3x"><span class="triangle"></span></i></div>';
		} else if (($title_select == 'img') && (!empty($title_img))) {
			echo '<div class="widget-icon pic"><img src="' . bfi_thumb( wp_get_attachment_url( $title_img ), array( 'width' => $img_width ) ) . '" alt /></div>';
		}
	}

	function cws_output_media_part ($blogtype, $pinterest_layout, $sb_block, $post = null){
		$pid = $post ? $post->ID : get_the_id();
		$post_format = get_post_format( $pid );
		$media_meta = get_post_meta( $pid, 'cws-mb' );
		$media_meta = isset($media_meta[0]) ? $media_meta[0] : null;
		$thumbnail = has_post_thumbnail( $pid ) ? wp_get_attachment_image_src(get_post_thumbnail_id( $pid ),'full') : null;
		$thumbnail = $thumbnail ? $thumbnail[0] : null;
		$single = ( isset($post) && $post_format != 'gallery' ) ? true : false;
		$thumbnail_dims = cws_get_post_tmumbnail_dims($blogtype, $pinterest_layout, $sb_block, $single);


		$some_media = false;
		ob_start();
		?>
			<div class="wrapper">
				<?php
					switch ($post_format) {
						case 'link':
							$link = $media_meta["cws-mb-link"];
							if ($thumbnail){
								?>
								<div class="pic">
									<img src="<?php echo bfi_thumb($thumbnail,$thumbnail_dims); $some_media = true; ?>" alt />
									<div class="hover-effect"></div>
									<?php echo $link ? "<div class='links'><a href='$link' class='fa fa-link' title='$link'></a></div>" : "<div class='links'><a href='$thumbnail' class='fancy fa fa-eye'></a></div>"; ?>
								</div>
								<?php
							}
							else{
								echo $link ? "<div class='link_url'>$link</div>" : "";
							}
							$some_media = true;
							break;
						case 'video':
							if ( $media_meta['cws-mb-video'] ){
								echo "<div class='video'>" . apply_filters('the_content',"[embed width='" . $thumbnail_dims['width'] . "']" . $media_meta['cws-mb-video'] . "[/embed]") . "</div>";
								$some_media = true;
							}
							break;
						case 'audio':
							if ( $media_meta['cws-mb-audio'] ){
								echo "<div class='audio'>" . apply_filters('the_content','[audio mp3="' . $media_meta['cws-mb-audio'] . '"]') . "</div>";
								$some_media = true;
							}
							break;
						case 'quote':
							if ($media_meta["cws-mb-quote"]){
								$text = $media_meta["cws-mb-quote"];
								$author = $media_meta["cws-mb-quote-author"];
								echo cws_testimonial_renderer( $thumbnail, $text, $author );
								$some_media = true;
							}
							break;
						case 'gallery':
							if ($media_meta["cws-mb-gallery"]){
								$gallery = $media_meta["cws-mb-gallery"];
								$match = preg_match_all("/\d+/",$gallery,$images);
								if ($match){
									$images = $images[0];
									$image_srcs = array();
									foreach ( $images as $image ){
										$image_src = wp_get_attachment_image_src($image,'full');
										$image_url = $image_src[0];
										array_push( $image_srcs, $image_url );

									}
									$some_media = count( $image_srcs ) > 0 ? true : false;
									$carousel = count($image_srcs) > 1 ? true : false;
									$gallery_id = uniqid( 'cws-gallery-' );
									echo  $carousel ? "<div class='gallery_carousel_nav'>
														<i class='prev fa fa-angle-left'></i>
														<i class='next fa fa-angle-right'></i>
														<div class='clearfix'></div></div>
														<div class='gallery_post_carousel'>" : "";
									foreach ( $image_srcs as $image_src ){
										?>
										<div class='pic'>
											<img src="<?php echo bfi_thumb($image_src,$thumbnail_dims); ?>" alt />
											<div class="hover-effect"></div>
											<div class="links">
												<a href="<?php echo $image_src; ?>" <?php echo $carousel ? " data-fancybox-group='$gallery_id'" : ""; ?> class="<?php echo $carousel ? 'fancy fancy_gallery fa fa-photo' : 'fancy fa fa-eye'; ?>" <?php echo $carousel ? "data-thumbnail='" . bfi_thumb( $image_src, array( 'width' => 50, 'height' => 50, 'crop' => true ) ) . "'" : ""; ?>></a>
											</div>
										</div>
										<?php
									}
									echo  $carousel ? "</div>" : "";
								}
							}
							break;
						default:
							if ( $thumbnail ){
								$image_data = wp_get_attachment_metadata( get_post_thumbnail_id( $pid ) );
								$post_img = (($image_data['width'] < $thumbnail_dims['width'] ) && is_single()) ? $thumbnail : null;
								echo "<div class='pic'><img src='". ($post_img ? $post_img : bfi_thumb($thumbnail,$thumbnail_dims)) ."' alt />". ($post_img ? "</div>" : "<div class='hover-effect'></div><div class='links'><a class='fancy fa fa-eye' href='$thumbnail'></a></div></div>");
								$some_media = true;
								}
							break;
					}
				?>
			</div>
		<?php
		$some_media ? ob_end_flush() : ob_end_clean();
	}

	function cws_get_post_tmumbnail_dims ($blogtype, $pinterest_layout, $sb_block, $single = false){
		$dims = array('width'=>0,'height'=>0);
		if ($single){
			if ($sb_block == 'none'){
				$dims['width'] = 1170;
			}
			else if (in_array($sb_block, array('left','right'))){
				$dims['width'] = 870;
			}
			else if ($sb_block == 'both'){
				$dims['width'] = 570;
			}
		}
		else{
			switch ($blogtype){
				case "large":
					if ($sb_block == 'none'){
						$dims['width'] = 1170;
						$dims['height'] = 659;
					}
					else if (in_array($sb_block, array('left','right'))){
						$dims['width'] = 870;
						$dims['height'] =  490;
					}
					else if ($sb_block == 'both'){
						$dims['width'] = 570;
						$dims['height'] =  321;
					}
					break;
				case "medium":
					$dims['width'] = 570;
					$dims['height'] = 321;
					break;
				case "small":
					$dims['width'] = 270;
					$dims['height'] = 152;
					break;
				case "pinterest":
					switch ($pinterest_layout){
						case '2':
							if ($sb_block == 'none'){
								$dims['width'] = 570;
								$dims['height'] = 321;
							}
							else if (in_array($sb_block, array('left','right'))){
								$dims['width'] = 420;
								$dims['height'] =  237;
							}
							else if ($sb_block == 'both'){
								$dims['width'] = 270;
								$dims['height'] =  152;
							}
							break;
						case '3':
							if ($sb_block == 'none'){
								$dims['width'] = 370;
								$dims['height'] = 208;
							}
							else if (in_array($sb_block, array('left','right'))){
								$dims['width'] = 270;
								$dims['height'] =  152;
							}
							/*****************************/
							else if ($sb_block == 'both'){
								$dims['width'] = 270;
								$dims['height'] =  152;
							}
							break;
						case '4':
							$dims['width'] = 270;
							$dims['height'] = 152;
							break;
					}
					break;
			}
		}
		return $dims;
	}

	function cws_has_sidebar_pos ($sb_block){
		return ( (!empty($sb_block)) && (in_array($sb_block,array('left','right','both'))) ) ? true : false;
	}

	function cws_blog_output ($r, $total_post_count, $posts_per_page, $blogtype, $pinterest_layout, $sb_block, $paged){
		if ($r->have_posts()):
			ob_start();
			while($r->have_posts()):
				$r->the_post();
				echo "<div class='item'>";
					cws_post_output($sb_block, $blogtype, $pinterest_layout);
					cws_page_links();
				echo "</div>";
			endwhile;
			ob_end_flush();
		endif;
	}

	function cws_load_more ($selector,$paged,$max_paged,$post_id){
		?>
		<span class="load_more" data-selector="<?php echo $selector; ?>" data-paged="<?php echo $paged; ?>" data-max-paged="<?php echo $max_paged; ?>" data-post-id="<?php echo $post_id; ?>" data-template-directory="<?php echo THEME_URI; ?>"><i class="fa fa-refresh"></i><?php _e("Load More",THEME_SLUG) ?></span>
		<?php
	}


	function cws_pagination ($paged=1,$max_paged=1){
		$pagenum_link = html_entity_decode( get_pagenum_link() );
		$query_args   = array();
		$url_parts	= explode( '?', $pagenum_link );

		if ( isset( $url_parts[1] ) ) {
			wp_parse_str( $url_parts[1], $query_args );
		}

		$pagenum_link = esc_url( remove_query_arg( array_keys( $query_args ), $pagenum_link ) );
		$pagenum_link = $GLOBALS['wp_rewrite']->using_permalinks() ? trailingslashit( $pagenum_link ) . '%_%' : trailingslashit( $pagenum_link ) . '?%_%';
		$pagenum_link = esc_url( add_query_arg( $query_args, $pagenum_link ) );

		$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
		$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : 'paged=%#%';
		$pagination_args = array( 'base' => $pagenum_link,
									'format' => $format,
									'current' => $paged,
									'total' => $max_paged,
									"prev_text" => __("Previous",THEME_SLUG),
									"next_text" => __("Next",THEME_SLUG),
									"mid_size" => 2,
									);
		$pagination = paginate_links($pagination_args);
		echo !empty($pagination) ? "<div class='pagination'>" . $pagination . "</div>" : "";
		?>
		<?php
	}

	function cws_page_links(){
		$args = array(
		 'before'		   => ''
		,'after'			=> ''
		,'link_before'	  => '<span>'
		,'link_after'	   => '</span>'
		,'next_or_number'   => 'number'
		,'nextpagelink'	 =>  __("Next Page",THEME_SLUG)
		,'previouspagelink' => __("Previous Page",THEME_SLUG)
		,'pagelink'		 => '%'
		,'echo'			 => 1 );
		ob_start();
		wp_link_pages( $args );
		$page_links = ob_get_clean();
		echo !empty($page_links) ? "<div class='pagination page_links'>" . $page_links . "</div>" : "";
	}

	function cws_post_output ($sb_block, $blogtype="large", $pinterest_layout="2", $post = null){
		$pid = $post ? $post->ID : get_the_id();
		if ( get_the_title() && get_post_format() != "aside" && !is_single() ){
			?>
				<div class="widget-title">
				<?php echo ( !isset($post) ? "<a href='" . get_permalink($pid) . "'>" : "" ) . get_the_title() . ( !isset($post) ? "</a>" : "" ); ?>
				</div>
			<?php
		}

		if ( get_post_type($pid) == 'post'):
			?>
			<div class="date clearfix">
				<?php if (get_comments_number() > 0 ) : ?>
				<i class="fa fa-comment">
					<a href="<?php comments_link(); ?>">
					<span><?php echo comments_number('0','1','%') ?></span>
					</a>
				</i>
				<?php endif; ?>
				<?php the_time(get_option('date_format')); ?>
			</div>
			<?php
		endif;
		cws_output_media_part($blogtype, $pinterest_layout, $sb_block, $post);
		$content = "";
		if (null != $post) {
			$content .= apply_filters('the_content', get_the_content($post->ID));
		} else {
			$chars_count = cws_get_content_chars_count( $blogtype, $pinterest_layout );
			$content .= cws_post_content_output( $chars_count, $blogtype );
		}
		echo $content;
		if ( get_post_type($pid) == 'post' ):
			echo "<div class='cats'>" . __("Posted", THEME_SLUG);
			$categories = get_the_category($pid);
			$show_author = cws_get_option("blog_author");
			$tags = wp_get_post_tags($pid);
			if ( !empty($categories) || $show_author || !empty($tags) ){
				if ( !empty($categories) ){
					echo " " . __("in", THEME_SLUG) . " ";
					for($i=0; $i<count($categories); $i++) {
						echo "<a href='" . get_category_link($categories[$i]->cat_ID) . "'>" . $categories[$i]->name . "</a>";
						echo $i<count($categories)-1 ? ", " : "";
					}
				}
				if ( $show_author ){
					echo " " . __("by", THEME_SLUG) . " ";
					$author = get_the_author();
					echo !empty($author) ? $author : "";
				}
				if ( !empty($tags) ){
					//echo "";
					echo get_the_tag_list(' | Tags: ', ', ');
					/*for ($i=0; $i<count($tags) ;$i++) {
						echo "<a href='" . get_tag_link($tags[$i]->term_id) . "'>" . $tags[$i]->name . "</a>";
						echo $i<count($tags)-1 ? ", " : "";
					}*/
				}
			}
			echo ( ($blogtype != 'pinterest') && (!is_single()) ) ? "<a href='" . get_permalink($pid) . "' class='more fa fa-long-arrow-right'></a>" : "";
			echo "</div>";
		endif;
	}

	function cws_custom_excerpt_length( $length ) {
		return 1400;
	}
	add_filter( 'excerpt_length', 'cws_custom_excerpt_length', 999 );

	function cws_closetags ( $html ) {
		#put all opened tags into an array
		preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
		$openedtags = $result[1];
		#put all closed tags into an array
		preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
		$closedtags = $result[1];
		$not_closing_tags = array( "area", "base", "br", "col", "command", "embed", "hr", "img", "input", "link", "meta", "param", "source" );
		$reuqired_children = array( "dl" => array( "dt", "dd" ) );
		$len_opened = count ( $openedtags );
		# all tags are closed
		if( count ( $closedtags ) == $len_opened )
		{
		return $html;
		}
		$openedtags = array_reverse ( $openedtags );
		$matches = array();
		$match = preg_match( "#((<([^>]+))|(</([^>]+))|<|(</))$#", $html,  $matches );
		if ( $match && count($matches) && (!empty($matches[0])) ){
			$html = substr_replace( $html, "", -1*strlen($matches[0]), strlen($matches[0]) );
		}
		# close tags
		for( $i = 0; $i < $len_opened; $i++ )
		{
			if ( !in_array ( $openedtags[$i], $closedtags ) ){
				if ( !in_array( $openedtags[$i], $not_closing_tags ) ){
					if ( array_key_exists($openedtags[$i], $reuqired_children)  ){
						$req_children = $reuqired_children[$openedtags[$i]];
						foreach ( $req_children as $req_child ) {
							if ( !in_array( $req_child, $openedtags ) ){
								$html .= in_array( $req_child, $not_closing_tags ) ? "<" . $req_child . " />" : "<" . $req_child . ">" . "</" . $req_child . ">";
							}
						}
					}
					$html .= "</" . $openedtags[$i] . ">";
				}
			}
			else
			{
			unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
			}
		}
		return $html;
	}

	function cws_post_content_output ($chars_count=0, $blogtype=""){
		$post_type = get_post_type();
		$content = "";
		global $more;
		$more = 0;
		if ($post_type == "post"){
			global $post;
			if ( $blogtype == "pinterest" ){
				$content =  !empty( $post->post_excerpt ) ? get_the_excerpt() :  substr( preg_replace( "|\s{2,}|", " ", strip_tags( strip_shortcodes( get_the_content("") ) ) ), 0, $chars_count );
			}
			else{
				$content =  !empty( $post->post_excerpt ) ? get_the_excerpt() :  cws_closetags( substr( preg_replace( "|\s{2,}|", " ", strip_shortcodes( get_the_content("") ) ), 0, $chars_count ) );
			}
		}
		else if ($post_type == "portfolio"){
			$content = preg_replace( "|\s{2,}|", " ", strip_tags(strip_shortcodes(get_the_content(""))) );
			$content = substr( $content, 0, $chars_count );
		}
		else{
			$content = preg_replace( "|\s{2,}|", " ", strip_tags(strip_shortcodes(get_the_content(""))) );
			$content = substr( $content, 0, $chars_count );
		}
		$more = 1;
		$full_content = preg_replace( "|\s{2,}|", " ", strip_tags(strip_shortcodes(get_the_content(""))) );
		$more_link = strlen( $full_content ) > strlen( $content ) ? true : false;
		$content .= ( $more_link && $post_type != "portfolio" ) ? " <a href='" . get_the_permalink() . "' class='more'></a>" : "";
		$content = apply_filters( 'the_content', $content );
		return $content;
	}

	function cws_get_content_chars_count ( $blogtype, $cols ){
		$number = 155;
		if ($blogtype == 'pinterest'){
			switch ( $cols ){
				case '2':
					$number = 160;
					break;
				case '3':
					$number = 90;
					break;
				case '4':
					$number = 60;
					break;
			}
		}
		else if ( $blogtype == 'medium' ){
			$number = 1400;
		}
		else if ( $blogtype == 'small' ){
			$number = 1100;
		}
		else{
			$number = 350;
		}
		return $number;
	}

	function cws_clinico_comment_callback ($comment, $args, $depth) {
		ob_start();
		$avatar = get_avatar($comment->user_id,(int)$args["avatar_size"]);
		$avatar_default = preg_match("/avatar-default/", $avatar) ? true : false;
		echo "<" . $args['style'] . " id='comment-" . $comment->comment_ID . "' class='comment_item clearfix'>";
			echo "<div class='comment_wrapper clearfix'>";
				if ($avatar_default){
					echo "<div class='avatar_frame icon_frame'>
							<i class='fa fa-user'></i>
							</div>";
				}
				else{
					echo "<div class='avatar_frame'>" . $avatar . "</div>";
				}
				echo "<div class='comment_data'>";
					echo $comment->comment_author ? "<span class='comment_author'>" . $comment->comment_author . "</span>" : "";
					echo ( ( date('Y') == date('Y',strtotime($comment->comment_date)) ) ? date('Y ') : "" ) . date('F j', strtotime($comment->comment_date));
					echo comment_reply_link(array_merge( $args, array("reply_text" => __("Reply", THEME_SLUG) . "<i class='fa fa-angle-double-right'></i>", "depth" => $depth, "max_depth" => $args['max_depth'])));
				echo "</div>";
				echo "<div class='comment'>";
					echo $comment->comment_content;
			echo "</div>";
		echo "</" . $args['style'] . ">";
		ob_end_flush();
	}

	/* CALENDAR */

	function cws_replace_tribe_events_calendar_stylesheet() {
	   $styleUrl = get_template_directory_uri() . '/tribe-events/tribe-events.css';
	   return $styleUrl;
	}
	add_filter('tribe_events_stylesheet_url', 'cws_replace_tribe_events_calendar_stylesheet');

	function cws_replace_tribe_events_calendar_pro_stylesheet() {
	   $styleUrl = get_template_directory_uri() . '/tribe-events/pro/tribe-events-pro.css';
	   return $styleUrl;
	}
	add_filter('tribe_events_pro_stylesheet_url', 'cws_replace_tribe_events_calendar_pro_stylesheet');

	function cws_replace_tribe_events_widget_calendar_stylesheet() {
	   $styleUrl = get_template_directory_uri() . '/tribe-events/pro/widget-calendar.css';
	   return $styleUrl;
	}
	add_filter('tribe_events_pro_widget_calendar_stylesheet_url', 'cws_replace_tribe_events_widget_calendar_stylesheet');

	add_filter( 'tribe_events_kill_responsive', '__return_true');
	//add_filter( 'tribe_events_pro_kill_responsive', '__return_true');


/****************** WALKER *********************/

class Cws_Walker_Nav_Menu extends Walker {
	private $elements;
	private $elements_counter = 0;

	function walk ($items, $depth) {
		$this->elements = $this->get_number_of_root_elements($items);
		return parent::walk($items, $depth);
	}

	/**
	 * @see Walker::$tree_type
	 * @since 3.0.0
	 * @var string
	 */
	var $tree_type = array( 'post_type', 'taxonomy', 'custom' );

	/**
	 * @see Walker::$db_fields
	 * @since 3.0.0
	 * @todo Decouple this.
	 * @var array
	 */
	var $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	/**
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<span class='button_open'></span><ul class=\"sub-menu\">\n";
	}
	/**
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}
	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$before_item = "<span class='depth'>";
		for ( $i=0; $i<$depth; $i++ ){
			$before_item .= "<span class='level'>- </span>";
		}
		$before_item .= "</span>";
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		if ($item->menu_item_parent=="0"){
			$this->elements_counter += 1;
			if ($this->elements_counter>$this->elements/2){
				array_push($classes,'right');
			}
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )	 ? $item->target	 : '';
		$atts['rel']	= ! empty( $item->xfn )		? $item->xfn		: '';
		$atts['href']   = ! empty( $item->url )		? $item->url		: '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = !empty($args->before) ? $args->before : "";
		$item_output .= '<a'. $attributes .'>' . $before_item;

		$item_output .= ( !empty($args->link_before) ? $args->link_before : "" ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( !empty($args->link_after ) ? $args->link_after : "" );
		$item_output .= '</a>';
		$item_output .= ( !empty($args->after) ? $args->after : "" );

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * @see Walker::end_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 */
	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

function cws_testimonial_renderer($thumbnail,$text,$author){
	ob_start();
	?>
	<div class="testimonial <?php echo $thumbnail ? '' : 'testimonial-alt' ?>">
		<div <?php echo $thumbnail ? "class='clearfix'" : ""; ?>>
			<?php echo $thumbnail ? "<img src='". bfi_thumb($thumbnail,array("width"=>100)) ."' alt />" : ""; ?>
			<p>
				<?php echo $text; ?>
			</p>
		</div>
		<div class="author">
			<?php echo $author ? $author : ""; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

function cws_get_grid_shortcodes (){
	return explode( ",", CWS_GRID_SHORTCODES );
}

function strip_cws_grid_shortcodes ($text){
	$shortcodes = cws_get_grid_shortcodes ();
	$find = array();
	foreach ( $shortcodes as $shortcode ){
		$shortcode = preg_replace( "|-|", "\-", $shortcode );
		$op_tag = "|\[.*" . $shortcode . ".*\]|";
		$cl_tag = "|\[/.*" . $shortcode . ".*\]|";
		array_push( $find, $op_tag, $cl_tag );
	}
	$text = preg_replace( $find, "", $text );
	return $text;
}

// Check if WooCommerce is active
if (in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
	require_once ( THEME_DIR . '/woocommerce/wooinit.php' ); //WooCommerce Shop ini file
};

// Check if WPML is active
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active('sitepress-multilingual-cms/sitepress.php') ){
	define('CWS_WPML_ACTIVE', true);
	define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
	$GLOBALS["wpml_settings"] = get_option("icl_sitepress_settings");
	global $icl_language_switcher;
}

function cws_is_wpml_active (){
	return defined("CWS_WPML_ACTIVE") && CWS_WPML_ACTIVE;
}

function cws_show_flags_in_footer (){
	return isset( $GLOBALS["wpml_settings"]["icl_lang_sel_footer"] ) ? $GLOBALS["wpml_settings"]["icl_lang_sel_footer"] : false;
}

function cws_put_ganalytics_code() {
	$gac = cws_get_option('ga-code');
	if (!empty($gac)) {
	echo '<script type="text/javascript">' . $gac . '</script>';
	}
	$gat = cws_get_option('ga-event-tracking');
	if (!empty($gat)) {
		echo '<script type="text/javascript">' . $gat . '</script>';
	}
}

add_action( 'wp_enqueue_scripts', 'load_my_child_styles', 20 );
function load_my_child_styles() {
	wp_register_style( 'child-theme-styles', get_stylesheet_uri() );
	wp_enqueue_style( 'child-theme-styles' );
}

// Array of Elusive Icons
// Contributed by @WhatJustHappened
// Last updated: 23 March 2015
function get_font_fa_icons() {
	$faIcons = array(
'twitter',
'facebook',
'pinterest',
'pinterest-square',
'google-plus-square',
'google-plus',
'linkedin',
'plus-square',
'mobile-phone',
'mobile',
'mail-reply',
'youtube-square',
'youtube',
'xing',
'xing-square',
'youtube-play',
'dropbox',
'stack-overflow',
'instagram',
'flickr',
'dribbble',
'skype',
'vk',
'foursquare',
'trello',
'stack-exchange',
'vimeo-square',
'plus-square-o',
'adn',
'bitbucket',
'bitbucket-square',
'tumblr',
'tumblr-square',
'github-alt',
'github',
'money',
'phone-square',
'unlock',
'credit-card',
'rss',
'hdd-o',
'bullhorn',
'bell',
'certificate',
'legal',
'dashboard',
'flash',
'bolt',
'sitemap',
'umbrella',
'cloud-download',
'cloud-upload',
'desktop',
'laptop',
'tablet',
'folder-o',
'folder-open-o',
'gamepad',
'keyboard-o',
'calendar-o',
'html5',
'css3',
'rss-square',
'external-link-square',
'share-square',
'compass',
'apple',
'windows',
'android',
'linux',
'female',
'male',
'gittip',
'sun-o',
'moon-o',
'archive',
'bug',
'weibo',
'renren',
'pagelines',
	);
	return $faIcons;
}

function get_all_fa_icons (){
	$faIcons = array(
"glass",
"music",
"search",
"envelope-o",
"heart",
"star",
"star-o",
"user",
"film",
"th-large",
"th",
"th-list",
"check",
"times",
"search-plus",
"search-minus",
"power-off",
"signal",
"cog",
"trash-o",
"home",
"file-o",
"clock-o",
"road",
"download",
"arrow-circle-o-down",
"arrow-circle-o-up",
"inbox",
"play-circle-o",
"repeat",
"refresh",
"list-alt",
"lock",
"flag",
"headphones",
"volume-off",
"volume-down",
"volume-up",
"qrcode",
"barcode",
"tag",
"tags",
"book",
"bookmark",
"print",
"camera",
"font",
"bold",
"italic",
"text-height",
"text-width",
"align-left",
"align-center",
"align-right",
"align-justify",
"list",
"outdent",
"indent",
"video-camera",
"picture-o",
"pencil",
"map-marker",
"adjust",
"tint",
"pencil-square-o",
"share-square-o",
"check-square-o",
"arrows",
"step-backward",
"fast-backward",
"backward",
"play",
"pause",
"stop",
"forward",
"fast-forward",
"step-forward",
"eject",
"chevron-left",
"chevron-right",
"plus-circle",
"minus-circle",
"times-circle",
"check-circle",
"question-circle",
"info-circle",
"crosshairs",
"times-circle-o",
"check-circle-o",
"ban",
"arrow-left",
"arrow-right",
"arrow-up",
"arrow-down",
"share",
"expand",
"compress",
"plus",
"minus",
"asterisk",
"exclamation-circle",
"gift",
"leaf",
"fire",
"eye",
"eye-slash",
"exclamation-triangle",
"plane",
"calendar",
"random",
"comment",
"magnet",
"chevron-up",
"chevron-down",
"retweet",
"shopping-cart",
"folder",
"folder-open",
"arrows-v",
"arrows-h",
"bar-chart",
"twitter-square",
"facebook-square",
"camera-retro",
"key",
"cogs",
"comments",
"thumbs-o-up",
"thumbs-o-down",
"star-half",
"heart-o",
"sign-out",
"linkedin-square",
"thumb-tack",
"external-link",
"sign-in",
"trophy",
"github-square",
"upload",
"lemon-o",
"phone",
"square-o",
"bookmark-o",
"phone-square",
"twitter",
"facebook",
"github",
"unlock",
"credit-card",
"rss",
"hdd-o",
"bullhorn",
"bell",
"certificate",
"hand-o-right",
"hand-o-left",
"hand-o-up",
"hand-o-down",
"arrow-circle-left",
"arrow-circle-right",
"arrow-circle-up",
"arrow-circle-down",
"globe",
"wrench",
"tasks",
"filter",
"briefcase",
"arrows-alt",
"users",
"link",
"cloud",
"flask",
"scissors",
"files-o",
"paperclip",
"floppy-o",
"square",
"bars",
"list-ul",
"list-ol",
"strikethrough",
"underline",
"table",
"magic",
"truck",
"pinterest",
"pinterest-square",
"google-plus-square",
"google-plus",
"money",
"caret-down",
"caret-up",
"caret-left",
"caret-right",
"columns",
"sort",
"sort-desc",
"sort-asc",
"envelope",
"linkedin",
"undo",
"gavel",
"tachometer",
"comment-o",
"comments-o",
"bolt",
"sitemap",
"umbrella",
"clipboard",
"lightbulb-o",
"exchange",
"cloud-download",
"cloud-upload",
"user-md",
"stethoscope",
"suitcase",
"bell-o",
"coffee",
"cutlery",
"file-text-o",
"building-o",
"hospital-o",
"ambulance",
"medkit",
"fighter-jet",
"beer",
"h-square",
"plus-square",
"angle-double-left",
"angle-double-right",
"angle-double-up",
"angle-double-down",
"angle-left",
"angle-right",
"angle-up",
"angle-down",
"desktop",
"laptop",
"tablet",
"mobile",
"circle-o",
"quote-left",
"quote-right",
"spinner",
"circle",
"reply",
"github-alt",
"folder-o",
"folder-open-o",
"smile-o",
"frown-o",
"meh-o",
"gamepad",
"keyboard-o",
"flag-o",
"flag-checkered",
"terminal",
"code",
"reply-all",
"star-half-o",
"location-arrow",
"crop",
"code-fork",
"chain-broken",
"question",
"info",
"exclamation",
"superscript",
"subscript",
"eraser",
"puzzle-piece",
"microphone",
"microphone-slash",
"shield",
"calendar-o",
"fire-extinguisher",
"rocket",
"maxcdn",
"chevron-circle-left",
"chevron-circle-right",
"chevron-circle-up",
"chevron-circle-down",
"html5",
"css3",
"anchor",
"unlock-alt",
"bullseye",
"ellipsis-h",
"ellipsis-v",
"rss-square",
"play-circle",
"ticket",
"minus-square",
"minus-square-o",
"level-up",
"level-down",
"check-square",
"pencil-square",
"external-link-square",
"share-square",
"compass",
"caret-square-o-down",
"caret-square-o-up",
"caret-square-o-right",
"eur",
"gbp",
"usd",
"inr",
"jpy",
"rub",
"krw",
"btc",
"file",
"file-text",
"sort-alpha-asc",
"sort-alpha-desc",
"sort-amount-asc",
"sort-amount-desc",
"sort-numeric-asc",
"sort-numeric-desc",
"thumbs-up",
"thumbs-down",
"youtube-square",
"youtube",
"xing",
"xing-square",
"youtube-play",
"dropbox",
"stack-overflow",
"instagram",
"flickr",
"adn",
"bitbucket",
"bitbucket-square",
"tumblr",
"tumblr-square",
"long-arrow-down",
"long-arrow-up",
"long-arrow-left",
"long-arrow-right",
"apple",
"windows",
"android",
"linux",
"dribbble",
"skype",
"foursquare",
"trello",
"female",
"male",
"gratipay",
"sun-o",
"moon-o",
"archive",
"bug",
"vk",
"weibo",
"renren",
"pagelines",
"stack-exchange",
"arrow-circle-o-right",
"arrow-circle-o-left",
"caret-square-o-left",
"dot-circle-o",
"wheelchair",
"vimeo-square",
"try",
"plus-square-o",
"space-shuttle",
"slack",
"envelope-square",
"wordpress",
"openid",
"university",
"graduation-cap",
"yahoo",
"google",
"reddit",
"reddit-square",
"stumbleupon-circle",
"stumbleupon",
"delicious",
"digg",
"pied-piper",
"pied-piper-alt",
"drupal",
"joomla",
"language",
"fax",
"building",
"child",
"paw",
"spoon",
"cube",
"cubes",
"behance",
"behance-square",
"steam",
"steam-square",
"recycle",
"car",
"taxi",
"tree",
"spotify",
"deviantart",
"soundcloud",
"database",
"file-pdf-o",
"file-word-o",
"file-excel-o",
"file-powerpoint-o",
"file-image-o",
"file-archive-o",
"file-audio-o",
"file-video-o",
"file-code-o",
"vine",
"codepen",
"jsfiddle",
"life-ring",
"circle-o-notch",
"rebel",
"empire",
"git-square",
"git",
"hacker-news",
"tencent-weibo",
"qq",
"weixin",
"paper-plane",
"paper-plane-o",
"history",
"circle-thin",
"header",
"paragraph",
"sliders",
"share-alt",
"share-alt-square",
"bomb",
"futbol-o",
"tty",
"binoculars",
"plug",
"slideshare",
"twitch",
"yelp",
"newspaper-o",
"wifi",
"calculator",
"paypal",
"google-wallet",
"cc-visa",
"cc-mastercard",
"cc-discover",
"cc-amex",
"cc-paypal",
"cc-stripe",
"bell-slash",
"bell-slash-o",
"trash",
"copyright",
"at",
"eyedropper",
"paint-brush",
"birthday-cake",
"area-chart",
"pie-chart",
"line-chart",
"lastfm",
"lastfm-square",
"toggle-off",
"toggle-on",
"bicycle",
"bus",
"ioxhost",
"angellist",
"cc",
"ils",
"meanpath",
"buysellads",
"connectdevelop",
"dashcube",
"forumbee",
"leanpub",
"sellsy",
"shirtsinbulk",
"simplybuilt",
"skyatlas",
"cart-plus",
"cart-arrow-down",
"diamond",
"ship",
"user-secret",
"motorcycle",
"street-view",
"heartbeat",
"venus",
"mars",
"mercury",
"transgender",
"transgender-alt",
"venus-double",
"mars-double",
"venus-mars",
"mars-stroke",
"mars-stroke-v",
"mars-stroke-h",
"neuter",
"facebook-official",
"pinterest-p",
"whatsapp",
"server",
"user-plus",
"user-times",
"bed",
"viacoin",
"train",
"subway",
"medium",
	);
	return $faIcons;
}
add_filter('redux/font-icons' , 'get_font_fa_icons');

?>
