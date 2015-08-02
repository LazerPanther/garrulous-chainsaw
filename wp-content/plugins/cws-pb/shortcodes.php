<?php
$pbf_redeclares = null;
if (file_exists(CWS_PB_THEME_DIR . '/pbf.php')) {
	require_once (CWS_PB_THEME_DIR . '/pbf.php');
}

if ( !shortcode_exists( 'cws-row' ) ) {
	function shortcode_cws_row ($atts, $content){
		extract(shortcode_atts(array(
			'cols' => 1,
			'flags' => 0,
			'extra_style' => '',
			'margin_left' => 'auto',
			'margin_right' => 'auto',
			'margin_top' => 'auto',
			'margin_bottom' => 'auto'), $atts));
		$style = "";
		$style .= $margin_left != "auto" ? "margin-left:" . $margin_left . "px;" : "";
		$style .= $margin_right != "auto" ? "margin-right:" . $margin_right . "px;" : "";
		$style .= $margin_top != "auto" ? "margin-top:" . $margin_top . "px;" : "";
		$style .= $margin_bottom != "auto" ? "margin-bottom:" . $margin_bottom . "px;" : "";

		$tcol_class = (bool)($flags & 1) ? ' eq-columns' : '';
		$tcol_class = !empty($extra_style) ? ' ' . $extra_style : '';
		$out = "<div class='grid-row" . $tcol_class . " clearfix'" . ( !empty($style) ? " style='$style'" : "" ) . ">";
		$out .= do_shortcode($content);
		$out .= "</div>";
		return $out;
	}
	add_shortcode('cws-row','shortcode_cws_row');
}

if ( !shortcode_exists( 'col' ) ) {
	function shortcode_cws_col ($atts, $content){
		extract(shortcode_atts(
			array(
				'span' => 12,
				'flags' => 0
			), $atts));
		$tcol_class = (bool)($flags & 1) ? ' pricing_table_column' : '';
		$tcol_class .= (bool)($flags & 2) ? ' active_table_column' : '';
		$out = "<div class='grid-col" . $tcol_class . " grid-col-" . $span . "'>";
		$out .= do_shortcode( $content );
		$out .= "</div>";
		return $out;
	}
	add_shortcode('col','shortcode_cws_col');
}

if ( !shortcode_exists( 'cws-widget' ) ) {
	function shortcode_cws_widget ( $atts, $content ){
		$original_att = $atts;
	/*	$atts = shortcode_atts(
			array(
				'type'=>'text',
				'title'=>null,
				'e_style'=>null,
				'toggle'=>null
			), $atts);*/
		extract ($atts);
		$out = '';
		if ( 'tcol' !== $type ) {
			$out .= "<section class='cws-widget'>";
			if ('callout' !== $type && 'tweet' !== $type && 'portfolio' !== $type && 'ourteam' !== $type ) {
				$out .= $title ? "<div class='widget-title'>" . $title . "</div>" : "";
			}
		}
		$args = array( 'atts' => $atts , 'content' => $content );
		switch ($type){
			case 'text':
				$out .= cws_text_renderer( $args );
				break;
			case 'tabs':
				$out .= cws_tabs_renderer( $args );
				break;
			case 'accs':
				$out .= cws_accordion_renderer( $args );
				break;
			case 'callout':
				$out .= cws_callout_renderer( $args );
				break;
			case 'tweet':
			case 'portfolio':
			case 'ourteam':
				$out .= cws_title_renderer( $args );
				break;
			case 'tcol':
				$out .= cws_pricecol_renderer( $original_att, $content );
				break;
			default: //
				$out .= do_shortcode( $content );//
		}
		if ('tcol' !== $type && 'callout' !== $type && 'tweet' !== $type) {
			$out .= "</section>";
		}
		return $out;
	}
	add_shortcode('cws-widget','shortcode_cws_widget');

	if (!$pbf_redeclares || !in_array('cws_pricecol_renderer', $pbf_redeclares) ) {
		function cws_pricecol_renderer($args, $content) {
			$args = shortcode_atts(
				array(
					'ishilited' => null,
					'title' => '',
					'encouragement' => '',
					'currency' => '',
					'price' => '',
					'price_description' => '',
					'order_url' => '',
					'button_text' => '',
				), $args);
			extract ($args);
			$out = '';
			//$out  = '<div class="pricing_table' . $isactive . '">';
			$out .= '<div>';
			$out .= '<div class="pricing_table_header">';
			$out .= '<div class="title">'. $title .'</div>';
			$out .= '<div class="encouragement">'. $encouragement .'</div>';
			$out .= '</div>';
			$out .= '<div class="price_part">';
			$out .= '<span class="price_container">';
			$out .= '<span class="currency">'. $currency .'</span>';
			$out .= '<span class="price">'. $price .'</span>';
			$out .= '<span class="price_description">'. $price_description .'</span>';
			$out .= '</span>';
			$out .= '</div>';
			$out .= '<div class="content_part">';
			$out .= do_shortcode($content);
			$out .= '</div>';
			$out .= '<a href="'. $order_url .'" class="cws_button large button_text pricing_table_button">'. $button_text .'</a>';
			$out .= '</div>';
			//$out .= '</div>';
			return $out;
		}
	}
}

