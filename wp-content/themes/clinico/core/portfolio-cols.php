<?php
	if(!empty($_POST['action'])) {
		require_once('../../../../wp-config.php');
		$action = $_POST['action'];
		//$all = $_POST['all'];
		$all = $action[2];
		$link = $action[0];
		$page = intval( substr( $link, strpos( $link,'paged=' )+6 ) );
		if (empty($page)) {
			$page=1;
		}
		$curr_filter = $action[5];
		$cats = $curr_filter != $all ? $curr_filter : '';
		echo render_portfolio(	$action[1],
														$action[2],
														(boolean)$action[3],
														$action[4],
														$page,
														$cats,
														true,
														$action[7],
														'',
														$action[6],
														$action[8]
														);
		die();
	}
	else if (!empty($_POST['filter'])) {
		require_once('../../../../wp-config.php');
		$filter = $_POST['filter'];
		$all = $filter[2];
		$page = !empty($_POST['link']) ? intval(substr($_POST['link'], strpos($_POST['link'], 'paged=') + 6)) : '';
		$curr_filter = $filter[5];
		//$prev_filter = $filter[5];
		//$page = intval(substr($link, strpos($link, 'paged=') + 6));
		if (empty($page) || ( empty($filter[2]) && ($curr_filter != $prev_filter) ) ) {
			$page=1;
		}
		$cats = $curr_filter != $all ? $curr_filter : '';
		if ($page > 1) {
			$cats = $filter[2];
		}

		echo render_portfolio( 	$filter[1],
														$filter[2],
														(boolean)$filter[3],
														$filter[4],
														$page,
														$cats,
														true,
														$filter[7],
														'',
														$filter[6],
														$filter[8]
														);
		die();
	}

function render_portfolio ($cols = 2,
							$cats,
							$UseFilter = false,
							$posts_per_page = '-1',
							$paged = 1,
							$filter = '',
							$bAjax = false,
							$blogtype = 'pinterest',
							$title = '',
							$pid = null,
							$cp_type = 'portfolio' ) {
		$content = '';
		switch ($cp_type) {
			case 'portfolio':
				$term = 'cws-portfolio-type';
				$tour = 'photo_tour';
				break;
			case 'staff':
				$term = 'cws-staff-dept';
				$tour = 'our_team';
				break;
		}
		if (!empty($cats) && '*' !== $cats) {
			$categories = array();
			$cats_array = explode( ',', $cats );
			foreach ( $cats_array as $cat) {
				$parent_term = get_term_by('slug', $cat, $term);
				$child_terms = get_terms($term, array('hide_empty' => true, 'child_of' => $parent_term->term_id));
				for ( $i=0; $i<count( $child_terms ); $i++ ){
					if ( in_array( $child_terms[$i]->slug, $cats_array ) ) array_splice( $child_terms, $i, 1 );
				}
				array_push( $categories, $parent_term );
				$categories = array_merge( $categories, $child_terms );
			}
		} else {
			$categories = get_terms($term, array('hide_empty' => true));
		}

		$content .=  $bAjax ? "" : "<div class='photo_tour_section_header clearfix'>";

		if ( $UseFilter && count($categories) > 1 ) :
			$all = '*';
			if (!empty($cats)) {
				$all = $cats;
			}
			if (!$bAjax):
			$content .= "<select class='filter'>";
				$content .= "<option value='$all'";
					$content .=  (!empty($filter) && $filter==$cats) ? ' selected>' : '>';
					$content .= __('All', THEME_SLUG);
				$content .= "</option>";
				foreach ( $categories as $cat ) {
					$selected = ($cat->slug == $filter) ? ' selected' : '';
					$content .= '<option value="' . $cat->slug . '"' . $selected . '>' . $cat->name . "</option>\n";
				}
			$content .= "</select>";
			endif;
		endif;

				$content .= ( ($bAjax) && (empty( $title )) ) ? "" : "<div class='widget-title'>$title</div>";
				$content .=  $bAjax ? "" : "</div>";
				$UseFilter =  intval($UseFilter);
				$ajaxurl = THEME_URI . '/core/portfolio-cols.php';
					$tax_query_arr = array();
					if (!empty($filter)) {
						$tax_query_arr = array(
								'taxonomy' => $term,
								'field' => 'slug',
								'terms' => explode(',', $filter)
							);
					} else if (!empty($categories)) {
						$terms = array();
						foreach ($categories as $category) {
							array_push( $terms, $category->slug );
						}
						$tax_query_arr = array(
								'taxonomy' => $term,
								'field' => 'slug',
								'terms' => $terms
							);
					}

					$arr = array(
						'posts_per_page' => $posts_per_page,
						'post_type' => $cp_type,
						'paged' => $paged,
						'ignore_sticky_posts' => true,
						'tax_query' => array( $tax_query_arr )
					);
					if ($posts_per_page == '-1') {
						unset($arr['paged']);
					}
					if (empty($tax_query_arr)) {
						unset($arr['tax_query']);
					}
					$p = new WP_Query($arr);
					$sb_block = '';
					if ( $pid ) {
						$sb = cws_GetSbClasses( $pid );
						$sb_block = $sb['sidebar_pos'];
					}
					if (!$bAjax){
						$content .= "<div class='$tour news-" . $blogtype . ( $blogtype == "pinterest" ? " news-" . $cols : ""  ) . "' >";
						$content .= '<div class="grid isotope" data-filter="'	. $filter
						.	'" data-cols="'	. $cols
						. '" data-aurl="'	. $ajaxurl
						. '" data-use-filter="'	. $UseFilter
						. '" data-ppp="' . $posts_per_page
						. '" data-blogtype="' . $blogtype
						. '" data-sb-block="'	. $sb_block
						. '" data-cp-type="'	. $cp_type
						. '" >';
					}
					if ($p->have_posts()) :
						if (!$pid) {
							$pid = get_post();
							if ($pid){
								$pid = $pid->ID;
							}
						}
						$thumbnail_dims = cws_get_post_tmumbnail_dims( $blogtype, $cols, $sb_block );
						$chars_count = cws_get_content_chars_count( $blogtype, $cols );

						if ($bAjax)
							$content .= "<div class='ajax_content'>";
						while ($p->have_posts()) : $p->the_post();
							$content .= build_portfolio_item( get_the_ID(), $cols, false, $thumbnail_dims, $chars_count, $cp_type );
						endwhile;
					endif;
				?>
	<?php $content .=  $bAjax ? '' : '</div></div>'; // class=grid ?>
	<?php
	if ( $posts_per_page != '-1' ) {
		$content .= ppagenavi( $p, '<div class="pagination">', '</div>', $paged);
	}
	if ($bAjax) $content .= "</div>";

	if ( $bAjax ){
		$content .= "</div>";
	}

	return $content;
}

