<?php
	add_action( 'add_meta_boxes', 'cws_add_metaboxes' );

	function cws_add_metaboxes()
	{
		add_meta_box( 'cws-page-metabox-id', 'CWS Page Options', 'cws_mb_page_callback', 'page', 'normal', 'high' );
	}

	function cws_mb_page_callback( $post )
	{
		$cws_stored_meta = get_post_meta( $post->ID, 'cws-mb' );
		$sb_layout = isset( $cws_stored_meta[0]['cws-mb-sb_layout'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-sb_layout'] ) : 'default';
		$blogtype_selected = $sb_layout == 'default' ? 'medium' : ( isset( $cws_stored_meta[0]['cws-mb-blogtype'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-blogtype'] ) : 'small' );
		$sb_override = isset( $cws_stored_meta[0]['cws-mb-sb_override'] );
		$sidebar1 = isset( $cws_stored_meta[0]['cws-mb-sidebar1'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-sidebar1'] ) : '';
		$sidebar2 = isset( $cws_stored_meta[0]['cws-mb-sidebar2'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-sidebar2'] ) : '';
		$category = isset( $cws_stored_meta[0]['cws-mb-category'] ) ? $cws_stored_meta[0]['cws-mb-category'] : '';
		$sb_is_blog = isset( $cws_stored_meta[0]['cws-mb-is_blog'] );
		$sb_foot_override = isset( $cws_stored_meta[0]['cws-mb-sb_foot_override'] );
		$footer_sidebar_top = isset( $cws_stored_meta[0]['cws-mb-footer-sidebar-top'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-footer-sidebar-top'] ) : '';
		$footer_sidebar_bottom = isset( $cws_stored_meta[0]['cws-mb-footer-sidebar-bottom'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-footer-sidebar-bottom'] ) : '';
		$slider = isset( $cws_stored_meta[0]['cws-mb-slider'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-slider'] ) : '';
		$sb_slider_override = isset( $cws_stored_meta[0]['cws-mb-sb_slider_override'] );
		$pinterest_layout = isset($cws_stored_meta[0]["cws-mb-pinterest_layout"]) ? $cws_stored_meta[0]["cws-mb-pinterest_layout"] : '';
		$pinterest_layout_override = $blogtype_selected == 'pinterest' ? true : false;

		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$redux_img = get_template_directory_uri() . '/framework/rc/assets/img/';

		$showhide = ($sb_layout == 'left') || ($sb_layout == 'right') || ($sb_layout == 'both') ? 'show' : 'hide';

		$post_mbhtml_attr = array(
			'page-meta'=>array(
				'options' => array(
					array(
						'id' => 'sb_layout',
						'title' => __('Sidebar Position',THEME_SLUG),
						'type' => 'image_select',
						'options' => array(
							'default' => array('title' => __('Default', THEME_SLUG), 'img' => $redux_img .'default.png', 'hide' => array('sidebar1', 'sidebar2') ),
							'left' => array('title' => __('Left', THEME_SLUG), 'img' => $redux_img .'left.png', 'show' => array('sidebar1'), 'hide' => array('sidebar2') ),
							'right' => array('title' => __('Right', THEME_SLUG), 'img' => $redux_img .'right.png', 'show' => array('sidebar1'), 'hide' => array('sidebar2') ),
							'both' => array('title' => __('Double', THEME_SLUG), 'img' => $redux_img .'both.png', 'show' => array('sidebar1', 'sidebar2') ),
							'none' => array('title' => __('None', THEME_SLUG), 'img' => $redux_img .'none.png', 'hide' => array('sidebar1', 'sidebar2') )
						),
						'default' => $sb_layout,
						'w' => '100%',
					),
					array(
						'id' => 'sidebar1',
						'hidden' => ($sb_layout != 'left') && ($sb_layout != 'right') && ($sb_layout != 'both'),
						'title' => __('Select a sidebar',THEME_SLUG),
						'type' => 'select',
						'source' => 'sidebars',
						'default' => $sidebar1,
						'w' => '50%'
					),
					array(
						'id' => 'sidebar2',
						'hidden' => ($sb_layout != 'both'),
						'title' => __('Select right sidebar',THEME_SLUG),
						'type' => 'select',
						'source' => 'sidebars',
						'default' => $sidebar2,
						'w' => '50%'
					),
					array(
						'id' => 'sb_override',
						'type' => 'check',
						'title' => __('This page is a Blog List:',THEME_SLUG),
						'hide' => array('blogtype', 'category[]', 'pinterest_layout'),
						'source' => array('' => ''),
						'default' => array($sb_override),
					),
					array(
						'id' => 'blogtype',
						'hidden' => ( (!$sb_override) || ($sb_layout=='default') ),
						//'depend' => array('category'),
						'type' => 'image_select',
						'title' => __('Blog Layout',THEME_SLUG),
						'options' => array(
							//'none' => array('title' => __('None', THEME_SLUG), 'img' => $redux_img .'default.png', 'hide' => array('category[]') ),
							'small' => array('title' => __('Small', THEME_SLUG), 'img' => $redux_img .'small.png', 'hide' => array('pinterest_layout') ),
							'medium' => array('title' => __('Medium', THEME_SLUG), 'img' => $redux_img . 'medium.png', 'hide' => array('pinterest_layout') ),
							'large' => array('title' => __('Large', THEME_SLUG), 'img' => $redux_img .'large.png', 'hide' => array('pinterest_layout') ),
							'pinterest' => array('title' => __('Pinterest', THEME_SLUG), 'img' => $redux_img .'pinterest.png', 'show' => array('pinterest_layout') )
						),
						'default' => $blogtype_selected,
						'w' => '250px',
					),
					array(
						'id' => 'pinterest_layout',
						'title' => __('Columns',THEME_SLUG),
						'hidden' => ( ( !$sb_override ) || ( $blogtype_selected != 'pinterest' ) || ( $sb_layout=='default' ) ),
						'type' => 'image_select',
						'options' => array(
									'2' => array('title' =>  __('Two',THEME_SLUG), 'img' => $redux_img . 'pinterest_2_columns.png'),
									'3' => array('title' => __('Three',THEME_SLUG), 'img' => $redux_img . 'pinterest_3_columns.png'),
									'4' => array('title' => __('Four',THEME_SLUG), 'img' => $redux_img . 'pinterest_4_columns.png'),
									),
						'default' => $pinterest_layout ? $pinterest_layout : '2',
						'w' => '250px',
					),
					array(
						'id' => 'category',
						'hidden' => !$sb_override,
						'title' => __('Category',THEME_SLUG),
						'desc' => __('Blog categories',THEME_SLUG),
						'type' => 'select',
						'select' => 'multiple',
						'source' => 'categories',
						'default' => $category,
						'w' => '100%'
					),
					array(
						'id' => '',
						'title' => __('Customize footer for this page:',THEME_SLUG),
						'type' => 'group',
						'source' => array(
							array(
								'id' => 'sb_foot_override',
								'type' => 'check',
								'hide' => array('footer-sidebar-top', 'footer-sidebar-bottom'),
								'source' => array('' => ''),
								'default' => array($sb_foot_override),
							),
							array(
								'id' => 'footer-sidebar-top',
								'hidden' => !$sb_foot_override,
								'title' => __('Select Footer sidebar',THEME_SLUG),
								'type' => 'select',
								'source' => 'sidebars',
								'default' => $footer_sidebar_top,
								'w' => '250px'
							),
							array(
								'id' => 'footer-sidebar-bottom',
								'hidden' => !$sb_foot_override,
								'title' => __('Select Copyrights sidebar',THEME_SLUG),
								'type' => 'select',
								'source' => 'sidebars',
								'default' => $footer_sidebar_bottom,
								'w' => '250px'
							),
						),
						'w' => '100%'
					),
					array(
						'id' => '',
						'title' => __('Show Slider on this page:',THEME_SLUG),
						'type' => 'group',
						'source' => array(
							array(
								'id' => 'sb_slider_override',
								'type' => 'check',
								'hide' => array('slider'),
								'source' => array('' => ''),
								'default' => array($sb_slider_override),
							),
							array(
								'id' => 'slider',
								'hidden' => !$sb_slider_override,
								'desc' => 'Slider shortcode',
								'type' => 'text',
								'default' => $slider,
								'w' => '90%'
							),
						),
						'w' => '100%',
						),
					),
				),
			);
		?>

		<section class="cws-page-section">
			<?php echo cws_shortcode_html_gen($post_mbhtml_attr, 'page-meta', 0, '', false); ?>
		</section>
		<?php
	}

	/*function cws_script_enqueue( $hook ) {
		global $typenow;

		wp_register_script('pb', THEME_URI . '/core/js/pb.js');
		wp_register_script('yui', THEME_URI . '/core/yui/yui/yui.js');
		if( $typenow == 'page' ) {
			wp_enqueue_media();
			wp_enqueue_script( 'yui' );
			wp_enqueue_script( 'pb' );
			wp_enqueue_style( 'cws-pb', THEME_URI . '/core/css/cws-pb.css' );

		}
		if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
		}
	}

	add_action( 'admin_enqueue_scripts', 'cws_script_enqueue' );

	add_filter('the_editor', 'cws_content');

function cws_content( $content ) {
	preg_match("/<textarea[^>]*id=[\"']([^\"']+)\"/", $content, $matches);
	$id = $matches[1];
	if( $id !== "content" )
		return $content;
	ob_start();
	include( THEME_DIR . '/core/pb.php' );
	return $content . ob_get_clean();
}*/

	add_action( 'save_post', 'cws_metabox_save', 11, 2 );

	function cws_metabox_save( $post_id, $post )
	{
		if ( "page" == $post->post_type ) {
			// Bail if we're doing an auto save
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

			// if our nonce isn't there, or we can't verify it, bail
			if ( !isset( $_POST['mb_nonce']) || !wp_verify_nonce($_POST['mb_nonce'], 'cws_mb_nonce') ) return;

			// if our current user can't edit this post, bail
			if ( !current_user_can( 'edit_post', $post->ID ) )
			return;

			// now we can actually save the data
			$allowed = array(
				'a' => array( // on allow a tags
					'href' => array() // and those anchors can only have href attribute
				)
			);

			foreach($_POST as $key => $value) {
				if (0 === strpos($key, 'cws-mb-')) {
					$save_array[$key] = $value;
					update_post_meta($post_id, 'cws-mb', $save_array);
				}
			}
		}
	}
?>