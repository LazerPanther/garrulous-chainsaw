<?php
	/**
	 * Footer Posts Widget Class
	 */
class CWS_Twitter extends WP_Widget {

	function __construct() {

		$widget_ops = array('classname' => 'widget_cws_twitter', 'description' => __("Clinico Footer post",  THEME_SLUG) );
		parent::__construct('cws_twitter', __('CWS Twitter',  THEME_SLUG), $widget_ops);
	}

	function widget($args, $instance) {

		extract($args);
		extract($instance);

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __('Latest Posts',  THEME_SLUG);
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$img_width = isset($instance['img_width']) ? absint($instance['img_width']) : 65;

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 4; // default value
		$visible_num = isset( $instance['visible_num'] ) ? absint( $instance['visible_num'] ) : 2;

		echo $before_widget;

		/* ICON OUTPUT */
		$args = array("title_select"=>$title_select,"title_fa"=>$title_fa,"title_img"=>$title_img,"img_width"=>$img_width);
		cws_widget_icon_rendering($args);
		/* ICON OUTPUT */

		$shortcode = "[twitter tweets='$number' visible='$visible_num' title='$title' before_title='$before_title' after_title='$after_title' sidebar='true' ";
		$shortcode .= (isset($backlight)&&($backlight)=='on') ? "backlight='on' ]" : "" . "]";

		echo do_shortcode( $shortcode );

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['visible_num'] = (int)$new_instance['visible_num'];
		$instance['backlight'] = $new_instance['backlight']; 

		/* ICON VARIABLES */
		$instance['title_select'] = $new_instance['title_select'];
		$instance['title_fa'] = strip_tags($new_instance['title_fa']);
		$instance['title_img'] = strip_tags($new_instance['title_img']);
		$instance['img_width'] = empty($new_instance['img_width']) ? 65 : $new_instance['img_width'];		
		$instance['show_icon_options'] = $new_instance['show_icon_options'];
		/* ICON VARIABLES */

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_cws_twitter']) )
			delete_option('widget_cws_twitter');

		return $instance;
	}

	function form( $instance ) {
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
		$visible_num = isset( $instance['visible_num'] ) ? absint( $instance['visible_num'] ) : 2;
		$title 	= isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Twitter';
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
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php __('Title:', THEME_SLUG); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p>
		<input type="checkbox" class="show_icon_options" id="<?php echo $this->get_field_id('show_icon_options'); ?>" name="<?php echo $this->get_field_name('show_icon_options'); ?>" <?php echo $show_icon_options == 'on' ? 'checked' : ''; ?> />
		<label for="<?php echo $this->get_field_id('show_icon_options'); ?>"><?php _e("Show icon options", THEME_SLUG); ?></label>
		</p>

		<!-- ICON SELECTION -->
				<?php $args = array('title_select'=>$title_select,'title_fa'=>$title_fa,'title_img'=>$title_img,'thumb_url'=>$thumb_url,'img_width'=>$img_width,'display_none'=>$display_none,'show_icon_options'=>$show_icon_options,'_this'=>$this,'all'=>true);
				cws_widget_icon_selection($args);
				?>
		<!-- ICON SELECTION -->

		<p><label for="<?php echo $this->get_field_id('number') ?>"><?php _e( 'Tweets to show:', THEME_SLUG ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('visible_num') ?>"><?php _e( 'Tweets per slide:', THEME_SLUG ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'visible_num' ); ?>" name="<?php echo $this->get_field_name( 'visible_num' ); ?>" type="text" value="<?php echo $visible_num; ?>" /></p>

		<p><input id="<?php echo $this->get_field_id('backlight'); ?>" name="<?php echo $this->get_field_name('backlight'); ?>" type="checkbox" <?php echo (isset($backlight) && ($backlight=="on")) ? "checked" : '' ?> />
		<label for="<?php echo $this->get_field_id('backlight'); ?>"><?php _e('Highlight this widget', THEME_SLUG); ?></label></p>
<?php
	}
}
?>