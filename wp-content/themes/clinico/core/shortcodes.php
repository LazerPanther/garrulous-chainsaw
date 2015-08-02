<?php

global $cws_shortcodes;
$cws_shortcodes = array ('progress', 'embed', 'alert', 'cws_button', 'quote', 'fa', 'featured_fa', 'dropcap', 'mark', 'ourteam', 'portfolio', 'cws_cta', 'news', 'tweets', 'table', 'milestone', 'services', 'shortcode_carousel', 'shortcode_blog');

function add_plugin($plugin_array) {
	global $cws_shortcodes;
	$plugin_array['cws_tmce'] = THEME_URI .'/core/cws_tmce.js';
	//$plugin_array['table'] = THEME_URI .'/core/tmcetable/plugin.js';
	return $plugin_array;
}

function add_button() {
	if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
	{
		add_filter('mce_external_plugins', 'add_plugin');
		add_filter('mce_buttons_3', 'register_button3');
	}
}
add_action('init', 'add_button');

function cws_shortcodes_admin_init( $hook ) {
	if( 'post.php' != $hook && 'post-new.php' != $hook ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker');
	wp_enqueue_script( 'wp-color-picker');
}
add_action('admin_enqueue_scripts', 'cws_shortcodes_admin_init');

// Add Shortcode
function progress_shortcode( $attr ) {
	extract( shortcode_atts(array('progress' => '50', 'title' => null, 'color' => null), $attr )	);
	return '<div class="single_bar ' . ( $title ? 'with_title' : '' ) . '">' . ( $title ? '<div class="title">' . $title . '</div>' : '' ) . '<div class="scale"><div class="progress" data-value="' . $progress . '"' . ( $color ? 'style="background-color:' . $color . ';"' : '' ) . '><div class="indicator">0</div></div></div></div>';
}
add_shortcode( 'progress', 'progress_shortcode' );

function quote_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(
		array(
			'photo' => null,
			'author' => '',
		), $atts));
	return cws_testimonial_renderer($photo, $content, $author);
}
add_shortcode( 'quote', 'quote_shortcode' );

function shortcode_alert($atts, $content = null) {
	extract(shortcode_atts(
		array(
			'type' => 'information',
			'title' => null,
			'e_style' => null,
			'grey_skin' => null
		), $atts));
	return '<div class="message_box ' . $type . '_box ' . ( $grey_skin ? " grey_skin" : "" ) . '">' . ( $title ? "<div class='message_box_header'>" . $title . "</div>" : "" ) . "<p>" . do_shortcode($content) . "</p>" . '</div>';
}
add_shortcode('alert', 'shortcode_alert');

