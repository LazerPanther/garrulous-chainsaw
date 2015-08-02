<?php
add_action('init', 'cws_custom_type_register', THEME_SLUG);

function cws_custom_type_register() {

	$labels = array(
		'name' => __('Staff Members', THEME_SLUG),
		'singular_name' => __('Staff Member', THEME_SLUG),
		'add_new' => __('Add Staff Member', THEME_SLUG),
		'add_new_item' => __('Add New Staff Member', THEME_SLUG),
		'edit_item' => __('Edit Staff Member', THEME_SLUG),
		'new_item' => __('New Staff Member', THEME_SLUG),
		'view_item' => __('View Staff Member', THEME_SLUG),
		'search_items' => __('Search Staff Member', THEME_SLUG),
		'not_found' => __('No Staff Members found', THEME_SLUG),
		'not_found_in_trash' => __('No Staff Members found in Trash', THEME_SLUG),
		'parent_item_colon' => '',
		'menu_name' => __('Staff', THEME_SLUG)
	);

	$staff_slug = cws_get_option('staff_slug');
	$staff_slug = !empty($staff_slug) ? $staff_slug : 'staff';

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => true,
		'has_archive' => true,
		'rewrite' => array('slug' => $staff_slug, 'with_front' => false),
		'supports' => array(
			'title',
			'editor',
			'thumbnail',
		),
		'menu_position' => 24,
		'taxonomies' => array('cws-staff-dept', 'cws-staff-procedures', 'cws-staff-position')
	);
	register_post_type('staff', $args);

	// Portfolio

	$labels = array(
		'name' => __('Portfolio Items', THEME_SLUG),
		'singular_name' => __('Portfolio Item', THEME_SLUG),
		'add_new' => __('Add Portfolio Item', THEME_SLUG),
		'add_new_item' => __('Add New Portfolio Item', THEME_SLUG),
		'edit_item' => __('Edit Portfolio Item', THEME_SLUG),
		'new_item' => __('New Portfolio Item', THEME_SLUG),
		'view_item' => __('View Portfolio Item', THEME_SLUG),
		'search_items' => __('Search Portfolio Item', THEME_SLUG),
		'not_found' => __('No Portfolio Items found', THEME_SLUG),
		'not_found_in_trash' => __('No Portfolio Items found in Trash', THEME_SLUG),
		'parent_item_colon' => '',
		'menu_name' => __('Portfolio', THEME_SLUG)
	);

	$portfolio_slug = cws_get_option('portfolio_slug');
	$portfolio_slug = !empty($portfolio_slug) ? $portfolio_slug : 'portfolio';

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_ui' => true,
		'capability_type' => 'post',
		'hierarchical' => true,
		'rewrite' => array('slug' => $portfolio_slug, 'with_front' => false),
		'supports' => array(
			'title',
			'editor',
			'thumbnail',
		),
		'menu_position' => 23,
		//'menu_icon' => CWS_PORTFOLIO_PLUGIN_URL . '/icon.png',
		'taxonomies' => array('cws-portfolio-type', 'post_tag')
	);

	register_post_type('portfolio', $args);

	cws_portfolio_register_taxonomies();
}
/*
require_once CWS_PORTFOLIO_PLUGIN_DIR . '/widgets/cws_portfolio.php';

add_action( 'widgets_init', 'cws_portfolio_register_widget' );

function cws_portfolio_register_widget() {
	register_widget('CWS_LatestPortfolio', THEME_SLUG);
}
*/
function add_staff_thumb_name ($columns) {
	$columns = array_slice($columns, 0, 1, true) +
						array('cws_dept_thumb' => __('Thumbnails', THEME_SLUG)) +
						array_slice($columns, 1, NULL, true);
	$columns['taxonomy-cws-staff-procedures'] = __('Procedures', THEME_SLUG);
	return $columns;
}
add_filter('manage_staff_posts_columns', 'add_staff_thumb_name');
add_filter('manage_portfolio_posts_columns', 'add_staff_thumb_name');

function add_staff_thumb ($column, $id) {
	if ('cws_dept_thumb' === $column) {
		echo the_post_thumbnail('thumbnail');
	}
}
add_action('manage_staff_posts_custom_column', 'add_staff_thumb', 5, 2);
add_action('manage_portfolio_posts_custom_column', 'add_staff_thumb', 5, 2);