if (!$pbf_redeclares || !in_array('cws_text_renderer', $pbf_redeclares) ) {
	function cws_text_renderer ($args) {
		extract($args);
		$out = "<section class='cws_widget_content'>";
		$out .= do_shortcode($content);
		$out .= "</section>";
		return $out;
	}
}

if (!$pbf_redeclares || !in_array('cws_tabs_renderer', $pbf_redeclares) ) {
	function cws_tabs_renderer ($args) {
		extract($args);
		$GLOBALS['tabs'] = $GLOBALS['tab_items_content'] = array();
		do_shortcode($content);
		$tabs = $GLOBALS['tabs'];
		$tab_items_content = $GLOBALS['tab_items_content'];
		unset ( $GLOBALS['tabs'], $GLOBALS['tab_items_content'] );
		if ( (!count($tabs)) || (!count($tab_items_content)) ) return;
		$out = "<div class='cws_widget_content tab_widget" . ( isset($atts['e_style']) ? " " . $atts['e_style'] : "" ) . "'>
				<div class='tabs'>";
		for ( $i=0; $i<count($tabs); $i++ ){
			$out .= "<a class='tab" . ( $tabs[$i]['open'] ? ' active' : '' ) . "' role='tab' tabindex='$i'>" . $tabs[$i]['title'] . "</a>";
		}
		$out .= "</div>
				<div class='tab_items'>";
		for ( $i=0; $i<count($tab_items_content); $i++ ){
			$out .= "<div class='tab_item' role='tabpanel' tabindex='$i'" . ( $tabs[$i]["open"] ? "" : " style='display:none'" ) . ">" . do_shortcode($tab_items_content[$i]) . "</div>";
		}
		$out .= "</div>
				</div>";
		unset( $tabs );
		unset( $tab_items_content );
		return $out;
	}
}

if ( !shortcode_exists( 'item' ) ) {
	function cws_item_shortcode ($atts, $content) {
		extract( shortcode_atts(
			array(
					'type' => NULL,
					'open' => NULL,
					'title' => __('TITLE',THEME_SLUG),
					'iconfa' => '',
					'iconimg' => ''
			), $atts));
		if ( empty($type) ) return;
		switch ($type){
			case 'tabs':
				cws_tab_item_handler( $title, $content, $open );
				break;
			case 'accs':
				return cws_accordion_item_renderer( $title, $content, $open, $iconfa, $iconimg );
				break;
		}
	}
	add_shortcode( 'item', 'cws_item_shortcode' );
}

function cws_pb_ourteam_shortcode($args) {
	extract(shortcode_atts(
		array(
			'cats' => '',
		), $args));
	$arr = array(
			'posts_per_page' => '10',
			'post_type' => 'staff',
			'ignore_sticky_posts' => true,
			'tax_query' => array(
				array(
					'taxonomy' => 'cws-staff-dept',
					'field' => 'slug',
					'terms' => explode(',',str_replace('|', '%', $cats)),
				),
			)
		);
	if (empty($cats)) {
		unset($arr['tax_query']);
	}
	$output = '';

	$r = new WP_Query($arr);
	if ($r->have_posts()) {
		while ( $r->have_posts() ) {
			$r->the_post();
			$output .= '<div class="item">';
			$thumb_id = get_post_thumbnail_id( get_the_ID() );
			$output .= '<div class="pic" data-id="' . $thumb_id . '">';
			$img = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
			$output .= '<img src="' . bfi_thumb($img[0], array('width' => 48, 'height' => 48) ) . '" alt="">';
			$output .= '</div></div>';
		}
		$output .= '<div class="clearfix"></div>';
	}
	wp_reset_query();
	return $output;
}
add_shortcode( 'pb_ourteam', 'cws_pb_ourteam_shortcode' );