function render_portfolio_carousel ($postspp, $title, $cats='', $cp_type){
	$out = '';
	switch ($cp_type) {
		case 'portfolio':
			$term = 'cws-portfolio-type';
			$tour = 'photo_tour';
			break;
		case 'staff':
			$term = 'cws-staff-dept';
			$tour = 'our_team';
			break;
	}

	$arr = array('posts_per_page' => $postspp,
				 'post_type' => $cp_type,
				 'ignore_sticky_posts' => true );

	if ( !empty($cats) ){
		$tax_query_arr = array(
				'taxonomy' => $term,
				'field' => 'slug',
				'terms' => explode(',', $cats)
		);
		$arr['tax_query'] = array( $tax_query_arr );
	}

	$r = new WP_Query( $arr );
	$out .= "<div class='carousel_header clearfix'>";
	$out .=  $r->post_count ? "<div class='carousel_nav'><i class='prev fa fa-angle-left'></i><i class='next fa fa-angle-right'></i><div class='clearfix'></div></div>" : "" ;
	$out .= !empty( $title ) ? "<div class='widget-title'>$title</div>" : "";
	$out .= "</div>";
	$out .= "<div class='$tour carousel_content'>";
	$out .= "<div class='carousel'>";
	$thumbnail_dims = cws_get_post_tmumbnail_dims( 'pinterest', 2, 'none' );
	$chars_count = cws_get_content_chars_count( 'pinterest', 4 );
	while ($r->have_posts()):
		$r->the_post();
		$out .= build_portfolio_item( get_the_ID(), 4, true, $thumbnail_dims, $chars_count, $cp_type );
	endwhile;
	$out .= "</div></div>";
	wp_reset_postdata();
	return $out;
}