function cws_portfolio_register_taxonomies() {

	register_taxonomy('cws-staff-dept', 'staff',
		array(
			'hierarchical' => true,
			'show_admin_column' => true,
			'label' => __('Staff Department', THEME_SLUG),
			'labels' => array(
				'add_new_item' => __('Add New Department', THEME_SLUG),
				'update_item' => __('Update Department', THEME_SLUG),
				'search_items' => __('Search Departments', THEME_SLUG),
				'edit_item' => __('Edit Department', THEME_SLUG),
				'view_item' => __('View Department', THEME_SLUG),
				),
			'query_var' => true,
			'rewrite' => array('slug' => 'staff-dept', THEME_SLUG)
			)
		);

	register_taxonomy('cws-staff-procedures', 'staff',
		array(
			'hierarchical' => true,
			'show_admin_column' => false,
			'label' => __('Procedures', THEME_SLUG),
			'labels' => array(
				'add_new_item' => __('Add New Procedure', THEME_SLUG),
				'description' => __('Prices', THEME_SLUG),
				'update_item' => __('Update Procedure', THEME_SLUG),
				'search_items' => __('Search Procedures', THEME_SLUG),
				'edit_item' => __('Edit Procedure', THEME_SLUG),
				'view_item' => __('View Procedure', THEME_SLUG),
				),
			'query_var' => true,
			'rewrite' => array('slug' => 'staff-procedure', THEME_SLUG)
			)
		);

	register_taxonomy('cws-staff-position', 'staff',
		array(
			'hierarchical' => false,
			'show_admin_column' => true,
			'label' => __('Positions', THEME_SLUG),
			'labels' => array(
				'add_new_item' => __('Add New Position', THEME_SLUG),
				'popular_items' => __('Popular Positions', THEME_SLUG),
				'separate_items_with_commas' => __('Separate positions with commas', THEME_SLUG),
				'update_item' => __('Update Position', THEME_SLUG),
				'search_items' => __('Search Positions', THEME_SLUG),
				'edit_item' => __('Edit Position', THEME_SLUG),
				'view_item' => __('View Position', THEME_SLUG),
				'choose_from_most_used' => __('Choose from the most used positions', THEME_SLUG),
				),
			'query_var' => true,
			'rewrite' => array('slug' => 'staff-position', THEME_SLUG)
			)
		);

	register_taxonomy('cws-staff-treatments', 'staff',
		array(
			'hierarchical' => false,
			'show_admin_column' => true,
			'label' => __('Treatments', THEME_SLUG),
			'labels' => array(
				'add_new_item' => __('Add New Treatment', THEME_SLUG),
				'popular_items' => __('Popular Treatments', THEME_SLUG),
				'separate_items_with_commas' => __('Separate treatmentss with commas', THEME_SLUG),
				'update_item' => __('Update Treatment', THEME_SLUG),
				'search_items' => __('Search Treatments', THEME_SLUG),
				'edit_item' => __('Edit Treatment', THEME_SLUG),
				'view_item' => __('View Treatment', THEME_SLUG),
				'choose_from_most_used' => __('Choose from the most used treatmentss', THEME_SLUG),
				),
			'query_var' => true,
			'rewrite' => array('slug' => 'staff-treatments', THEME_SLUG)
			)
		);

	register_taxonomy('cws-portfolio-type', 'portfolio',
		array(
			'hierarchical' => true,
			'show_admin_column' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'portfolio-type')
			)
		);
}

/* Department metaboxes */

function dept_metabox_add($tag) {
	// check for assigned technical category should be added
	?>
	<div class="form-field">
		<label for="cws-clinico-dept-org"><?php _e('Organizer', THEME_SLUG)?></label>
		<textarea name="cws-clinico-dept-org" id="cws-clinico-dept-org" class="postform" rows="5" cols="40"></textarea>
	</div>
	<div class="form-field">
	<label for="cws-clinico-dept-img"><?php _e('Add featured image', THEME_SLUG)?></label>
	<a id="media-dept-img"><?php _e('Add image', THEME_SLUG); ?></a>
	<a id="remov-dept-img" style="display:none"><?php _e('Remove this image', THEME_SLUG); ?></a>
	<input class="widefat" hidden readonly id="dept-img" name="cws-clinico-dept-img" type="text" value="" />
	<img id="img-dept-img" src />
	</div>
	<div class="form-field">
	<label for="cws-clinico-dept-procedures"><?php _e('Select procedures available for this Department', THEME_SLUG)?></label>
	<select multiple name="cws-clinico-dept-procedures[]" id="cws-clinico-dept-procedures" class="postform" style="width:100%">
	<?php
		echo cws_print_procedures_select();
	?>
	</select>
	</div>
	<div class="form-field">
	<label for="cws-clinico-events"><?php _e('Events associated with this Department', THEME_SLUG)?></label>
	<select multiple name="cws-clinico-events[]" id="cws-clinico-events" class="postform" style="width:100%">
	<?php
		echo cws_print_events_select();
	?>
	</select>
	</div>
	<div class="form-field">
	<label for="cws-clinico-dept-fa"><?php _e('Department\'s icon', THEME_SLUG)?></label>
	<select name="cws-clinico-dept-fa" id="cws-clinico-dept-fa" class="postform" style="width:50%">
	<?php
		echo cws_print_fa_select();
	?>
	</select>
	</div>
	<?php
}

