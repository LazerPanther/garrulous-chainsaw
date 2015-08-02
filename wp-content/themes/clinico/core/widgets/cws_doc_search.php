<?php // Find a Doctor Widget
	class CWS_Doc_Search extends WP_WIDGET {
		function __construct(){
			$widget_ops = array('classname' => 'widget_cws_doc_search', 'description' => __( "Doctor's search widget"  , THEME_SLUG) );
			$control_ops = array('width' => 400, 'height' => 350);
			parent::__construct('doc-search', __("CWS Doctor's Search ", THEME_SLUG), $widget_ops);
		}
		function widget( $args, $instance ) {

			extract( $args );
			extract( $instance );

			$title = apply_filters( 'widget_title', empty( $title ) ? '' : $title, $instance, $this->id_base );
			$text = apply_filters( 'widget_text', empty( $text ) ? '' : $text, $instance );
			$img_width = isset($instance['img_width']) ? absint($instance['img_width']) : 65;

			echo $before_widget;

			/* ICON OUTPUT */
			$args = array("title_select"=>$title_select,"title_fa"=>$title_fa,"title_img"=>$title_img,"img_width"=>$img_width);
			cws_widget_icon_rendering($args);
			/* ICON OUTPUT */

			?>
			<div class="cws-widget-content doc_search <?php echo (isset($backlight)&&($backlight)=='on') ? 'backlight' : ''; ?>">
				<?php if (!empty( $title )) echo $before_title . $title . $after_title;
					global $wpdb;
					$docs = $wpdb->get_col("SELECT post_title FROM {$wpdb->posts} WHERE post_type = 'staff'");
					wp_enqueue_script('jquery-ui-autocomplete');
					$docs_arr_js = "var docs = ['" . implode("','", $docs) . "'];";
				?>
				<script>
				jQuery(function(){

				<?php echo $docs_arr_js; ?>					
					jQuery("#docname").autocomplete({
						source: docs,
						messages: {noResults: '', results: function() {} },
						appendTo: '.search_field.by_name'
 				    })
				});
				</script>
				<section class="find_a_doctor">
					<form role="search" method="get" class="doctors-search-form" id="quick-search" action="<?php echo home_url(); ?>">
						<input type="hidden" name="asearch" value="1">
						<div class="search_field by_name">
							<input type="text" placeholder="<?php _e('Search by name', THEME_SLUG); ?>" id="docname" name="docname">
						</div>
						<div class="search_field by_treatment">
							<select name="cws-stafftreatments">
								<?php
									$depts = get_terms('cws-staff-treatments');
									echo '<option value="" disabled selected>'. __("Select treatment", THEME_SLUG) . '</option>';
									foreach ($depts as $dept=>$v) {
										echo '<option value="' . $v->slug . '">' . $v->name . '</option>'	;
									}
								?>
							</select>
						</div>
						<div class='submit_field'>
							<button type="submit"><?php _e("Search", THEME_SLUG); ?></button>
						</div>
					</form>
				</section>
			</div>
			<?php
			echo $after_widget;
		}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['backlight'] = $new_instance['backlight'];
		/* ICON VARIABLES */
		$instance['title_select'] = $new_instance['title_select'];
		$instance['title_fa'] = strip_tags($new_instance['title_fa']);
		$instance['title_img'] = strip_tags($new_instance['title_img']);
		$instance['img_width'] = empty($new_instance['img_width']) ? 65 : $new_instance['img_width'];
		$instance['show_icon_options'] = $new_instance['show_icon_options'];
		/* ICON VARIABLES */
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$backlight = isset( $instance['backlight'] ) ? $instance['backlight'] : false;
		/* ICON VARIABLES */
		$title_select = isset( $instance['title_select'] ) ? strval($instance['title_select']) : 'fa';
		$title_fa = isset( $instance['title_fa'] ) ? strip_tags($instance['title_fa']) : '';
		$title_img = isset( $instance['title_img'] ) ? strval($instance['title_img']) : '';
		$img_width = isset( $instance['img_width'] ) ? absint($instance['img_width']) : 65;		
		$display_none = ' style="display:none"';
		$thumb_url = $title_img ? '="' . wp_get_attachment_thumb_url($title_img) . '"' : '';
		$show_icon_options = isset($instance['show_icon_options']) ? $instance['show_icon_options'] : false;
		/* ICON VARIABLES */

?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', THEME_SLUG); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p>
		<input type="checkbox" class="show_icon_options" id="<?php echo $this->get_field_id('show_icon_options'); ?>" name="<?php echo $this->get_field_name('show_icon_options'); ?>" <?php echo $show_icon_options == 'on' ? 'checked' : ''; ?> />
		<label for="<?php echo $this->get_field_id('show_icon_options'); ?>"><?php _e("Show icon options", THEME_SLUG); ?></label>
		</p>

		<!-- ICON SELECTION -->
				<?php $args = array('title_select'=>$title_select,'title_fa'=>$title_fa,'title_img'=>$title_img,'thumb_url'=>$thumb_url,'img_width'=>$img_width,'display_none'=>$display_none,'show_icon_options'=>$show_icon_options,'_this'=>$this);
				cws_widget_icon_selection($args);
				?>
		<!-- ICON SELECTION -->

		<p><input id="<?php echo $this->get_field_id('backlight'); ?>" name="<?php echo $this->get_field_name('backlight'); ?>" type="checkbox" <?php echo (isset($backlight) && ($backlight=="on")) ? "checked" : '' ?> />
		<label for="<?php echo $this->get_field_id('backlight'); ?>"><?php _e('Highlight this widget', THEME_SLUG); ?></label></p>
<?php
	}
}
?>