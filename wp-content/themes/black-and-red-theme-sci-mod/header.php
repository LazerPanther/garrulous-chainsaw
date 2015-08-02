<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="description" content="<?php if (is_single() || is_page()) {
		# Single post / page.
		# Use the 'description' custom field.
		echo get_post_meta($post->ID, 'description', true);
	} elseif (is_category()) {
		# Category page.
		# Use category's description
		echo trim(strip_tags(category_description()));
	} else {
		# Default meta description
		# Blog's description
		echo bloginfo('description');
	} ?> " />

<meta name="keywords" content="chiropractic, chiropractor, chiropractic cary, chiropractic raleigh, chiropractor raleigh, chiropractor cary, auto accident, auto accident raleigh, auto accident cary, car accident, car accident raleigh, back pain, neck pain, headache, headaches, migraine, weight loss, nutrition, detoxification, whiplash, car accident cary, sports therapy, physical therapy, holistic medicine, health and wellness, health and fitness, Dr. Mercola, foot detox, ionic foot detox">

	<!--<title><?php bloginfo('name'); ?><?php //wp_title(); ?></title>-->
	<title><?php if (function_exists('seo_title_tag')) {     seo_title_tag(); } else { bloginfo('name'); wp_title();} ?></title>

	<style type="text/css" media="screen">
		@import url( <?php bloginfo('stylesheet_url'); ?> );
	</style>

	<!--[if gte IE 6]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/ie.css">
	<![endif]-->

	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php bloginfo('atom_url'); ?>" />

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>
</head>

<body>
<div id="content-wrapper">
<div class="search">
	<div class="menu_search">
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	</div>
	<div class="clear"></div>
</div>
<div id="header"><div class="himg">
	<h1><a href="<?php bloginfo('url'); ?>/" target="_self"><?php bloginfo('name'); ?></a></h1>
	<!--<h4><?php // bloginfo('description'); ?></h4>-->
</div></div>

<div id="menu">
	<div class="menu_items">		
		<ul>
            <li<?php if (is_home()) { echo ' class="current_page_item"'; } ?>><a href="<?php bloginfo('url'); ?>/">home</a></li>
            <?php wp_list_pages('title_li=' ); ?>			
		</ul>
	</div>
	
	<div class="clear"></div>
</div>


<!-- end header -->
