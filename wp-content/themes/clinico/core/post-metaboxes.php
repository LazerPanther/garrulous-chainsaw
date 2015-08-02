<?php
	add_action( 'add_meta_boxes', 'cws_post_add_metaboxes' );

	function cws_post_add_metaboxes()
	{
		add_meta_box( 'cws-post-metabox-id', 'CWS Post Options', 'cws_mb_post_callback', 'post', 'normal', 'high' );
	}

	function cws_mb_post_callback( $post )
	{
		$cws_stored_meta = get_post_meta( $post->ID, 'cws-mb' );
		$gallery = isset( $cws_stored_meta[0]['cws-mb-gallery'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-gallery'] ) : '';
		$video = isset( $cws_stored_meta[0]['cws-mb-video'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-video'] ) : '';
		$audio = isset( $cws_stored_meta[0]['cws-mb-audio'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-audio'] ) : '';
		$link = isset( $cws_stored_meta[0]['cws-mb-link'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-link'] ) : '';
		$quote = isset( $cws_stored_meta[0]['cws-mb-quote'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-quote'] ) : '';
		$quote_author = isset( $cws_stored_meta[0]['cws-mb-quote-author'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-quote-author'] ) : '';
		$table_currency= isset( $cws_stored_meta[0]['cws-mb-table-currency'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-table-currency'] ) : '';
		$table_price = isset( $cws_stored_meta[0]['cws-mb-table-price'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-table-price'] ) : '';
		$table_price_description = isset( $cws_stored_meta[0]['cws-mb-table-price-description'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-table-price-description'] ) : '';
		$table_encourage = isset( $cws_stored_meta[0]['cws-mb-table-encourage'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-table-encourage'] ) : '';
		$table_order = isset( $cws_stored_meta[0]['cws-mb-table-order'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-table-order'] ) : '';
		$table_signup = isset( $cws_stored_meta[0]['cws-mb-table-signup'] ) ? esc_attr( $cws_stored_meta[0]['cws-mb-table-signup'] ) : 'Sign up!';
		$table_hilighted = isset( $cws_stored_meta[0]['cws-mb-table-hilighted'] ) ? true : false;
		wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

		$post_mbhtml_attr = array(
			'gallery'=>array(
				'options'=>array(
					array(
						'id' => 'gallery',
						'title' => __('Gallery',THEME_SLUG),
						'type' => 'text',
						'default' => $gallery,
						'w' => '50%'
					),
					array(
						'id' => 'media-button',
						'title' => '',
						'desc' => '',
						'value' => __( 'Add images', THEME_SLUG ),
						'type' => 'button',
						'w' => '20%'
					),
				)
			),
			'video'=>array(
				'options'=>array(
					array(
						'id' => 'video',
						'title' => __('URL to video file',THEME_SLUG),
						'desc' => __('Ex.: http://player.vimeo.com/video/44510157?color=ffffff', THEME_SLUG),
						'type' => 'textarea',
						'rows' => '3',
						'default' => $video,
						'w' => '100%'
					),
				)
			),
			'quote'=>array(
				'options'=>array(
					array(
						'id' => 'quote',
						'title' => __('Quote',THEME_SLUG),
						'desc' => __('Enter the quote', THEME_SLUG),
						'type' => 'textarea',
						'rows' => '6',
						'default' => $quote,
						'w' => '100%'
					),
					array(
						'id' => 'quote-author',
						'title' => __('Author',THEME_SLUG),
						'type' => 'text',
						'default' => $quote_author,
						'w' => '50%'
					),
				)
			),
			'audio'=>array(
				'options'=>array(
					array(
						'id' => 'audio',
						'title' => __('URL to audio file',THEME_SLUG),
						'desc' => __('Ex.: /wp-content/uploads/audio.mp3', THEME_SLUG),
						'type' => 'text',
						'default' => $audio,
						'w' => '100%'
					),
				)
			),
			'link'=>array(
				'options'=>array(
					array(
						'id' => 'link',
						'title' => __('URL',THEME_SLUG),
						'type' => 'text',
						'default' => $link,
						'w' => '100%'
					),
				)
			),
			'table-price'=>array(
				'options'=>array(
					array(
						'id' => 'table-currency',
						'title' => __('Currency',THEME_SLUG),
						'type' => 'text',
						'default' => $table_currency,
						'w' => '100%'
					),
					array(
						'id' => 'table-price',
						'title' => __('Price',THEME_SLUG),
						'type' => 'text',
						'default' => $table_price,
						'w' => '100%'
					),
					array(
						'id' => 'table-price-description',
						'title' => __('Price Description',THEME_SLUG),
						'type' => 'text',
						'default' => $table_price_description,
						'w' => '100%'
					),
					array(
						'id' => 'table-encourage',
						'title' => __('Encouragement',THEME_SLUG),
						'desc' => __('Encourage your customers with some simple words', THEME_SLUG),
						'type' => 'text',
						'default' => $table_encourage,
						'w' => '100%'
					),
					array(
						'id' => 'table-order',
						'title' => __('Order url',THEME_SLUG),
						'type' => 'text',
						'default' => $table_order,
						'w' => '100%'
					),
					array(
						'id' => 'table-signup',
						'title' => __('Button name',THEME_SLUG),
						'type' => 'text',
						'default' => $table_signup,
						'w' => '100%'
					),
					array(
						'id' => 'table-hilighted',
						'title' => __('Highlight this cell',THEME_SLUG),
						'type' => 'check',
						'source' => array(
							'' => '',
							),
						'default' => array($table_hilighted),
					),
				)
			),
		);
		?>

		<section id="post-gallery" class="cws-post-section">
			<?php echo cws_shortcode_html_gen($post_mbhtml_attr, 'gallery', 0, '', false); ?>
		</section>
		<section id="post-video" class="cws-post-section">
			<?php echo cws_shortcode_html_gen($post_mbhtml_attr, 'video', 0, '', false); ?>
		</section>
		<section id="post-quote" class="cws-post-section">
			<?php echo cws_shortcode_html_gen($post_mbhtml_attr, 'quote', 0, '', false); ?>
		</section>
		<section id="post-audio" class="cws-post-section">
			<?php echo cws_shortcode_html_gen($post_mbhtml_attr, 'audio', 0, '', false); ?>
		</section>
		<section id="post-link" class="cws-post-section">
			<?php echo cws_shortcode_html_gen($post_mbhtml_attr, 'link', 0, '', false); ?>
		</section>
		<section id="post-table" class="cws-post-section">
			<?php echo cws_shortcode_html_gen($post_mbhtml_attr, 'table-price', 0, '', false); ?>
		</section>
		<?php
	}

	function cws_on_post_script_enqueue($a) {
		global $typenow;
		if( $a == 'post-new.php' || $a == 'post-new.php' ) {
			wp_enqueue_media();

		// Registers and enqueues the required javascript.
			wp_register_script( 'post-metaboxes-script', THEME_URI . '/core/js/post-metaboxes.js', array( 'jquery' ) );
			wp_enqueue_script( 'post-metaboxes-script' );

			wp_register_style( 'mb_post_css', THEME_URI . '/core/css/mb-post-styles.css', false, '1.0.0' );
			wp_enqueue_style( 'mb_post_css' );
		}
	}
	add_action( 'admin_enqueue_scripts', 'cws_on_post_script_enqueue' );

	add_action( 'save_post', 'cws_post_metabox_save', 11, 2 );

	function cws_post_metabox_save( $post_id, $post )
	{
		if ( "post" == $post->post_type ) {
			// Bail if we're doing an auto save
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

			// if our nonce isn't there, or we can't verify it, bail
			if ( !isset( $_POST['mb_nonce']) || !wp_verify_nonce($_POST['mb_nonce'], 'cws_mb_nonce') ) return;

			// if our current user can't edit this post, bail
			if( !current_user_can( 'edit_post', $post->ID ) ) {
				return;
			}

			// now we can actually save the data
			$allowed = array(
				'a' => array( // on allow a tags
					'href' => array() // and those anchords can only have href attribute
				)
			);

			$pf = $_POST['post_format'];
			if ( 'table' == $pf ) {
				wp_set_post_terms($post_id, 'post-format-table', 'post_format');
			}

			$key_post_format_start = 'cws-mb-' . $_POST['post_format'];
			foreach($_POST as $key => $value) {
				if (0 === strpos($key, $key_post_format_start)) {
					$save_array[$key] = $value;
					update_post_meta($post_id, 'cws-mb', $save_array);
				}
			}
		}
	}
?>