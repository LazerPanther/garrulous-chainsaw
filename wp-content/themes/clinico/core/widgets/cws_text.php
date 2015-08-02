<?php
	/**
	 * Latest Posts Widget Class
	 */
class CWS_Text extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_cws_text', 'description' => __( 'Modified WP Text widget', THEME_SLUG) );
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('cws-text', __('CWS Text', THEME_SLUG), $widget_ops);
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
			<?php if ( isset( $title ) && !empty( $title ) ) echo $before_title . $title . $after_title; ?>
			<section class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( do_shortcode($text) ) : do_shortcode($text); ?></section>
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
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
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

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;
			<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', THEME_SLUG); ?></label></p>

		<p><input id="<?php echo $this->get_field_id('backlight'); ?>" name="<?php echo $this->get_field_name('backlight'); ?>" type="checkbox" <?php echo (isset($backlight) && ($backlight=="on")) ? "checked" : '' ?> />
		<label for="<?php echo $this->get_field_id('backlight'); ?>"><?php _e('Highlight this widget', THEME_SLUG); ?></label></p>

<?php
	}
}

?>