function dept_metabox_edit($tag) {
	// check for assigned technical category should be added
	$display_none = ' style="display:none"';
	$org = get_option_value( 'cws-clinico-dept-org', $tag->term_id);
	$title_img = get_option_value( 'cws-clinico-dept-img', $tag->term_id);

	$thumb_url = $title_img ? '="' . wp_get_attachment_thumb_url($title_img) . '"' : '';
	?>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="cws-clinico-dept-org"><?php _e('Organizer', THEME_SLUG)?></label>
		</th><td>
			<textarea name="cws-clinico-dept-org" id="cws-clinico-dept-org" class="postform" rows="5" cols="40"><?php echo $org; ?></textarea>
		</td>
	</tr>
	<tr class="form-field">
	<th scope="row" valign="top">
	<label for="cws-clinico-dept-img"><?php _e('Select a Picture for this department', THEME_SLUG)?></label>
	</th><td>
	<a id="media-dept-img" <?php echo $title_img ? $display_none : ''; ?>><?php _e('Click here to select image', THEME_SLUG); ?></a>
	<a id="remov-dept-img" <?php echo !$title_img ? $display_none : ''; ?>><?php _e('Remove this image', THEME_SLUG); ?></a>
	<input class="widefat" hidden readonly id="dept-img" name="cws-clinico-dept-img" type="text" value="<?php echo $title_img; ?>" />
	<img id="img-dept-img" src<?php echo $thumb_url; ?> />
	</td></tr>
	<?php
	$procedures = get_option_value( 'cws-clinico-dept-procedures', $tag->term_id );
	?>
	<tr class="form-field">
	<th scope="row" valign="top">
	<label for="cws-clinico-dept-procedures"><?php _e('Select Procedures for this department', THEME_SLUG)?></label>
	</th><td>
	<select multiple name="cws-clinico-dept-procedures[]" id="cws-clinico-dept-procedures" class="postform" style="width:50%">
	<?php
		echo cws_print_procedures_select($procedures);
	?>
	</select>
	</td></tr>
	<?php
	$events = get_option_value( 'cws-clinico-dept-events', $tag->term_id );
	?>
	<tr class="form-field">
	<th scope="row" valign="top">
	<label for="cws-clinico-events"><?php _e('Select Events associated with this department', THEME_SLUG)?></label>
	</th><td>
	<select multiple name="cws-clinico-events[]" id="cws-clinico-events" class="postform" style="width:50%">
	<?php
		echo cws_print_events_select($events);
	?>
	</select>
	</td></tr>
	<?php
		$fa_icon = get_option_value( 'cws-clinico-dept-fa', $tag->term_id );
	?>
	<tr class="form-field">
	<th scope="row" valign="top">
	<label for="cws-clinico-dept-fa"><?php _e('Icon', THEME_SLUG)?></label>
	</th><td>
	<select name="cws-clinico-dept-fa" id="cws-clinico-dept-fa" class="postform" style="width:50%">
	<?php
		echo cws_print_fa_select($fa_icon);
	?>
	</select>
	</td></tr>
	<?php
}

add_action('cws-staff-dept_add_form_fields', 'dept_metabox_add', 10, 1);
add_action('cws-staff-dept_edit_form_fields', 'dept_metabox_edit', 10, 1);

/* Procedures metaboxes */

/*function procedures_metabox_add($tag) {
	// check for assigned technical category should be added
	?>
	<div class="form-field">
	<label for="cws-clinico-proc-fa"><?php _e('Select an Icon for this Procedure', THEME_SLUG)?></label>
	<select name="cws-clinico-proc-fa" id="cws-clinico-proc-fa" class="postform" style="width:50%">
	<?php
		echo cws_print_fa_select();
	?>
	</select>
	</div>
	<div class="form-field">
	<label for="cws-clinico-proc-url"><?php _e('Url', THEME_SLUG)?></label>
	<input name="cws-clinico-proc-url" id="cws-clinico-proc-url" type="text" size="40">
	</div>
	<?php
}*/

