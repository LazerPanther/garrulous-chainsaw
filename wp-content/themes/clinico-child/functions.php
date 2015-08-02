<?php

register_sidebar( array(
	'name' => 'Header Widget',
	'id' => 'header-widget',
	'before_widget' => '<div class="info-widget"><div>',
	'after_widget' => '</div></div>',
	'before_title' => '<div class="info-title"><span>',
	'after_title' => '</span></div>',
));
register_sidebar( array(
	'name' => 'Footer Widget',
	'id' => 'footer-widget',
	'before_widget' => '<div class="info-widget"><div>',
	'after_widget' => '</div></div>',
	'before_title' => '<div class="info-title"><span>',
	'after_title' => '</span></div>',
));
?>