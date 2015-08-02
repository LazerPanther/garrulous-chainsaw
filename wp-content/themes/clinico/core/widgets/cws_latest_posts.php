<?php
	/**
	 * Latest Posts Widget Class
	 */
class CWS_Latest_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_cws_recent_entries', 'description' => __( 'CWS most recent posts', THEME_SLUG) );
		parent::__construct('cws-recent-posts', __('CWS Recent Posts', THEME_SLUG), $widget_ops);
		$this->alt_option_name = 'widget_cws_recent_entries';

	}

	function widget($args, $instance) {

		extract($args);
		extract($instance);
		$img_width = isset($instance['img_width']) ? absint($instance['img_width']) : 65;

		$query_args = array( 'posts_per_page' => $post_count, 'cat' => $category, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'nopaging' => true );
		$tech_cat = cws_get_option('tech-category');
		$tech_cat = isset( $tech_cat ) ? $tech_cat : array();
		if ( count( $tech_cat ) > 0 ){
			for ( $i = 0; $i < count( $tech_cat ); $i ++ ){
				$tech_cat[$i] = '-' . $tech_cat[$i];
			}
			$tech_cat = implode( ',', $tech_cat );
			$query_args["cat"] = $tech_cat . "," . $query_args["cat"];
		}
		$r = new WP_Query( apply_filters( 'widget_posts_args', $query_args ) );
		if ($r->have_posts()) :
?>
		<?php
		echo $before_widget;

		/* ICON OUTPUT */
		$args = array("title_select"=>$title_select,"title_fa"=>$title_fa,"title_img"=>$title_img,"img_width"=>$img_width);
		cws_widget_icon_rendering($args);
		/* ICON OUTPUT */

		$outer_num = $r->post_count<$post_count ? $r->post_count : $post_count;
		$number = $number || $outer_num;
		$carousel = $outer_num <= $number ? false : true;
		?>
		<div class="cws-widget-content <?php echo (isset($backlight)&&($backlight)=='on') ? 'backlight' : ''; ?>">
			<?php
			if ($carousel):
				echo "<div class='carousel_header clearfix'>";
				echo $r->post_count ? "<div class='widget_carousel_nav'><i class='prev fa fa-angle-left'></i><i class='next fa fa-angle-right'></i><div class='clearfix'></div></div>" : "" ;
			endif;
			echo !empty( $title ) ? $before_title . $title . $after_title : "";
			if ($carousel) echo "</div>";
			if ($carousel) echo "<div class='carousel_content clearfix'>";
			?>
			<section class="<?php echo $carousel ? 'widget_carousel' : ''; ?>">
				<?php
					while ($r->have_posts() && ($outer_num>0)) :
				?>
					<div>
						<ul class="post-list">
						<?php
							$inner_num = $number;
							while ( ($inner_num>0) && ($outer_num>0) ) :
								$r->the_post();
								$current_post = get_permalink();
						?>
							<li>
								<figure>
									<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
										if ( $image ) : ?>
										<img src="<?php echo bfi_thumb($image[0], array('width' => 70, 'height' => 70) ); ?>" alt="<?php echo esc_attr( get_the_title() ? get_the_title() : get_the_ID() ); ?>">
									<?php endif; ?>
									<a href="<?php echo $current_post; ?>" class="post-title">
									<?php
									$title = esc_attr( get_the_title() ? get_the_title() : get_the_ID() );
									$sep_pos = stripos($title,'|');
									if ($sep_pos){
										echo substr($title,0,$sep_pos);
									}
									else{
										echo $title;
									}
									?>
									</a>
									<p>
									<?php
										$content = strip_shortcodes(strip_tags(get_the_content('',false)));
										$visible_content = substr($content,0,(int)$count_chars);
										echo $visible_content;
										if (strlen($visible_content) < strlen($content)){
											echo " <a class='more' href='$current_post'></a>";
										}
									?>
									</p>
									<?php if ( $show_date ) : ?>
										<p class="time-post"><?php echo get_the_date(); ?></p>
									<?php endif; ?>
								</figure>
							</li>
							<?php if ($inner_num === 1) : ?>
							<?php endif; ?>
							<?php $inner_num --;
								  $outer_num --; ?>
						<?php endwhile; ?>
						</ul>
					</div>
				<?php endwhile; ?>
			</section>
			<?php if ($carousel) echo "</div>"; ?>
		</div>
		<?php echo $after_widget;
		?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['category'] = (int) $new_instance['category'];
		$instance['number'] = (int) $new_instance['number'];
		$instance['count_chars'] = (int) $new_instance['count_chars'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		$instance['post_count'] = (int) $new_instance['post_count'];
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
		$title	 = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : __('Latest Posts', THEME_SLUG);
		$category  = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : NULL;
		$post_count = isset( $instance['post_count'] ) ? absint( $instance['post_count'] ) : 8;
		$number	= isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
		$count_chars	= isset( $instance['count_chars'] ) ? absint( $instance['count_chars'] ) : 50;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
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

		$cats = get_terms( 'category' );
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

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Posts per slide:', THEME_SLUG); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo $number; ?>" size="3" /></p>

		<p><label for="<?php echo $this->get_field_id( 'post_count' ); ?>"><?php _e('Posts to show:', THEME_SLUG); ?></label>
		<input id="<?php echo $this->get_field_id( 'post_count' ); ?>" name="<?php echo $this->get_field_name( 'post_count' ); ?>" type="number" value="<?php echo $post_count; ?>" size="3" /></p>

		<p><label for="<?php echo $this->get_field_id( 'count_chars' ); ?>"><?php _e('Description length:', THEME_SLUG); ?></label>
		<input id="<?php echo $this->get_field_id( 'count_chars' ); ?>" name="<?php echo $this->get_field_name( 'count_chars' ); ?>" type="number" value="<?php echo $count_chars; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e('Show post date', THEME_SLUG); ?></label></p>

		<p><input id="<?php echo $this->get_field_id('backlight'); ?>" name="<?php echo $this->get_field_name('backlight'); ?>" type="checkbox" <?php echo (isset($backlight) && ($backlight=="on")) ? "checked" : '' ?> />
		<label for="<?php echo $this->get_field_id('backlight'); ?>"><?php _e('Highlight this widget', THEME_SLUG); ?></label></p>
<?php
	}

}

?>