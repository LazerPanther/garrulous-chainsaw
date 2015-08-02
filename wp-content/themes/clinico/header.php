<!DOCTYPE html>
<!--[if lte IE 8]><html class="ie8 no-js" lang="en"><![endif]-->
<!--[if IE 9]><html class="ie9 no-js" lang="en"><![endif]-->
<html class="not-ie no-js" xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<?php
			$fav = cws_get_option('favicon');
			$fav_url = isset($fav) ? bfi_thumb( $fav['url'], array( 'width' => 32, 'height' => 32, 'crop' => true ) ) : '';
			echo !empty($fav_url) ? '<link rel="shortcut icon" href="'. $fav_url .'" />' : '';
		?>

		<title><?php
			$title = is_woo() ? woocommerce_page_title(false) : (
				is_post_type_archive('staff') ? post_type_archive_title( '', false ) : (
					is_tax() ? (get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) )->name) : (get_post_type() == 'tribe_events' ? get_post_type_object('tribe_events')->labels->name : get_the_title() ) ) );
			echo bloginfo('name') . ': ' . $title; ?></title>
	<?php
		cws_put_ganalytics_code();

		if ( is_singular() )
			wp_enqueue_script( 'comment-reply' );
		cws_process_fonts();
		// Google Analytics' code
		$ga_code = cws_get_option('ga-code');
		echo !empty($ga_code) ? '<script type="text/javascript">' . $ga_code . '</script>' : '';
		wp_head();
	?>
	</head>
	<body <?php body_class(); ?>>
	<?php
		if ($post) {
			$meta = get_post_meta($post->ID,'cws-mb');
			$meta = isset($meta[0]) ? $meta[0] : null;
		}
	?>
	<?php
		$boxed_layout = ('0' != cws_get_option('boxed-layout') ) ? 'boxed' : '';
		echo $boxed_layout ? '<div class="page_boxed">' : '';

	 	if ( cws_is_wpml_active() && !cws_show_flags_in_footer() ){
			echo "<section class='header_language_bar'><div class='container clearfix'><div class='bar'>";
				do_action('icl_language_selector');
			echo "</div></div></section>";
	 	}

		// Start header
		$logo_position = cws_get_option('logo-position');
		$main_header_class = 'page-header';
		if (isset($logo_position)){
			$main_header_class .= ' logo-' . $logo_position;
		}
		if ( !is_front_page() ) {
			$main_header_class .= ' secondary-page';
		}
		echo '<section class="'. $main_header_class .'">';
		?>
			<div class="container">
				<div class="sticky_container clearfix">
				<?php
					cws_show_logo();
					//<!--Start main menu-->
					global $current_user;
					$menu_locations = get_nav_menu_locations();
					$menu_position = cws_get_option("menu-position");
					echo '<nav class="main-nav-container a-' . $menu_position . '">';
					if( isset($menu_locations['header-menu']) ) {
						echo '<div class="mobile_menu_header"><i class="fa fa-bars"></i><div class="mobile_menu_title">' . __("NAVIGATION", THEME_SLUG) . '</div></div>';
						wp_nav_menu( array(
							'theme_location'  => 'header-menu',
							'menu_class' => 'main-menu',
							'container' => false,
							'walker' => new Cws_Walker_Nav_Menu()
						) );
					} elseif ( isset( $current_user->roles[0] ) && 'administrator' === $current_user->roles[0]) {
						echo '<div><h5 style="color:red;background-color:#fff;padding:10px;font-size:18px;margin: -6px 0 0;">Please enable menu in <a href="wp-admin/nav-menus.php">admin panel -&gt; appearance -&gt; menus</a></h5></div>'; // warning
					}
					echo '</nav>';
				?>
						<!--End main menu-->
				</div>
			</div>
		</section>
		<!-- quick search -->
		<section class="toggle_sidebar">
			<div class="container">
				<?php
					$toggle_sidebar = cws_get_option('toggle-sidebar');
					if ( !empty($toggle_sidebar) ):
						ob_start();
						dynamic_sidebar($toggle_sidebar);
						$widgets = ob_get_clean();
						if ( !empty($widgets) ):
						?>
							<div class="switcher_shadow"></div>
							<div id='toggle_sidebar_area' class='theme_color_sb_area'>
							<?php
								echo $widgets;
							?>
							</div>
							<div class="switcher">
								<?php echo cws_get_option("toggle-sidebar-title"); ?>
							</div>
						<?php
						endif;
					endif;
				?>
			</div>
		</section>
		<!--/ quick search -->
		<?php
		if ( !is_search() && !isset($_GET['asearch']) ){
			if ( is_front_page() ) {
				$slider_type = cws_get_option("home-slider-type");
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$is_revslider = is_plugin_active( 'revslider/revslider.php');
				if ((!empty($slider_type)) && ($slider_type!="none") && $is_revslider) {
					echo '<section class="media-part"><div class="container">';
					switch($slider_type) {
						case "img-slider":
							$slider_shortcode = cws_get_option("home-header-slider");
							if (!empty($slider_shortcode)) {
								echo do_shortcode($slider_shortcode);
							}
							break;
						case "video-slider":
							$video_link = cws_get_option("home-header-video");
							if (!empty($video_link)) {
								global $wp_embed;
								$embed_shortcode = "[embed]" . $video_link . "[/embed]";
								echo $wp_embed->run_shortcode($embed_shortcode);
							}
							break;
						case "stat-img-slider":
							$image_object = cws_get_option("home-header-image");
							if (!empty($image_object)) {
								echo '<img src="' . $image_object['url'] . '"></img>';
							}
							break;
					}
					echo '</div></section>';
				}
			}
			else {
				$slider = isset($meta['cws-mb-slider']) ? $meta['cws-mb-slider'] : null;
				if ( ( isset($meta['cws-mb-sb_slider_override']) ) && ( !empty($slider) ) ){
					echo '<section class="media-part"><div class="container">';
					echo do_shortcode($slider);
					echo '</div></section>';
				}
				else {
					echo '<section class="page-title"><div class="container clearfix">';
						$post_title = is_woo() ? woocommerce_page_title(false) : (
							is_post_type_archive('staff') ? post_type_archive_title( '', false ) : (
								is_tax() ? (get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) )->name) : ( get_post_type() == 'tribe_events' ? get_post_type_object('tribe_events')->labels->name : get_the_title() ) ) );
						$page_tile = is_front_page() ?
							__("HOME",THEME_SLUG) : ( isset($GLOBALS['wp_query']->tribe_is_event_query ) ?
								(  $post_title ?
									$post_title : __("EVENTS",THEME_SLUG) ) : ( is_404() ?
									__('404 PAGE', THEME_SLUG ) : $post_title ) );
						echo '<h1>' . $page_tile . "</h1>";
						//echo '<h1>'' . ( is_front_page() ? __("HOME",THEME_SLUG) : get_the_title() ) . "</h1>";
						echo cws_get_option("breadcrumbs") ? ( is_woo() ? woocommerce_breadcrumb() : dimox_breadcrumbs() ) : "";
					echo '</div></section>';
				}
			}
		}
		else{
			echo '<section class="page-title"><div class="container clearfix">';
				$post_title = is_woo() ? woocommerce_page_title(false) : get_the_title();
				$page_tile = __( "Search", THEME_SLUG );
				echo '<h1>' . $page_tile . "</h1>";
				//echo '<h1>'' . ( is_front_page() ? __("HOME",THEME_SLUG) : get_the_title() ) . "</h1>";
				echo cws_get_option("breadcrumbs") ? ( is_woo() ? woocommerce_breadcrumb() : dimox_breadcrumbs() ) : "";
			echo '</div></section>';
		}
		if ( is_front_page() && !is_search() && !isset($_GET['asearch']) ){
			$benefits_sb = cws_get_option("benefits-sidebar");
			if (!empty($benefits_sb)){
				ob_start();
				dynamic_sidebar($benefits_sb);
				$benefits_sb_content = ob_get_clean();
				if (!empty($benefits_sb_content)){
					?>
					<section class="benefits <?php echo $slider_type == 'video-slider' ? 'under' : ''; ?>">
						<div class="container">
							<div class="benefits_area">
								<?php
								echo $benefits_sb_content;
								?>
							</div>
						</div>
					</section>
					<?php
				}
			}
		}
		?>
		<?php
			$is_user_logged = is_user_logged_in();
			$stick_menu = cws_get_option('menu-stick');
		?>
		<script type="text/javascript">
			var stick_menu = <?php echo isset($stick_menu) ? $stick_menu : true ?>;
			var is_user_logged = <?php echo $is_user_logged ? 'true' : 'false' ; ?>;
		</script>
	<!-- HEADER END -->
	<!--Start main content-->