function cws_pb_portfolio_shortcode($args) {
	extract(shortcode_atts(
		array(
			'cats' => '',
			'cols' => '2',
			'items' => '10'
		), $args));

	$pb_options = function_exists('cws_get_pb_options') ? cws_get_pb_options() : null;

	$port_post_type = ($pb_options && isset($pb_options['portfolio']['options']['post_type'])) ? $pb_options['portfolio']['options']['post_type']  : 'portfolio';
	$port_tax = ($pb_options && isset($pb_options['portfolio'])) ? $pb_options['portfolio']['options']['taxonomy']  : 'cws-portfolio-type';

	$arr = array(
			'posts_per_page' => $items,
			'post_type' => $port_post_type,
			'ignore_sticky_posts' => true,
			'tax_query' => array(
				array(
					'taxonomy' => $port_tax,
					'field' => 'slug',
					'terms' => explode(',',str_replace('|', '%', $cats)),
				),
			)
		);
	if (empty($cats)) {
		unset($arr['tax_query']);
	}
	$output = '';

	$r = new WP_Query($arr);
	if ($r->have_posts()) {
		while ( $r->have_posts() ) {
			$r->the_post();
			$output .= '<div class="item">';
			$thumb_id = get_post_thumbnail_id( get_the_ID() );
			$output .= '<div class="pic" data-id="' . $thumb_id . '">';
			$img = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
			$output .= '<img src="' . bfi_thumb($img[0], array('width' => 48, 'height' => 48) ) . '" alt="">';
			$output .= '</div></div>';
		}
		$output .= '<div class="clearfix"></div>';
	}
	wp_reset_query();
	return $output;
}
add_shortcode( 'pb_portfolio', 'cws_pb_portfolio_shortcode' );
add_shortcode( 'pb_portfolio_fw', 'cws_pb_portfolio_shortcode' );

function cws_pb_blog_shortcode($args) {
	extract(shortcode_atts(
		array(
			'cats' => '',
			'items' => '8',
		), $args));
	$arr = array(
			'posts_per_page' => $items,
			'post_type' => 'post',
			'ignore_sticky_posts' => true,
			'tax_query' => array(
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => explode(',',str_replace('|', '%', $cats)),
				),
			)
		);
	if (empty($cats)) {
		unset($arr['tax_query']);
	}
	$output = '';

	$r = new WP_Query($arr);
	if ($r->have_posts()) {
		while ( $r->have_posts() ) {
			$r->the_post();
			$output .= '<div class="item">';
			$thumb_id = get_post_thumbnail_id( get_the_ID() );
			if ($thumb_id) {
				$output .= '<div class="pic" data-id="' . $thumb_id . '">';
				$img = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
				$output .= '<img src="' . bfi_thumb($img[0], array('width' => 64, 'height' => 64) ) . '" alt="">';
				$output .= '</div>';
			}
			$output .= '<div class="text">';
			$output .= '<h4>' . get_the_title() . '</h4>';
			$output .= '<p>' . cws_pb_limit_text(apply_filters('the_content', get_the_content()), 30). '</p>';
			$output .= '</div>';
			$output .= '</div>';
		}
		$output .= '<div class="clearfix"></div>';
	}
	wp_reset_query();
	return $output;
}
add_shortcode( 'pb_blog', 'cws_pb_blog_shortcode' );

function cws_pb_tweet_shortcode($args) {
	extract(shortcode_atts(
		array(
			'items' => '4',
		), $args));
	$output = '';

	$twt_obj = cws_getTweets( '', $items, false );
	if ($twt_obj && is_array($twt_obj)) {
		if (!array_key_exists('error', $twt_obj)) {
			$output .= '<ul>';
			if ($twt_obj) {
				$i = 0;
				foreach ($twt_obj as $tweets) {
					$strtime = strtotime($tweets['created_at']);
					$tweet_text = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $tweets['text']);
					$tweet_text = preg_replace('/#([\\d\\w]+)/', '<a href="http://twitter.com/search?q=%23$1&amp;src=hash">$0</a>', $tweet_text);
					if (strlen($tweet_text) > 0) {
						$output .= '<li>';
						$output .= '<p>' . $tweet_text . '</p>';
						$output .= '<span class = "date">' . date('M d, Y', $strtime) . '</span>';
						$output .= '</li>';
					}
					$i++;
				}
			} else {
				$output .= '<li>' . __( 'Twitter API keys and tokens are not set.', THEME_SLUG ) . '</li>';
			}
			$output .= '</ul>';
		} else {
			$output = $twt_obj['error'];
		}
	} else {
		$output = 'Twitter feed is currently turned off. You may turn it on and set the API Keys and Tokens using <a href="/wp-admin/admin.php?page=_options&tab=6" target="_blank">Theme Options -> Social Options: Enable Twitter Feed</a>.';
	}
	return $output;
}
add_shortcode( 'pb_tweet', 'cws_pb_tweet_shortcode' );

