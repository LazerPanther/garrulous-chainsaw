<?php get_header(); wpm_header(); // wpm_header_hook
if(ot_get_option( 'disable_home_slider' ) !=='off'){ ?>

<div class="container slider_container">
	<div class="row slider_row">
		<?php get_template_part( 'templates/slider/3_left', '' ); ?>
    </div>
</div>                
<?php } 

get_template_part( 'templates/home/call', '' ); 
get_template_part( 'templates/home/featured', '' ); 
get_template_part( 'templates/home/widget', '' );
get_template_part( 'templates/home/blog_right', '' );

wpm_footer(); // wpm_footer_hook 
get_footer(); ?>