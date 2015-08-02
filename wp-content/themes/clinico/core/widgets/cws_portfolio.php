<?php
	/**
	 * Latest Posts Widget Class
	 */
class CWS_Portfolio extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_cws_recent_pictures', 'description' => __( 'Latest Portfolio Items', THEME_SLUG) );
		parent::__construct('cws-recent-pictures', __('CWS Portfolio', THEME_SLUG), $widget_ops);
		$this->alt_option_name = 'widget_cws_recent_pictures';
	}

	function widget($args, $instance) {

		extract($args);
		extract($instance);
		
		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __('Latest Pictures', THEME_SLUG);
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$img_width = isset($instance['img_width']) ? absint($instance['img_width']) : 65;
		
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 4; // default value

		ob_start();
		echo $before_widget;

		/* ICON OUTPUT */
		$args = array("title_select"=>$title_select,"title_fa"=>$title_fa,"title_img"=>$title_img,"img_width"=>$img_width);
		cws_widget_icon_rendering($args);
		/* ICON OUTPUT */

		$cat_name = !empty($category) ? get_term_by( "id", $category, "cws-portfolio-type" )->name : "";

		$query_args = array(
			'post_type' => 'portfolio', 'cws-portfolio-type' => $cat_name, 'post_status' => 'published', 'posts_per_page' => -1,
		);
		$items = new WP_Query( $query_args );
		$carousel = $items->post_count>1 ? true : false;
		?>

		<div class="cws-widget-content <?php echo (isset($backlight)&&($backlight)=='on') ? 'backlight' : ''; ?>">
			<div class='carousel_header clearfix'>
			<?php echo $carousel ? "<div class='widget_carousel_nav'><i class='prev fa fa-angle-left'></i><i class='next fa fa-angle-right'></i><div class='clearfix'></div></div>" : "" ; ?>
			<?php if (!empty( $title )) echo $before_title . $title . $after_title; ?>
			</div>
			<div class='photo_tour carousel_content'>
				<?php echo $carousel ? "<section class='widget_carousel'>" : "";
					$counter = 0;
					if ( $items->have_posts() ) :
						$gallery_id = uniqid( 'cws-gallery-' );
						while ( ( $items->have_posts() ) && ( $counter<$number ) ) :
							$items->the_post();
							$full =  wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ), 'full' );
							$full_url = $full[0];
							if ( !empty($full_url) ):
								$cws_stored_meta = get_post_meta( get_the_id(), 'cws-portfolio' );
								$thumb_url =  bfi_thumb( $full_url, array(  'width'=>270, 'height'=>270, 'crop'=>true ) );
								$counter ++;
								?>
								<div class='item'>
									<div class="pic">
										<?php echo "<img src='$thumb_url' alt /><div class='hover-effect'></div><div class='links'><a" . ( $carousel ? " data-fancybox-group='$gallery_id'" : "" ) . " href='$full_url' class='fancy" . ( $carousel ? " fancy_gallery" : "" ) . " fa fa-photo'></a></div>" ;?>
									</div>
									<div class='portfolio_item_info'>
										<?php 
											$title = get_the_title();
											$link = get_the_permalink();
											echo !empty($title) ? "<div class='name'><a href='$link'>$title</a></div>" : "";
											$short_desc = !empty( $cws_stored_meta[0]['cws-portfolio-short_desc'] ) ? $cws_stored_meta[0]['cws-portfolio-short_desc'] : "";
											echo !empty( $short_desc ) ? substr( $short_desc, 0, 60 ) : cws_post_content_output( 60 );
										?>
									</div>
								</div>
							<?php
							endif;
						endwhile;
					endif;
				echo $carousel ? "</section>" : ""; ?>
			</div>
		</div>
		<?php echo $after_widget;
		ob_end_flush();
		?>
<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['category'] = (int) $new_instance['category'];
		$instance['number'] = $new_instance['number'];
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
		$title	 = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Latest Pictures';
		$category  = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : NULL;
		$number	= isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
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
		$cats = get_terms('cws-portfolio-type');
		
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

		<p><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e('Category:', THEME_SLUG); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
		<option value="0" label="<?php _e('All Categories', THEME_SLUG);?>" />
		<?php
		foreach ( $cats as $cat ) {
			echo "<option value='$cat->term_id'" . ( $cat->term_id == $category ? " selected='selected'" : "" ) . ">" . $cat->name . "</option>";
		}
		?>
		</select>
		
		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Slides to show:', THEME_SLUG); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo $number; ?>" size="2" /></p>

		<p><input id="<?php echo $this->get_field_id('backlight'); ?>" name="<?php echo $this->get_field_name('backlight'); ?>" type="checkbox" <?php echo (isset($backlight) && ($backlight=="on")) ? "checked" : '' ?> />
		<label for="<?php echo $this->get_field_id('backlight'); ?>"><?php _e('Highlight this widget', THEME_SLUG); ?></label></p>
<?php
	}

}

?>