/*function procedures_metabox_edit($tag) {
	// check for assigned technical category should be added
	$fa_icon = get_option_value( 'cws-clinico-proc-fa', $tag->term_id );
	?>
	<tr class="form-field">
	<th scope="row" valign="top">
	<label for="cws-clinico-proc-fa"><?php _e('Select an Icon for this Procedure', THEME_SLUG)?></label>
	</th><td>
	<select name="cws-clinico-proc-fa" id="cws-clinico-proc-fa" class="postform" style="width:50%">
	<?php
		echo cws_print_fa_select($fa_icon);
	?>
	</select>
	</td></tr>
	<?php
	$proc_url = get_option_value( 'cws-clinico-proc-url', $tag->term_id );
	?>
	<tr class="form-field">
	<th scope="row" valign="top">
	<label for="cws-clinico-dept-procedures"><?php _e('Url', THEME_SLUG)?></label>
	</th><td>
	<input name="cws-clinico-proc-url" id="cws-clinico-proc-url" type="text" val="<?php echo $proc_url; ?>" size="40">
	</td></tr>
	<?php
}*/

/*add_action('cws-staff-procedures_add_form_fields', 'procedures_metabox_add', 10, 1);*/
/*add_action('cws-staff-procedures_edit_form_fields', 'procedures_metabox_edit', 10, 1);*/

/** Save Custom Fields Of Departments */
add_action( 'created_cws-staff-dept', 'staff_metabox_save', 10, 2 );
add_action( 'edited_cws-staff-dept', 'staff_metabox_save', 10, 2 );

function staff_metabox_save( $term_id, $tt_id ) {
	if ( isset( $_POST['cws-clinico-dept-org'] ) ) {
		$option = get_option('cws-clinico-dept-org');
		$option[$term_id] = $_POST['cws-clinico-dept-org'];
	} else {
		$option[$term_id] = '';
	}
	update_option( 'cws-clinico-dept-org', $option);
	if ( isset( $_POST['cws-clinico-dept-img'] ) ) {
		$option = get_option('cws-clinico-dept-img');
		$option[$term_id] = $_POST['cws-clinico-dept-img'];
	} else {
		$option[$term_id] = '';
	}
	update_option( 'cws-clinico-dept-img', $option);
	// fontawesome icon for widgets
	if ( isset( $_POST['cws-clinico-dept-fa'] ) ) {
		$option = get_option('cws-clinico-dept-fa');
		$option[$term_id] = $_POST['cws-clinico-dept-fa'];
	} else {
		$option[$term_id] = '';
	}
	update_option( 'cws-clinico-dept-fa', $option);
	// widget description
	if ( isset( $_POST['cws-clinico-dept-wdesc'] ) ) {
		$option = get_option('cws-clinico-dept-wdesc');
		$option[$term_id] = stripslashes($_POST['cws-clinico-dept-wdesc']);
	} else {
		$option[$term_id] = '';
	}
	update_option( 'cws-clinico-dept-wdesc', $option);
	if ( isset( $_POST['cws-clinico-dept-procedures'] ) ) {

		$procs = $_POST['cws-clinico-dept-procedures'];
		$i = count($procs);
		$out = '';
		foreach ($procs as $proc) {
			$i--;
			$out .= $proc;
			$out .= ($i ? ',' : '');
		}
		$option = get_option('cws-clinico-dept-procedures');
		$option[$term_id] = $out;
	} else {
		$option[$term_id] = '';
	}
	update_option( 'cws-clinico-dept-procedures', $option );
	if ( isset( $_POST['cws-clinico-events'] ) ) {
		$procs = $_POST['cws-clinico-events'];
		$i = count($procs);
		$out = '';
		foreach ($procs as $proc) {
			$i--;
			$out .= $proc;
			$out .= ($i ? ',' : '');
		}
		$option = get_option('cws-clinico-dept-events');
		$option[$term_id] = $out;
	} else {
		$option[$term_id] = '';
	}
	update_option( 'cws-clinico-dept-events', $option );

}

/** Save Custom Fields Of Departments */
add_action( 'created_cws-staff-procedures', 'procedures_metabox_save', 10, 2 );
add_action( 'edited_cws-staff-procedures', 'procedures_metabox_save', 10, 2 );