function cws_pb_limit_text($str, $limit) {
	if (str_word_count($str, 0) > $limit) {
		$words = str_word_count($str, 2);
		$pos = array_keys($words);
		$str = substr($str, 0, $pos[$limit]) . '...';
	}
	return $str;
}

function cws_tab_item_handler ( $title, $content, $open ){
	array_push( $GLOBALS['tabs'], array('title'=>$title,'open'=>$open) );
	array_push( $GLOBALS['tab_items_content'], $content );
}

if (!$pbf_redeclares || !in_array('cws_accordion_renderer', $pbf_redeclares) ) {
	function cws_accordion_renderer ( $args ){
		extract($args);
		if ( isset( $atts['toggle'] ) ) return cws_toggle_renderer ($args);
		$type2 = isset($atts['alt_style']) && '1' == $atts['alt_style'] ? ' type-2' : '';
		$out = "<section class='cws_widget_content accordion_widget" . ( !empty($atts['e_style']) ? " " . $atts['e_style'] : "" ) . $type2 . "'>";
		$out .= do_shortcode($content);
		$out .= "</section>";
		return $out;
	}
}

if (!$pbf_redeclares || !in_array('cws_callout_renderer', $pbf_redeclares) ) {
	function cws_callout_renderer ($args) {
		extract(shortcode_atts(
			array(
				'iconfa' => '',
				'iconimg' => '',
				'title' => '',
				'c_btn_text' => __('Click Me', THEME_SLUG),
				'c_btn_href' => '#',
			), $args['atts']));

		return do_shortcode( '[cws_cta icon="' . $iconfa . '" img="' . $iconimg . '" title="' . $title . '" button_text="' . $c_btn_text . '" link="' . $c_btn_href . '"]' .
			$args['content'] . '[/cws_cta]');
	}
}

function cws_title_renderer($args) {
	extract(shortcode_atts(
		array('title' => ''), $args['atts']));
	$cont = str_replace(']', ' title="' . $title . '"]', $args['content']);
	return do_shortcode($cont);
}

if (!$pbf_redeclares || !in_array('cws_accordion_item_renderer', $pbf_redeclares) ) {
	function cws_accordion_item_renderer ( $title, $content, $open, $iconfa, $iconimg ){
		$featured = !empty( $iconfa ) || !empty( $iconimg );
		if (function_exists('cws_get_option')) {
			$font_array = cws_get_option('body-font');
			if ($font_array) {
				$iconimg_size = round( $font_array['font-size'] * 1.14 * 1.5 );
			} else {
				$iconimg_size = 32;
			}
		} else {
			$iconimg_size = 32;
		}
		$out = "<div class='accordion_section" . ( $open ? " active" : "" ) . ( $featured ? " featured" : "" ) . "'>";
		$out .= "<div class='accordion_title'>" . ( $featured ? ( !empty( $iconfa ) ? "<i class='acc_featured_icon fa fa-$iconfa'></i>" : ( !empty( $iconimg ) ? "<span class='acc_featured_img'><img width='$iconimg_size' height='$iconimg_size' src='" . ( bfi_thumb( $iconimg, array( 'width' => $iconimg_size . 'px', 'height' => $iconimg_size . 'px' ) ) ) . "' /></span>" : "" ) ) : "" ) . "$title<i class='accordion_icon'></i></div>"; // TITLE
		$out .= "<div class='accordion_content'" . ( $open ? "" : " style='display: none;'" ) . ">" . do_shortcode($content) . "</div>";
		$out .= "</div>";
		return $out;
	}
}

if (!$pbf_redeclares || !in_array('cws_toggle_renderer', $pbf_redeclares) ) {
	function cws_toggle_renderer ($args){
		extract( $args );
		$out = "<section class='cws_widget_content toggle_widget" . ( !empty($atts['e_style']) ? " " . $atts['e_style'] : "" ) . "'>";
		$out .= do_shortcode( $content );
		$out .= "</section>";
		return $out;
	}
}

?>