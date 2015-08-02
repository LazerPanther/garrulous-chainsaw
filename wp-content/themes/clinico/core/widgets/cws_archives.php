<?php
	/**
	 * Latest Posts Widget Class
	 */
class CWS_Archives extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_cws_archives', 'description' => __( 'Modified WP Archives widget', THEME_SLUG) );
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('cws_archives', __('CWS Archives', THEME_SLUG), $widget_ops);
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
		<div class="cws-widget-content <?php echo (isset($backlight)&&($backlight)=='on') ? 'backlight' : ''; ?>">
			<?php if (isset( $title )) echo $before_title . $title . $after_title; ?>

			<div class='archives'>
				<?php
				$archives_args = array('format'=>'custom',
										'before'=>'<div class="archive_item">',
										'after'=>'</div>');
				if ($show_post_count) $archives_args["show_post_count"] = true;
				wp_get_archives($archives_args);
				?>
			</div>

		</div>
		<?php
		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_post_count'] = $new_instance['show_post_count'];
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
		$show_post_count = isset($instance['show_post_count']) ? $instance['show_post_count'] : "";
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

		<p><input type="checkbox" id="<?php echo $this->get_field_id('show_post_count'); ?>" name="<?php echo $this->get_field_name('show_post_count'); ?>" <?php echo $show_post_count ? "checked='checked'" : ""; ?> />
		<label for="<?php echo $this->get_field_id('show_post_count'); ?>"><?php _e("Show post count",THEME_SLUG) ?></label></p>

		<p><input id="<?php echo $this->get_field_id('backlight'); ?>" name="<?php echo $this->get_field_name('backlight'); ?>" type="checkbox" <?php echo (isset($backlight) && ($backlight=="on")) ? "checked" : '' ?> />
		<label for="<?php echo $this->get_field_id('backlight'); ?>"><?php _e('Highlight this widget', THEME_SLUG); ?></label></p>
<?php
	}
}

?>