function procedures_metabox_save( $term_id, $tt_id ) {
	if ( isset( $_POST['cws-clinico-proc-fa'] ) ) {
		$option = get_option('cws-clinico-proc-fa');
		$option[$term_id] = $_POST['cws-clinico-proc-fa'];
		update_option( 'cws-clinico-proc-fa', $option);
	}
	if ( isset( $_POST['cws-clinico-proc-url'] ) ) {
		$option = get_option('cws-clinico-proc-url');
		$option[$term_id] = $_POST['cws-clinico-proc-url'];
		update_option( 'cws-clinico-proc-url', $option );
	}
}

/* add icon to the cat list */
function add_staff_fa_column($columns) {
	$columns =	array_slice($columns, 0, 1, true) +
							array('cws_dept_thumb' => __('Pics', THEME_SLUG)) +
							array_slice($columns, 1, NULL, true);
	$columns['procedures'] = __('Procedures', THEME_SLUG);
	$columns['slug'] = $columns['procedures'];
	$columns['events'] = __('Events', THEME_SLUG);
	unset($columns['slug']);
	$columns['cws_dept_decription'] = __('Description', THEME_SLUG);
	$columns['description'] = $columns['cws_dept_decription'];
	unset($columns['description']);
	return $columns;
}

function get_option_value($option, $id) {
	$opt = get_option( $option );
	return isset($opt[$id]) ? $opt[$id] : null;
}

function add_staff_fa_icon ($value, $column_name, $id) {
	switch ($column_name) {
		case 'cws_dept_thumb':
			$title_img = get_option_value( 'cws-clinico-dept-img', $id );
			$thumb_url = $title_img ? '="' . wp_get_attachment_thumb_url($title_img) . '"' : '';
			if (!empty($thumb_url)) {
				echo '<img id="img-dept-img-'.$id.'" src' . $thumb_url. '/>';
			}
		break;
		/*case 'cws_dept_decription':
			$v = get_terms('cws-staff-dept', array('hide_empty' => 0, 'include' => array($id) ));
			$desc = $v[0]->description;
			//$desc = category_description( ->term_id );
			$wdesc = get_option_value( 'cws-clinico-dept-wdesc', $id );
			$fa_widget = get_option_value( 'cws-clinico-dept-fa', $id );
			if (!empty($fa_widget)) {
				$fa_widget = '<i class="fa fa-' . $fa_widget . '"></i>';
			}
			echo $desc;
			if (!empty($wdesc) && !empty($fa_widget)) {
				echo '<br><div class="wdesc">' . $fa_widget . $wdesc . '</div>';
			}
		break;*/
		case 'procedures':
			$procs_string = get_option_value( 'cws-clinico-dept-procedures', $id );
			if ($procs_string) {
				$procs_arr = explode(",", $procs_string );
				$procs = get_terms('cws-staff-procedures',
					array('hide_empty' => 0,
						'parent' => 0,
						'include' => $procs_arr));
				$i = count($procs);
				if ($i) {
					$out = '';
					foreach ($procs as $v) {
						$i--;
						$out .= $v->name;
						$out .= ($i ? ', ' : '');
					}
					echo $out;
				}
			}
		break;
		case 'events':
			$events_string = get_option_value( 'cws-clinico-dept-events', $id );
			if ($events_string) {
				$events_arr = explode(",",  $events_string);
				$events = get_terms('tribe_events_cat',
					array('hide_empty' => 0,
						'parent' => 0,
						'include' => $events_arr));
				if ( !isset($events->errors) ) {
					$i = count($events_arr);
					if ($i) {
						$out = '';
						foreach ($events as $v) {
							$i--;
							$out .= $v->name;
							//$out .= $events[array_search($v, $events)]->name;
							$out .= ($i ? ', ' : '');
						}
						echo $out;
					}
				}
			}
		break;
	}
}

add_filter('manage_edit-cws-staff-dept_columns', 'add_staff_fa_column');
add_filter('manage_cws-staff-dept_custom_column', 'add_staff_fa_icon', 10, 3);
//add_filter('manage_cws-staff-dept_column', 'add_staff_fa_icon', 10, 3);

// remove
function edit_procedures_columns($columns) {
	$columns =	array_slice($columns, 0, 1, true) +
							array('cws_proc_thumb' => __('Pics', THEME_SLUG)) +
							array_slice($columns, 1, NULL, true);
	unset($columns['posts']);
	$columns['url'] = __('Url', THEME_SLUG);
	$columns['slug'] = $columns['url'];
	unset($columns['slug']);
	return $columns;
}

