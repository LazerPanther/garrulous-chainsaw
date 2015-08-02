<?php
/*	$cws_stored_meta = get_post_meta( $post->ID, 'cws-mb' );
	if (isset( $cws_stored_meta[0]['cws-mb-sb_override'] )) {
		get_template_part('blog');
		return;
	}*/
	$paged = !empty($_POST['paged']) ? (int)$_POST['paged'] : (!empty($_GET['paged']) ? (int)$_GET['paged'] : ( get_query_var("paged") ? get_query_var("paged") : 1 ) ) ;
	$posts_per_page = (int)get_option('posts_per_page');

	get_header();

	$pid = get_query_var("page_id");
	$pid = !empty($pid) ? $pid : get_queried_object_id();
	$sb = array(
		'sidebar_pos' => cws_get_option("def-page-layout"),
		'sidebar1' => cws_get_option("def-page-sidebar1"),
		'sidebar2' => cws_get_option("def-page-sidebar2")
	);
	$sb_block = $sb['sidebar_pos'];
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
					<?php
					global $wp_query;
					$total_post_count = $wp_query->found_posts;
					$max_paged = $total_post_count % $posts_per_page ? ceil( $total_post_count / $posts_per_page ) : $total_post_count / $posts_per_page ;
					if(have_posts()){
						?>
						<section class="news news-small">
							<div class="grid clearfix">
							<?php
								$bPagination = true;
								if (0 === strlen($wp_query->query_vars['s']) ) {
									echo "<div class='item clearfix'>";
									_e('Empty search string', THEME_SLUG);
									echo '</div></div>';
									echo '</section>';
									$bPagination = false;
								} else {
									while( have_posts() ) : the_post();
										$content = get_the_content();
										$content = strip_shortcodes(strip_cws_grid_shortcodes($content));
										$content = strip_tags($content);
										$content = preg_replace( "|\s+|", " ", $content );
										$title = get_the_title();

										$cont = '';
										$bFound = false;
										foreach ($search_terms as $term) {
											$pos = 0;
											$term_len = strlen($term);
											do {
												$pos = stripos($content, $term, $pos);
												if (FALSE !== $pos) {
													$start = ($pos > 50) ? $pos - 50 : 0;
												} else {
													$start = 0;
												}
												$temp = substr($content, $start, $term_len + 100) . " ... ";
												$cont .= $temp;
												if (!$pos) { break; }
												$pos += $term_len;
											} while (true);
										}

										if (strlen($cont) > 0) {
											$bFound = true;
										}
										else {
											foreach ($search_terms as $term) {
											$pos = 0;
											$term_len = strlen($term);
											do {
												$pos = stripos($title, $term, $pos);
												if (FALSE !== $pos) {
													$bFound = true;
													break;
												}
											} while ($pos);
											}
										}

										if (!empty($cont)){
											echo "<div class='item clearfix'>";
												$pattern = "#\[[^\]]+\]#";
												$replace = "";
												$cont = preg_replace($pattern, $replace, $cont);
												$cont = preg_replace('/('.implode('|', $search_terms) .')/iu', '<mark>\0</mark>', $cont);
												if (get_the_title()){
													?>
														<div class="widget-title">
														<?php echo ( !isset($post) ? "<a href='" . get_permalink() . "'>" : "" ) . get_the_title() . ( !isset($post) ? "</a>" : "" ); ?>
														</div>
													<?php
												}
												$cont = apply_filters('the_content', $cont);
												echo $cont;

												echo "<div class='cats'>" . __("Posted", THEME_SLUG);
												$categories = get_the_category($post->ID);
												$show_author = cws_get_option("blog_author");
												if ( !empty($categories) || $show_author ){
													if ( !empty($categories) ){
														echo "&nbsp;" . __("in", THEME_SLUG) . "&nbsp;";
														for($i=0;$i<count($categories);$i++) {
															echo "<a href='" . get_category_link($categories[$i]->cat_ID) . "'>" . $categories[$i]->name . "</a>";
															echo $i<count($categories)-1 ? ", " : "";
														}
													}
													if ( $show_author ){
														echo "&nbsp;" . __("by", THEME_SLUG) . "&nbsp;";
														$author = get_the_author();
														echo !empty($author) ? $author : "";
													}
												}
												echo "<a href='" . get_permalink() . "' class='more fa fa-long-arrow-right'></a>";
												echo "</div>";

											echo "</div>";
										}
									endwhile;
								}
								?>
							</div>
						</section>
						<?php
						//global $wp_query;
						if ($bPagination) {
							cws_pagination($paged,$max_paged);
						}
					}
					else {
						?>
						<div class="cws_widget_content">
							<section class="news blog-post no-search-results">
								<div class="item">
									<?php
									echo "<div class='widget-title'>" . __( "No search results", THEME_SLUG ) . "</div>";
									echo apply_filters( "the_content", __( "Sorry, but nothing matched your search terms. Please try again with some different keywords.", THEME_SLUG ) . "</p>" );
									get_search_form($search_terms);
									?>
								</div>
							</section>
						</div>
						<?php
					}
					?>
				</div>
			</main>
		</div>
	</div>
<?php get_footer(); ?>