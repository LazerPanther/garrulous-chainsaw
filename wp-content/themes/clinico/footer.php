<?php

	// $gen_sets = theme_get_option('general', 'gen_sets');
?>
	<!--Start Footer-->
	<?php
		global $sidebars_widgets;
		if ( (!is_404()) && (!empty($post)) ) {
			$cws_stored_meta = get_post_meta( $post->ID, 'cws-mb' );
			if (isset( $cws_stored_meta[0]['cws-mb-sb_foot_override'] )) {
				$footer_sb_top = $cws_stored_meta[0]['cws-mb-footer-sidebar-top'];
				$footer_sb_bottom = $cws_stored_meta[0]['cws-mb-footer-sidebar-bottom'];
			} else {
				$footer_sb_top = cws_get_option('footer-sidebar-top');
				$footer_sb_bottom = cws_get_option('footer-sidebar-bottom');
			}
		} else {
			$footer_sb_top = cws_get_option('footer-sidebar-top');
			$footer_sb_bottom = cws_get_option('footer-sidebar-bottom');
		}
	?>
	<footer class="page_footer">
		<div id="scrollup"><i class='fa fa-angle-double-up'></i></div>
		<?php 
		$footer_section_class = "footer_part";
		$sidebar_area_class = "footer_sidebar_area";
		if ($footer_sb_top){
		 	echo "<div class='footer-top-part $footer_section_class'><div class='container'><div class='footer_sb_container'><div class='$sidebar_area_class'>";
		 	dynamic_sidebar($footer_sb_top);
		 	echo "</div></div></div></div>";
 		}
		?>
		<?php
 		if ($footer_sb_bottom){
 		 	echo "<div class='footer-bottom-part $footer_section_class'><div class='container'><div class='$sidebar_area_class" . ( cws_is_wpml_active() && cws_show_flags_in_footer() ?  " with_flags" : "" ) . " clearfix'>";
 		 	if ( cws_is_wpml_active() && cws_show_flags_in_footer() ){
 		 		echo "<div class='footer_language_bar'>";
 		 		do_action('icl_language_selector');
 		 		echo "</div>";
 		 	}
 		 	dynamic_sidebar($footer_sb_bottom);
 		 	echo "</div></div></div>";
 		}
 		?>
	</footer>
	<!--End Footer-->
	<?php
		// Google Analytics' code
		$ga_event = cws_get_option('ga-event-tracking');
		echo !empty($ga_event) ? '<script type="text/javascript">' . $ga_event . '</script>' : '';
		$boxed_layout = ('0' != cws_get_option('boxed-layout') ) ? 'boxed' : '';
		echo $boxed_layout ? "</div>" : "";
		wp_footer();
	?>
	</body>
</html>