function add_procedures_fields ($value, $column_name, $id) {
	switch ($column_name) {
		case 'cws_proc_thumb':
			$fa_icon = get_option_value( 'cws-clinico-proc-fa', $id );
			if (!empty($fa_icon)) {
				echo '<i class="fa fa-' . $fa_icon . ' fa-2x"></i>';
			}
		break;
		case 'url':
			$url = get_option_value( 'cws-clinico-proc-url', $id );
			if (!empty($url)) {
				echo '<a href="'.$url.'">'.$url.'</a>';
			}
		break;
	}
}

add_filter('manage_edit-cws-staff-procedures_columns', 'edit_procedures_columns');
add_filter('manage_cws-staff-procedures_custom_column', 'add_procedures_fields', 10, 3);

/* Shortcodes */
function cws_metabox_scripts_enqueue($a) {
 if( $a == 'widgets.php' || $a == 'post-new.php' || $a == 'post.php' || $a == 'edit-tags.php' ) {
  wp_enqueue_script( 'select2-js', THEME_URI . '/framework/rc/assets/js/vendor/select2/select2.js', array('jquery') );
  wp_enqueue_style( 'select2-css', THEME_URI . '/framework/rc/assets/js/vendor/select2/select2.css', false, '2.0.0' );
  wp_enqueue_script( 'custom-admin-js', THEME_URI . '/core/js/custom-admin.js', array('jquery') );
  wp_enqueue_script( 'post-metaboxes-script', THEME_URI . '/core/js/post-metaboxes.js', array( 'jquery' ) );
  wp_enqueue_media();
  wp_enqueue_style( 'mb_post_css' );
 }
}

add_action( 'admin_enqueue_scripts', 'cws_metabox_scripts_enqueue' );

/* Metaboxes */
/*add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );

function load_custom_wp_admin_style() {
	wp_register_style( 'portfolio_css', CWS_PORTFOLIO_PLUGIN_URL . '/css/styles.css', false, '1.0.0' );
	wp_enqueue_style( 'portfolio_css' );

	//wp_enqueue_script( 'popup-js', get_template_directory_uri() . '/core/popup.js', array('jquery', THEME_SLUG) );
	//wp_enqueue_script( 'wp-color-picker', THEME_SLUG);
	//wp_register_script( 'post-metaboxes-script', THEME_URI . '/core/js/post-metaboxes.js', array( 'jquery' ) );
	//wp_enqueue_script( 'post-metaboxes-script' );
	//wp_enqueue_style( 'mb_post_css' );
}*/

add_action( 'add_meta_boxes', 'cws_portfolio_add_metaboxes' );

function cws_portfolio_add_metaboxes() {
	add_meta_box( 'cws-metabox-id', 'Team Member\'s Page', 'cws_mb_staff_callback', 'staff', 'advanced', 'high' );
	add_meta_box( 'cws-metabox-id', 'Portfolio Page', 'cws_mb_portfolio_callback', 'portfolio', 'advanced', 'high' );
}