function build_portfolio_item ($pageid, $cols, $is_carousel = false, $thumbnail_dims, $chars_count, $cp_type ) {
	$out = '';
	$term = '';
	switch ($cp_type) {
		case 'portfolio':
			$term = 'cws-portfolio-type';
			break;
		case 'staff':
			$term = 'cws-staff-dept';
			break;
	}
	$cws_stored_meta = get_post_meta( $pageid, 'cws-' . $cp_type);
	if (!$is_carousel){
		$cats = wp_get_post_terms($pageid, $term);
		$cat_id = '';
		if ( count($cats) > 0 ) {
			foreach ( $cats as $cat ) {
				$cat_id .= $cat->term_id;
			}
		}
	}
	$out .= '<div class="item"' . ( !$is_carousel ? ' data-category="' . $cat_id . '"' : '' ) . '>';
	$video = isset( $cws_stored_meta[0]['cws-portfolio-video'] ) ? esc_attr( $cws_stored_meta[0]['cws-portfolio-video'] ) : '';
	$is_direct_url = isset( $cws_stored_meta[0]['cws-portfolio-is-video-link'] ) ? true : false;

	$content = get_the_content();
	$image = cws_portfolio_get_image($content);
	$fancy_image = $image;
	$a_class = '';
	$url_target = '';
	if ( empty($image) ) {
		$img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		$fancy_image =  $img[0];
		$image = bfi_thumb( $img[0], $thumbnail_dims );
		//$image = $img[0];
	}

	$out .= '<div class="pic">';

	switch ($cp_type) {
		case 'portfolio':
			$out .= '<img src="' . $image . '" alt="">';
			$out .= '<div class="hover-effect"></div>';
			$out .= '<div class="links">';
			$out .= '<a' . ( $is_direct_url ? ' href="' . ( empty($video) ? get_the_permalink() : $video ) . '" class="fa fa-link" target = "_blank"' : ( !empty($video) ? ' href="' . $video . '" class="fa fa-magic fancy" data-fancybox-type="iframe"' : ( !empty($image) ? ' href="' . $fancy_image . '" class="fancy fa fa-eye"' : '' ) ) ) . '></a>';
			$out .= '</div></div>';
			$out .= '<div class="portfolio_item_info">';
			$title = get_the_title();
			$link = !$is_direct_url ? get_the_permalink() : ( empty($video) ? get_the_permalink() : $video );
			$out .= !empty( $title ) ? "<div class='name'><a href='$link'" . ( ( !empty($video) && $is_direct_url ) ? " target = '_blank'" : "" ) . ">$title</a></div>" : "";
			$out .= !empty( $cws_stored_meta[0]['cws-portfolio-short_desc'] ) ? $cws_stored_meta[0]['cws-portfolio-short_desc'] : cws_post_content_output( $chars_count );
			break;
		case 'staff':
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			$out .= '<img src="' . bfi_thumb($img[0], array('width' => 270, 'height' => 270) ) . '" alt="">';
			$out .= '<div class="hover-effect"></div>';
			$out .= '<div class="links">';
			$cws_stored_meta = isset( $cws_stored_meta[0]['social'] ) ? $cws_stored_meta[0]['social'] : array();

			if (count($cws_stored_meta)>0) {
				foreach ($cws_stored_meta as $social_item) {
					$url = $social_item['cws-mb-socialgroup-url'];
					$title = $social_item['cws-mb-socialgroup-title'];
					$fa =  $social_item['cws-mb-socialgroup-fa'];
					$out .= '<a ' . ( $url ? "href='$url' " : "" ) . ( $title ? "title='$title' " : "" ) . ( $fa ? "class='fa fa-$fa' " : "" ) . ' target="_blank"></a>';
				}
			}
			$out .= "</div></div><div class='team_member_info'>";
			$name = get_the_title();
			$link = get_permalink();
			$out .= $name ?  "<a href='$link'><div class='name'>" . $name . "</div></a>" : "";

			$terms = wp_get_post_terms(get_the_ID(), 'cws-staff-position');
			if ( count($terms) ):
				$out .= "<div class='positions'>";
				$i = 0;
				foreach ($terms as $k=>$v) {
					$i++;
					$out .= $v->name;
					if ($i < count($terms)) {
						$out .= ', ';
					}
				}
				$out .= "</div>";
			endif;
			break;
	}
	$out .= '</div></div>';
	return $out;
}