function cws_shortcode_milestone ( $atts, $content = null ){
	extract(shortcode_atts(array(
			"fa" => null,
			"number" => null,
			), $atts));
	$out = "";
	$out .= "<div class='milestone" . ( $fa ? " with_icon" : "" ) . "'>";
	$out .= $fa ? "<div class='milestone_icon'><i class='icon_frame fa fa-" . $fa . "'></i></div>" : "";
	$out .= "<div class='milestone_content'>" . ( $number ? "<div class='number' data-number='$number'>0</div>" : "" ) . $content . "</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode( "milestone", "cws_shortcode_milestone" );

// Font-Awesome shortcode
function shortcode_fa($attr, $content = null) {
	extract(shortcode_atts(
		array(
			'code' => null,
			'size' => null,
			'custom_color' => null,
			'color' => null,
			'bg_color' => null,
			'border_color' => null,
		), $attr));
	$custom_color_settings = $custom_color ? " data-font-color='$color' data-bg-color='$bg_color' data-border-color='$border_color'" : "";
	$out = "<i class='soc_icon fa fa-" . $code . " fa-" . $size . ( $custom_color ? " custom_color" : "" ) . "'" . $custom_color_settings . "></i>";
	return $out;
}
add_shortcode('fa', 'shortcode_fa');

function shortcode_featured_fa($attr, $content = null) {
	extract(shortcode_atts(
		array(
			'code' => null,
			'size' => null,
			'type' => null,
			'float' => null,
			'custom_color' => null,
			'color' => null,
			'bg_color' => null,
		), $attr));

	$position = "";
	if ($float) {
		switch ($float) {
			case 'left':
				$position = "f-left";
				break;
			case 'right':
				$position = "f-right";
				break;
		}
	}
	$color_styles = $custom_color ? " style='color:$color;background-color:$bg_color;'" : "";
	$out = "<i class='featured_icon ". $position ." $type fa fa-" . $code . " fa-" . $size . ( $custom_color ? " custom_color" : "" ) . "'" . $color_styles . "></i>";
	return $out;
}
add_shortcode('featured_fa', 'shortcode_featured_fa');

function shortcode_mark($attr, $content = null) {
	extract(shortcode_atts(array(
		'color' => '',
		'bg_color' => '',
	), $attr));
	//$class = '';
	$output = '<mark';
	if ( $color || $bgcolor ) {
		$output .= ' style="' . ( $color ? 'color: ' . $color . ';' : '') . ($bg_color ? ' background-color: ' . $bg_color . ';' : '') . '"';
	}
	$output .= '>';
	$output .= $content;
	$output .= '</mark>';
	return $output;
}
add_shortcode('mark', 'shortcode_mark');

function shortcode_dropcap($attr, $content = null) {
	return "<span class='dropcap'>" . $content . "</span>";
}
add_shortcode('dropcap', 'shortcode_dropcap');


function register_button3($buttons) {
	global $cws_shortcodes;
	$buttons = array_merge((array)$buttons, (array)$cws_shortcodes );
	return $buttons;
}

// outputs description post
function shortcode_cws_cta($attr, $content = null) {
	extract(shortcode_atts(
		array(
			'icon' => null,
			'img' => null,
			'title' => null,
			'button_text' => __('Click Here', THEME_SLUG),
			'link' => '#'
		), $attr));
	$output = '<div class="cws-widget callout_widget clearfix ' . ( ($icon || $img) ? "with_icon" : "" ) . '">';
	$output .= $img ? "<div class='icons_part icon_frame img_icon'><img src=" . $img . "></div>" : "";
	$output .= $icon ? "<div class='icons_part icon_frame'><i class='fa fa-" . $icon . "'></i></div>" : "";
	$output .= "<div class='content_wrapper'><div class='text_part'>" . ( $title ? "<div class='title'>$title</div>" : "" ) . do_shortcode($content) . "</div><div class='button_part'><a class='cws_button medium' href='$link'>$button_text</a></div></div>";
	$output .= '</div>';

	return $output;
}
add_shortcode('cws_cta', 'shortcode_cws_cta');

function shortcode_ourteam($attr) {
	extract(shortcode_atts(
		array(
			'title' => '',
			'mode' => 'all',
			'render' => 'all',
			'cats' => '',
			'usefilter' => '0',
		), $attr));
	$output = '';

	wp_enqueue_script( 'cws-script-portfolio-js', THEME_URI . '/core/js/portfolio.js', array('jquery') );
	require_once (THEME_DIR . '/core/portfolio-cols.php');

	$carousel = $mode == "carousel";
	$carousel = !empty($render) && 'carousel' === $render ? true : $carousel;
		$output .= '<div class="cws_widget"><div class="cws_widget_content">';
	if ($carousel) {
		$output .= render_portfolio_carousel(-1, $title, $cats, 'staff');
	} else {
		$pid = get_the_ID();
		$output .= '<section class="photo_tour_section cws_widget">';
		$output .= render_portfolio( -1, $cats, '1' === $usefilter, -1, 1, $cats, false, 'pinterest', $title, $pid, 'staff' );
		$output .= '</section>';
	}
		$output .= '</div></div>';
	/*

	$r = new WP_Query($arr);
	if ($r->have_posts()) {
		$output .= '<div class="cws_widget"><div class="cws_widget_content">';
		if ($carousel):
			$output  .= "<div class='carousel_header clearfix'>";
			$output  .=  $r->post_count ? "<div class='carousel_nav'><i class='prev fa fa-angle-left'></i><i class='next fa fa-angle-right'></i><div class='clearfix'></div></div>" : "" ;
		endif;
		$output .= !empty( $title ) ? "<div class='widget-title'>$title</div>" : "";
		if ($carousel) $output  .= "</div>";
		$output .= '<div class="our_team' . ( $carousel ? ' carousel_content' : '' ) . '">';
		$output .= '<div class="' . ( $carousel ? 'carousel' : 'grid isotope' ) . '">';
		while ( $r->have_posts() ) {
			$r->the_post();
			$output .= '<div class="item">';
			$output .= '<div class="pic">';
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			$output .= '<img src="' . bfi_thumb($img[0], array('width' => 270, 'height' => 270) ) . '" alt="">';
			$output .= '<div class="hover-effect"></div>';
			$output .= '<div class="links">';
			$cws_stored_meta = get_post_meta( get_the_ID(), 'cws-staff');
			$cws_stored_meta = isset( $cws_stored_meta[0]['social'] ) ? $cws_stored_meta[0]['social'] : array();

			if (count($cws_stored_meta)>0) {
				foreach ($cws_stored_meta as $social_item) {
					$url = $social_item['cws-mb-socialgroup-url'];
					$title = $social_item['cws-mb-socialgroup-title'];
					$fa =  $social_item['cws-mb-socialgroup-fa'];
					$output .= '<a ' . ( $url ? "href='$url' " : "" ) . ( $title ? "title='$title' " : "" ) . ( $fa ? "class='fa fa-$fa' " : "" ) . '></a>';
				}
			}

			$output .= "</div></div><div class='team_member_info'>";

			$name = get_the_title();
			$link = get_permalink();
			$output .= $name ?  "<a href='$link'><div class='name'>" . $name . "</div></a>" : "";

			$terms = wp_get_post_terms(get_the_ID(), 'cws-staff-position');
			if ( count($terms) ):
				$output .= "<div class='positions'>";
				$i = 0;
				foreach ($terms as $k=>$v) {
					$i++;
					$output .= $v->name;
					if ($i < count($terms)) {
						$output .= ', ';
					}
				}
				$output .= "</div>";
			endif;

			$output .= "</div></div>";
		}
		$output .= "</div></div></div></div>";
	}
	wp_reset_query();
	*/
	return $output;
}
add_shortcode('ourteam', 'shortcode_ourteam');

function shortcode_current_year() { return date("Y"); }
add_shortcode('current-year', 'shortcode_current_year');

function shortcode_site_title() { return get_bloginfo('name'); }
add_shortcode('site-title', 'shortcode_site_title');

function shortcode_site_tagline() { return get_bloginfo('description'); }
add_shortcode('site-tagline', 'shortcode_site_tagline');

function shortcode_site_url() { return home_url(); }
add_shortcode('site-url', 'shortcode_site_url');

function shortcode_wpurl() { return site_url(); }
add_shortcode('wp-url', 'shortcode_wpurl');

function shortcode_twitter($attr) {
	extract(shortcode_atts(
		array(
			'tweets' => 4,
			'visible' => 2,
			'before_widget' => "<div class='cws-widget'>",
			'after_widget' => "</div>",
			'before_title' => "<div class='widget-title'><span>",
			'after_title' => "</span></div>",
			'sidebar' => false,
			'backlight' => false,
			'title' => ''
		), $attr));
	$visible = intval($visible);
	$tweets = intval($tweets);
	$out = '';
	$name = cws_get_option('tw-api-name');
	$twt_obj = cws_getTweets( $name, $tweets );
	if ($twt_obj && is_array($twt_obj)) {
		if (!array_key_exists('error', $twt_obj)) {
			$is_carousel = ($visible < $tweets ? true : false);
			$out .= $sidebar ? "" : $before_widget;
			$backlight_class = $backlight ? "backlight" : "";
			$out .= "<div class='cws-widget-content $backlight_class'>";

			if ($is_carousel):
				$out .= "<div class='carousel_header clearfix'>";
				$out .= count($twt_obj) ? "<div class='widget_carousel_nav'><i class='prev fa fa-angle-left'></i><i class='next fa fa-angle-right'></i></div>" : "" ;
			endif;
			$out .= !empty( $title ) ? $before_title . $title . $after_title : "";
			if ($is_carousel) $out .= "</div>";
			if ($is_carousel) $out .= "<div class='carousel_content'>";

			$out .= '<ul class="latest_tweets ' . ($is_carousel ? ' widget_carousel' : '' ) . '">';
			if ($twt_obj) {
				$i = 0;
				foreach ($twt_obj as $tweets) {
					if ( $i == 0 ) {
						$out .= '<li><ul>';
					}
					//var_dump($tweets['tweets'][0]);
					//die;
					$strtime = strtotime($tweets['created_at']);
					$tweet_text = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $tweets['text']);
					$tweet_text = preg_replace('/#([\\d\\w]+)/', '<a href="http://twitter.com/search?q=%23$1&amp;src=hash">$0</a>', $tweet_text);
					if (strlen($tweet_text) > 0) {
						$out .= '<li class="clearfix"><div class="icon_frame"><i class="fa fa-twitter fa-2x"></i></div><div>';
						$out .= '<p>' . $tweet_text . '</p>';
						$out .= '<span class = "date">' . date('M d, Y', $strtime) . '</span>';
						$out .= '</div></li>';
					}
					$i++;
					if ($i == $visible) {
						$out .= '</ul></li>';
						$i = 0;
					}
				}
				if ( ( $i != $visible ) && ( $i != 0 ) ) {
					$out .= '</ul></li>';
				}
			} else {
				$out .= '<li>' . __( 'Twitter API keys and tokens are not set.', THEME_SLUG ) . '</li>';
			}
			$out .= '</ul>';
			if ($is_carousel) $out .= "</div>";
			$out .= '</div>';
			$out .=  $sidebar ? "" : $after_widget;
		} else {
			$out = $twt_obj['error'];
		}
	} else {
		$out = 'Twitter feed is currently turned off. You may turn it on and set the API Keys and Tokens using <a href="/wp-admin/admin.php?page=_options&tab=6" target="_blank">Theme Options -> Social Options: Enable Twitter Feed</a>.';
	}
	return $out;
}
add_shortcode('twitter', 'shortcode_twitter');