function cws_mb_staff_callback ( $post ) {
	//$cws_stored_meta_sg = get_post_meta( $post->ID, 'cws-mb-socialgroup', THEME_SLUG);
	$cws_stored_meta = get_post_meta( $post->ID, 'cws-staff', THEME_SLUG);
	//$cws_stored_meta_sg = isset($cws_stored_meta_sg[0]) ? $cws_stored_meta_sg[0] : null;

	//$social = isset($cws_stored_meta[0]['cws-staff-social']) ? $cws_stored_meta[0]['cws-staff-social'] : '';
	wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );
	$post_mbhtml_attr = array(
		'social' => array(
			'options' => array(
				array(
					'id' => 'socialgroup',
					'title' => __('Social group:',THEME_SLUG),
					'type' => 'group',
					'source' => array(
						array(
							'id' => 'fa',
							'title' => __('Icon',THEME_SLUG),
							'type' => 'select',
							'source' => 'get_font_fa_icons',
							'w' => '150px'
						),
						array(
							'id' => 'title',
							'title' => __('Title',THEME_SLUG),
							'type' => 'text',
							'w' => '150px'
						),
						array(
							'id' => 'url',
							'title' => __('Url',THEME_SLUG),
							'type' => 'text',
							'w' => '150px'
						),
					),
					'w' => '100%'
				),
			)
		),
	);

	$degree = isset( $cws_stored_meta['cws-staff-degree'] ) ? esc_attr( $cws_stored_meta['cws-staff-degree'] ) : '';
	$office = isset( $cws_stored_meta['cws-staff-office'] ) ? esc_attr( $cws_stored_meta['cws-staff-office'] ) : '';
	$workingdays = isset( $cws_stored_meta['cws-staff-workingdays'] ) ? $cws_stored_meta['cws-staff-workingdays'] : array();
	$post_mb_staff_attr = array(
		'staff'=>array(
			'options'=>array(
				array(
					'id' => 'degree',
					'title' => __('Doctor\'s degree (optional)',THEME_SLUG),
					'type' => 'text',
					'default' => $degree,
					'w' => '100%'
				),
				array(
					'id' => 'office',
					'title' => __('Office address',THEME_SLUG),
					'type' => 'text',
					'default' => $office,
					'w' => '100%'
				),
				array(
					'id' => 'workingdays',
					'title' => __('Working days',THEME_SLUG),
					'desc' => __('Working days','cws_portfolio'),
					'type' => 'select',
					'select' => 'multiple',
					'source' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
					'default' => $workingdays,
					'w' => '100%'
				),
			)
		),
	);

	echo '<section id="post-staff-general" class="cws-page-section">';
	echo cws_shortcode_html_gen($post_mb_staff_attr, 'staff', 0, false, false, 'staff');
	echo '</section>';

	for ($i=0;$i < 5; $i++) {
		echo '<section id="post-staff-' . $i . '" class="cws-page-section">';
		echo cws_shortcode_html_gen($post_mbhtml_attr, 'social', isset($cws_stored_meta['social'][$i]) ? $cws_stored_meta['social'][$i] : '', $i, false);
		echo '</section>';
	}

		?>
		<script>cws_shortcode_init();</script>
	<?php
}

function cws_mb_portfolio_callback( $post ) {
	$cws_stored_meta = get_post_meta( $post->ID, 'cws-portfolio');
	$short_desc = isset( $cws_stored_meta[0]['cws-portfolio-short_desc'] ) ? esc_attr( $cws_stored_meta[0]['cws-portfolio-short_desc'] ) : '';
	$video = isset( $cws_stored_meta[0]['cws-portfolio-video'] ) ? esc_attr( $cws_stored_meta[0]['cws-portfolio-video'] ) : '';
	$is_video_link = isset( $cws_stored_meta[0]['cws-portfolio-is-video-link'] ) ? true : false;
	$use_rel_projects = isset( $cws_stored_meta[0]['cws-portfolio-use_rel_projects'] ) ? true : false;
	$rel_projects_title = isset( $cws_stored_meta[0]['cws-portfolio-rel_projects_title'] ) ? $cws_stored_meta[0]['cws-portfolio-rel_projects_title'] : '';
	$rel_projects_num = isset( $cws_stored_meta[0]['cws-portfolio-rel_projects_num'] ) ? $cws_stored_meta[0]['cws-portfolio-rel_projects_num'] : '4';

	wp_nonce_field( 'cws_mb_nonce', 'mb_nonce' );

	$post_mbhtml_attr = array(
			'portfolio'=>array(
				'options'=>array(
					array(
						'id' => 'short_desc',
						'title' => __('Excerpt', THEME_SLUG),
						'type' => 'text',
						'default' => $short_desc,
						'w' => '50%'
					),
					array(
						'id' => 'video',
						'title' => __('Custom URL', THEME_SLUG),
						'type' => 'textarea',
						'rows' => '3',
						'default' => $video,
						'w' => '50%'
					),
					array(
						'id' => 'is-video-link',
						'title' => __('Use direct URL instead of the popup', THEME_SLUG),
						'type' => 'check',
						'source' => array(
							'' => '',
							),
						'default' => array($is_video_link),
					),
					array(
						'id' => '',
						'title' => __('Show related projects:', THEME_SLUG),
						'type' => 'group',
						'source' => array(
							array(
								'id' => 'use_rel_projects',
								'type' => 'check',
								'toggle' => array('rel_projects_title','rel_projects_num'),
								'source' => array('' => ''),
								'default' => array($use_rel_projects),
							),
							array(
								'id' => 'rel_projects_title',
								'disabled' => !$use_rel_projects,
								'title' => __('Carousel title', THEME_SLUG),
								'type' => 'text',
								'default' => $rel_projects_title,
								'w' => '300px'
							),
							array(
								'id' => 'rel_projects_num',
								'disabled' => !$use_rel_projects,
								'title' => __('Number of items', THEME_SLUG),
								'type' => 'text',
								'default' => $rel_projects_num,
								'w' => '200px'
							)
						)
					)
				)
			),
		);

	?>
		<section id="cws-portfolio-section" class="cws-portfolio-section">
			<?php echo cws_shortcode_html_gen($post_mbhtml_attr, 'portfolio', 0, false, false, 'portfolio'); ?>
		</section>
	<?php
}