function ppagenavi($query, $before = '', $after = '', $page_d = 1) {
		wp_reset_query();
		global $wpdb, $paged;

		$paged = $page_d;

		$pagenavi_options = array(
			'pages_text' => '',
			'current_text' => '%PAGE_NUMBER%',
			'page_text' => '%PAGE_NUMBER%',
			'first_text' => 'First Page',
			'last_text' => 'Last Page',
			'next_text' => __("Next", THEME_SLUG),
			'prev_text' => __("Previous", THEME_SLUG),
			'dotright_text' => '...',
			'dotleft_text' => '...',
			'num_pages' => 5, //continuous block of page numbers
			'always_show' => 0,
			'num_larger_page_numbers' => 0,
			'larger_page_numbers_multiple' => 5,
		);

		$output = "";

		$request = $query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		//Retrieve variable in the WP_Query class.
		/*http://codex.wordpress.org/Function_Reference/get_query_var*/
/*			if ( get_query_var('paged') ) {
			$paged = get_query_var('paged');
		}
		elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		} else {
			$paged = 1;
		}*/
		$numposts = $query->found_posts;
		$max_page = $query->max_num_pages;

		$pages_to_show = intval($pagenavi_options['num_pages']);
		$larger_page_to_show = intval($pagenavi_options['num_larger_page_numbers']);
		$larger_page_multiple = intval($pagenavi_options['larger_page_numbers_multiple']);
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start = floor($pages_to_show_minus_1/2);
		//ceil — Round fractions up (http://us2.php.net/manual/en/function.ceil.php)
		$half_page_end = ceil($pages_to_show_minus_1/2);
		$start_page = $paged - $half_page_start;

		if( $start_page <= 0 ) {
			$start_page = 1;
		}

		$end_page = $paged + $half_page_end;
		if( ($end_page - $start_page) != $pages_to_show_minus_1 ) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if($start_page <= 0) {
			$start_page = 1;
		}

		$larger_per_page = $larger_page_to_show*$larger_page_multiple;
		$larger_start_page_start = (round_num($start_page, 10) + $larger_page_multiple) - $larger_per_page;
		$larger_start_page_end = round_num($start_page, 10) + $larger_page_multiple;
		$larger_end_page_start = round_num($end_page, 10) + $larger_page_multiple;
		$larger_end_page_end = round_num($end_page, 10) + ($larger_per_page);

		if( $larger_start_page_end - $larger_page_multiple == $start_page ) {
			$larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
			$larger_start_page_end = $larger_start_page_end - $larger_page_multiple;
		}
		if( $larger_start_page_start <= 0 ) {
			$larger_start_page_start = $larger_page_multiple;
		}
		if( $larger_start_page_end > $max_page ) {
			$larger_start_page_end = $max_page;
		}
		if( $larger_end_page_end > $max_page ) {
			$larger_end_page_end = $max_page;
		}

		if( $max_page > 1 || intval($pagenavi_options['always_show']) == 1 ) {
			/*http://php.net/manual/en/function.str-replace.php */
			/*number_format_i18n(): Converts integer number to format based on locale (wp-includes/functions.php*/
			$pages_text = str_replace("%CURRENT_PAGE%", number_format_i18n($paged), $pagenavi_options['pages_text']);
			$pages_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pages_text);
			$output .= $before;

			if(!empty($pages_text)) {
				$output .= '<span class="pages">'.$pages_text.'</span>';
			}
			//Displays a link to the previous post which exists in chronological order from the current post.
			/*http://codex.wordpress.org/Function_Reference/previous_post_link*/
			if ($paged > 1) {
				$output .= '<a class="prev page-numbers" href="paged=' . ($paged - 1) . '">' . $pagenavi_options['prev_text'] . '</a>';
			}

			if ($start_page >= 2 && $pages_to_show < $max_page) {
				$first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['first_text']);
				//esc_url(): Encodes < > & " ' (less than, greater than, ampersand, double quote, single quote).
				/*http://codex.wordpress.org/Data_Validation*/
				//get_pagenum_link():(wp-includes/link-template.php)-Retrieve get links for page numbers.
				$output .= '<a href="paged=1" class="first page-numbers" title="'.$first_page_text.'">1</a>';
				if(!empty($pagenavi_options['dotleft_text'])) {
					$output .= '<span class="expand">'.$pagenavi_options['dotleft_text'].'</span>';
				}
		}

		if ( $larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page ) {
			for($i = $larger_start_page_start; $i < $larger_start_page_end; $i+=$larger_page_multiple) {
				$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
				$output .= '<a href="paged='.$i.'" class="page-numbers" title="'.$page_text.'">'.$page_text.'</a>';
			}
		}

		for ( $i = $start_page; $i  <= $end_page; $i++ ) {
			if( $i == $paged ) {
				$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
				$output .= '<span class="current">'.$current_page_text.'</span>';
			} else {
				$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
				$output .= '<a href="paged='.$i.'" class="single_page page-numbers" title="'.$page_text.'">'.$page_text.'</a>';
			}
		}

		if ( $end_page < $max_page ) {
			if(!empty($pagenavi_options['dotright_text'])) {
				$output .= '<span class="expand">'.$pagenavi_options['dotright_text'].'</span>';
			}
			$last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['last_text']);
			$output .= '<a href="paged='.$max_page.'" class="last page-numbers" title="'.$last_page_text.'">'.$max_page.'</a>';
		}

		$next = ($paged < $max_page) ? '<a class="page-numbers" href="paged='. ($paged + 1) . '">' . $pagenavi_options['next_text'] . '</a>' : '';
		if ( strlen($next) ) {
			$output .= $next;
		}

		if ( $larger_page_to_show > 0 && $larger_end_page_start < $max_page ) {
			for ( $i = $larger_end_page_start; $i <= $larger_end_page_end; $i+=$larger_page_multiple ) {
				$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
				$output .= '<a href="paged='.$i.'" class="single_page page-numbers" title="'.$page_text.'">'.$page_text.'</a>';
			}
		}
		$output .= $after;
		}

		return $output;
	}
?>