function shortcode_button($atts, $content){
	extract( shortcode_atts(array(
			'type' => 'default',
			'size' => 'medium',
			'link' => '#',
			'custom_color' => null,
			'button_color' => null,
			'text_color' => null,
			'border_color' => null
		),$atts ));
	$class = $type ? ( $type != 'default' ? $type . " " : "" ) : "";
	$class .= $size ? ( $size != 'medium' ? $size . " " : "" ) : "";
	$class .= $custom_color ? "custom_color " : "";
	$out = "<a href='$link' class='cws_button " . $class . "'" . ( $custom_color ? " data-bg-color='" . $button_color . "'" . " data-font-color='" . $text_color . "'" . " data-border-color='" . $border_color . "'" : "" ) . ">" . do_shortcode($content) . "</a>";
	return $out;
}
add_shortcode('cws_button', 'shortcode_button');

function shortcode_services($attr) {
	extract(shortcode_atts(
		array(
			'filter' => '',
			'open' => '',
	), $attr));
	$incs = !empty($filter) ? explode(',', $filter) : array();
	$include = array();
	foreach ($incs as $inc) {
		$include[] = get_term_by('slug', $inc, 'cws-staff-dept')->term_id;
	}
	$opened = !empty($open) ? explode(',', $open) : array();
	$terms_args = array('hide_empty' => 0,
						'parent' => 0,
						'include' => $include);
	$depts = get_terms('cws-staff-dept', $terms_args);
	$out = '';
	if (0 !== count($depts) ) {
		$out .= "<div class='services'>";
		foreach ($depts as $dept=>$v) {
			$open = in_array($v->slug, $opened);
			$out .= '<div class="accordion_section'. ( $open ? ' active' : '' ) .'">';
			$fa_widget = get_option_value( 'cws-clinico-dept-fa', $v->term_id );
			$fa_check = sprintf('<i class="service_icon fa fa-2x fa-%s"></i>',  !empty($fa_widget) ? $fa_widget : 'check');
			$out .= sprintf('<div id="cws-service-%s" class="accordion_title">%s%s<i class="accordion_icon"></i></div>', $v->term_id, $fa_check, $v->name);
			$out .= '<div class="accordion_content"' . ( $open ? '' : ' style="display:none;"' ) . '>';
			$out .= '<div class="details">';
				$title_img = wp_get_attachment_image_src(get_option_value( 'cws-clinico-dept-img', $v->term_id), 'full');
				if ($title_img) {
					$out .= '<div class="img_part"><img src="' . bfi_thumb($title_img[0], array( 'width'=>'191', 'height'=>'116' )) . '" alt=""></div>';
				}
				$out .= '<div class="description_part"><div class="description_part_container">';
				if (strlen($v->description) > 0) {
					// extract first line, wrap it with strong, the rest convert newlines into brakes
					$out .= '<div>';
					$ar = explode("\r\n", $v->description);
					if (count($ar) == 1) {
						$ar = explode("\n", $v->description);
						if (count($ar) == 1) {
							$ar = explode("\r", $v->description);
						}
					}
					if (count($ar) > 1) {
						$out .= '<div class="desc_title">' . $ar[0] . '</div>';
						array_splice($ar,0,1);
					}
					$out .= nl2br(implode("\r\n", $ar));
					$out .= '</div>';
				}
				$org = get_option_value( 'cws-clinico-dept-org', $v->term_id );
				if (strlen($org) > 0) {
					// extract first line, wrap it with strong, the rest convert newlines into brakes
					$out .= '<div>';
					$ar = explode("\r\n", $org);
					if (count($ar) == 1) {
						$ar = explode("\n", $org);
						if (count($ar) == 1) {
							$ar = explode("\r", $org);
						}
					}
					if (count($ar) > 1) {
						$out .= '<div class="desc_title">' . $ar[0] . '</div>';
						array_splice($ar,0,1);
					}
					$out .= nl2br(implode("\r\n", $ar));
					$out .= '</div>';
				}
				if (in_array( 'the-events-calendar/the-events-calendar.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
					$events = get_option_value( 'cws-clinico-dept-events', $v->term_id );
					if (!empty($events)) {
						$events = explode(',',$events);
						// displying events
						if ( count($events) > 0 ):
							$out .= '<div>';
							$out .= '<div class="events desc_title">' . __( 'Events: ', THEME_SLUG ) . '</div>';
							$homeurl = home_url();
							for( $i=0; $i<count($events); $i++ ) {
								if (!empty($events[$i])) {
									$ev_obj = get_term($events[$i], 'tribe_events_cat');
									if($ev_obj) {
										if ( $i!=0 ) $out .= ', ';
										$out .= '<a href="' . $homeurl . '/?tribe_events_cat=' . $ev_obj->slug . '">' . $ev_obj->name . '</a>';
									}
								}
							}
							$out .= '</div>';
						endif;
					}
				}
				$out .= '</div></div>';
			$out .= '</div>';
			$out .= '<div class="row clearfix">';
			$procedures = get_option_value( 'cws-clinico-dept-procedures', $v->term_id);
			if (!empty($procedures)) {
				$procedures = explode(',',$procedures);
				// displying procedures
				foreach($procedures as $proc) {
					$out .= '<div class="col">';
					$proc_obj = get_term($proc, 'cws-staff-procedures');
					if ($proc_obj) {
						$out .= '<div class="col_title">' . $proc_obj->name . '</div>';
						$out .= !empty($proc_obj->description) ? '<div class="desc_row">' . $proc_obj->description . '</div>' : '';
					}
					$proc_children = get_terms('cws-staff-procedures', 'hide_empty=0&parent=' . $proc);
					if (!empty($proc_children)) {
						foreach($proc_children as $proc_proc) {
							$proc_obj = get_term($proc_proc, 'cws-staff-procedures');
							$out .= '<div class="service_row"><dl>';
							$out .= '<dt><span>'. $proc_proc->name .'</span></dt>';
							$out .= '<dd><span>'. $proc_proc->description .'</span></dd>';
							$out .= '</dl></div>';
						}
					}
					$out .= '</div>';
				}
				if ($v->count > 0) {

					$out .= '<div class="col">';
					$out .= '<div class="col_title">' . __('Doctors',THEME_SLUG) . '</div>'; // !!!

					$tax_query_arr = array(
								'taxonomy' => 'cws-staff-dept',
								'field' => 'slug',
								'terms' => array($v->slug)
							);

					$arr = array(
						'post_type' => 'staff',
						'ignore_sticky_posts' => true,
						'tax_query' => array( $tax_query_arr )
					);
					$p = new WP_Query($arr);
					if ($p->have_posts()) :
						while ($p->have_posts()) : $p->the_post();
							$out .= '<div class="service_row"><dl><dt><span>';
							$positions = wp_get_post_terms(get_the_ID(), 'cws-staff-position');
							$i = count($positions);
							$name = '';
							foreach ($positions as $pos=>$n) {
								$i--;
								$name .= $i ? $n->name . ', ' : $n->name;
							}
							$out .= get_the_title() . '</span></dt><dd><span>' . $name;
							$out .= '</span></dd></dl></div>';
						endwhile;
					endif;
					// print 'full doctors list' ?
					$out .= '</div>';
				}
			}
			$out .= "</div></div></div>";
		}
		$out .= '</div>';
	}
	return $out;
}

add_shortcode('services', 'shortcode_services');

function shortcode_cws_portfolio($attr = array()) {
	extract( shortcode_atts(
		array(
			'cols' => '2',
			'cats' => '',
			'usefilter' => '',
			'filter' => '',
			'usecarousel' => '',
			'carousel' => '',
			'title' => '',
			'postspp' => '',
			'items' => '',
		), $attr ) );
	require_once (THEME_DIR . '/core/portfolio-cols.php');
	$usecarousel = !empty($usecarousel) ? $usecarousel : $carousel;
	$usefilter = !empty($usefilter) ? $usefilter : $filter;
	$postspp = !empty($postspp) ? $postspp : $items;
	wp_enqueue_script( 'cws-script-portfolio-js', THEME_URI . '/core/js/portfolio.js', array('jquery') );
	$content = "<section class='photo_tour_section cws_widget'>";
	if ($usecarousel == '1') {
		$content .= render_portfolio_carousel($postspp, $title, $cats, 'portfolio');
	} else {
		$blogtype = in_array( $cols, array('2','3','4') ) ? 'pinterest' : 'large';
		$pid = get_the_ID();
		$pid = !empty($pid) ? $pid : ( isset($post) ? $post->ID : get_query_var("page_id"));
		$content .= render_portfolio( $cols, $cats, '1' === $usefilter, $postspp, 1, $cats, false, $blogtype, $title, $pid );
	}
	$content .= '</section>';
	return $content;
}

add_shortcode('portfolio', 'shortcode_cws_portfolio');

function cws_shortcode_blog ( $attr, $content ){
	extract (shortcode_atts(array(
		'title' => '',
		'post_count' => '9',
		'columns' => '3',
		'cats' => ''
		), $attr));
	$args = array( 'post_type' => 'post',
					'post_status' => 'publish',
					'ignore_sticky_posts' => false,
					'posts_per_page'=>$post_count);
	$tech_cat = cws_get_option('tech-category');
	$tech_cat = isset( $tech_cat ) ? $tech_cat : array();
	$tech_cat_array = $tech_cat; // copy

	if ( count( $tech_cat ) > 0 ){
		for ( $i = 0; $i < count( $tech_cat ); $i ++ ){
			$tech_cat[$i] = '-' . $tech_cat[$i];
		}
		$tech_cat = implode( ',', $tech_cat );
		$args["cat"] = $tech_cat;
	}
	if ( !empty($cats) ){
		$cats = explode( ",", $cats );
		for ( $i = 0; $i < count( $cats ); $i ++ ){
			$cats[$i] = get_category_by_slug( $cats[$i] )->term_id;
		}
		for ( $i = 0; $i < count( $cats ); $i ++ ){
			if ( in_array( $cats[$i], $tech_cat_array ) ) array_splice( $cats, $i, 1 );
		}
		if ( count( $cats ) > 0 ){
			$cats = implode( ",", $cats );
			if (isset($args["cat"])){
				$args["cat"] .= "," . $cats;
			}
			else{
				$args["cat"] = $cats;
			}
		}
	}
	$r = new WP_Query($args);
	ob_start();
	?>
	<div class="cws_widget">
		<div class="cws_widget_content blog">
			<?php echo !empty($title) ? "<div class='widget-title'>$title</div>" : ""; ?>
			<section class="news news-pinterest <?php echo 'news-' . $columns; ?>">
				<div class="grid isotope">
					<?php
					cws_blog_output($r, $post_count, $post_count, 'pinterest', $columns, 'none', 1);
					?>
				</div>
			</section>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode('shortcode_blog', 'cws_shortcode_blog');

function cws_shortcode_carousel ( $attr, $content){
	extract (shortcode_atts(array(
			'title' => '',
		), $attr));
	$out = "<div class='shortcode_carousel'>";
	$out .= "<div class='carousel_header clearfix'>";
	$out .= "<div class='carousel_nav'><i class='prev fa fa-angle-left'></i><i class='next fa fa-angle-right'></i><div class='clearfix'></div></div>";
	$out .= !empty($title) ? "<div class='widget-title'>$title</div>" : "";
	$out .= "</div>";
	$out .= "<div class='carousel_content'>" . do_shortcode( $content ) . "</div>";
	$out .= "</div>";
	return $out;
}
add_shortcode('shortcode_carousel', 'cws_shortcode_carousel');

/* pb's intermediary shortcodes */
function cws_tweet_shortcode($args) {
	extract(shortcode_atts(
		array(
			'title' => '',
			'items' => '4',
			'visible' => '4',
		), $args));
	return do_shortcode('[twitter tweets=' . $items . ' visible='. $visible .' title="' . $title . '" /]');
}
add_shortcode( 'tweet', 'cws_tweet_shortcode' );

function cws_blog_shortcode($args) {
	extract(shortcode_atts(
		array(
			'cols' => 'large',
			'items' => '4',
			'cats' => '',
		), $args));
	return do_shortcode('[shortcode_blog post_count=' . $items . ' columns="' . $cols . '" cats="' . $cats . '" /]');
}
add_shortcode( 'blog', 'cws_blog_shortcode' );
?>