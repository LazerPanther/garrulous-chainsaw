<?php
function title_filter( $where, &$wp_query ) {
	global $wpdb;
	if ( $search_term = $wp_query->get( 'cws_search_title' ) ) {
		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like( $search_term ) ) . '%\'';
	}
	return $where;
}

if (isset($_GET['asearch'])) {
	$blogtype = 'small';
	$posts_per_page = (int)get_option('posts_per_page');
	$paged = !empty($_GET['paged']) ? (int)$_GET['paged'] : 1;
	$args = array('post_type' => 'staff',
						'post_status' => 'publish',
						'ignore_sticky_posts' => false,
						'posts_per_page' => $posts_per_page,
						'paged' => $paged);

	if ( !empty($_GET['docname']) ) {
		$args['cws_search_title'] = $_GET['docname'];
	}

	if ( !empty($_GET['cws-stafftreatments']) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'cws-staff-treatments',
				'field' => 'slug',
				'terms' => $_GET['cws-stafftreatments'],
			)
		);
	}

	add_filter( 'posts_where', 'title_filter', 10, 2 );
	$r = new WP_Query($args);
	remove_filter( 'posts_where', 'title_filter', 10, 2 );

	$total_post_count = $r->found_posts;
	$max_page = $total_post_count % $posts_per_page ? ceil( $total_post_count / $posts_per_page ) : $total_post_count / $posts_per_page ;
	$sb = array(
		'sidebar_pos' => cws_get_option("def-page-layout"),
		'sidebar1' => cws_get_option("def-page-sidebar1"),
		'sidebar2' => cws_get_option("def-page-sidebar2")
	);
	$sb_block = $sb['sidebar_pos'];

		get_header();
		$class_container = 'page-content' . (cws_has_sidebar_pos($sb_block) ? ( 'both' == $sb_block ? ' double-sidebar' : ' single-sidebar' ) : '');
		?>
		<div class="<?php echo $class_container; ?>">
			<div class="container">
			<?php
				if (cws_has_sidebar_pos($sb_block)) {
					if ('both' == $sb_block) {
						echo '<aside class="sbleft">';
						dynamic_sidebar($sb['sidebar1']);
						echo '</aside>';
						echo '<aside class="sbright">';
						dynamic_sidebar($sb['sidebar2']);
						echo '</aside>';
					} else {
						echo '<aside class="sb'.$sb_block.'">';
						dynamic_sidebar($sb['sidebar1']);
						echo '</aside>';
					}
				}
			?>
			<main>
				<div class="grid-row">
					<?php if ($r->have_posts()){ ?>
						<section class="news <?php echo $blogtype ? 'news-'. $blogtype : ''; ?> staff_results">
							<div class="grid clearfix">
								<?php
									if ($r->have_posts()){
										while ($r->have_posts()):
											$r->the_post();
											$curr_post = $r->posts[$r->current_post];
											?>
											<div class='item clearfix'>
												<?php
													cws_output_media_part($blogtype,2,$sb_block, $curr_post);
													$title = get_the_title();
													echo $title ? "<div class='widget-title'>" . $title . "</div>" : "";
													echo cws_post_content_output(500);
													$positions = wp_get_post_terms( get_the_ID(), 'cws-staff-position');
													echo "<div class='cats'>" . __("Positions: ",THEME_SLUG);
													for($i=0;$i<count($positions);$i++) {
														echo $positions[$i]->name;
														echo $i<count($positions)-1 ? ", " : "";
													}
													echo "<a href=" . get_permalink() . " class='more fa fa-long-arrow-right'></a>";
													echo "</div>";
												?>
											</div>
											<?php
										endwhile;
									}
								?>
							</div>
							<?php cws_pagination($paged, $max_page); ?>
						</section>
					<?php
					}
					else{
						?>
						<div class="cws_widget_content">
							<section class="news blog-post no-search-results">
								<div class="item">
									<?php
									echo "<div class='widget-title'>" . __( "No search results", THEME_SLUG ) . "</div>";
									echo apply_filters( "the_content", __( "Sorry, but nothing matched your search terms. Please try again with some different keywords.", THEME_SLUG ) . "</p>" );
									?>
								</div>
							</section>
						</div>
						<?php
					}
					?>
				</div>
				<?php comments_template(); ?>
			</main>
		</div>
	</div>
<?php
	get_footer();
}
else {
	die();
}
?>