add_action( 'save_post', 'cws_plugin_metabox_save', 11, 2 );

function cws_plugin_metabox_save( $post_id, $post )
{
	if ( "portfolio" == $post->post_type || "staff" == $post->post_type ) {
		// Bail if we're doing an auto save
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		// if our nonce isn't there, or we can't verify it, bail
		if ( !isset( $_POST['mb_nonce']) || !wp_verify_nonce($_POST['mb_nonce'], 'cws_mb_nonce', THEME_SLUG) ) return;

		// if our current user can't edit this post, bail
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// now we can actually save the data
		$allowed = array(
			'a' => array( // on allow a tags
				'href' => array() // and those anchords can only have href attribute
			)
		);
		// Probably a good idea to make sure your data is set
		$key_prefix = 'cws-' . $post->post_type; // 'cws-portfolio-'
		if ("portfolio" == $post->post_type) {
			foreach($_POST as $key => $value) {
				if (0 === strpos($key, $key_prefix)) {
					$save_array[$key] = $value;
				}
			}
		}
		else if ("staff" == $post->post_type) {
			$key_prefix_sg = 'cws-mb-socialgroup-fa';
			$save_array = array();
			foreach($_POST as $key => $value) {
				if (0 === strpos($key, $key_prefix)) {
					$save_array[$key] = $value;
					update_post_meta($post_id, $key_prefix, $save_array);
				} else if (0 === strpos($key, $key_prefix_sg)) {
					$idx = substr($key, -1);
					if ( !empty($_POST['cws-mb-socialgroup-title'.$idx]) && !empty($_POST['cws-mb-socialgroup-url'.$idx]) ) {
						$save_array['social'][$idx]['cws-mb-socialgroup-fa'] = $value;
						//$save_array['social'][$idx]['cws-mb-socialgroup-color'] = $_POST['cws-mb-socialgroup-color'.$idx];
						$save_array['social'][$idx]['cws-mb-socialgroup-title'] = $_POST['cws-mb-socialgroup-title'.$idx];
						$save_array['social'][$idx]['cws-mb-socialgroup-url'] = $_POST['cws-mb-socialgroup-url'.$idx];
					}
				}
			}
		}
		update_post_meta($post_id, $key_prefix, $save_array);
	}
}

// our little API
function cws_portfolio_get_image ($content) {
	$first_img = '';
	$shortcodes = array('gallery' => 'ids', 'vc_gallery' => 'images', 'vc_video' => 'link', THEME_SLUG);
	foreach ($shortcodes as $shortcode => $value) {
		$pos = strpos($content, '[' . $shortcode);
		if (false !== $pos) {
			$end_pos = strpos($content, ']', $pos);
			$val_pos = strpos($content, $value . '=', $pos);
			if ($val_pos < $end_pos) {
				// if this attribute is inside our shortcode
				$att_array_start = $val_pos + strlen($value) + 2;
				// extract only values between "..."
				$str_atts = substr($content, $att_array_start, strpos($content, '"', $att_array_start) - $att_array_start);
				$str_array_atts = explode(',', $str_atts);
				$first_img_id = $str_array_atts[0];
				if ('vc_video' == $shortcode) {
					$first_img = cws_get_video_thumb($first_img_id);
				}
				else {
					$first_img = wp_get_attachment_url( $first_img_id );
				}
				break;
			}
		}
	}
	return $first_img;
}

function cws_get_video_thumb( $url ) {
	$ret = '';
	preg_match( '#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#', $url, $matches );
	if ( isset($matches[0]) ) {
		$ret = 'http://i3.ytimg.com/vi/' . $matches[0] . '/0.jpg';
	}
	else {
		preg_match( '#vimeo.com\/(.*)#', $url, $matches );
		if ( isset($matches[1]) ) {
			$resp = wp_remote_get('http://vimeo.com/api/v2/video/' . $matches[1] . '/php');
			$vimeo_ret = unserialize($resp['body']);
			$ret = $vimeo_ret[0]['thumbnail_large'];
		}
	}
	return $ret;
}

?>