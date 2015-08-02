<?php
$post_type = get_post_type( $post->ID );
if ($post_type == "portfolio"){
	require_once( THEME_DIR . "/core/portfolio-cols.php");
	wp_enqueue_script( 'cws-script-portfolio-js', THEME_URI . '/core/js/portfolio.js', array('jquery') );
	$cws_stored_meta = get_post_meta( $post->ID, 'cws-portfolio' );
}
$second_line = '';

	$sb = cws_GetSbClasses($post->ID);
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
					<div class="cws_widget_content">
						<section class="news blog-post">
							<?php
								while ( have_posts() ) : the_post();
									?>
									<div class="item">
										<?php
											cws_post_output($sb_block, 'large', '2', $post);
											cws_page_links();
										?>
									</div>
									<?php
								endwhile;
							?>
						</section>
					</div>
				</div>
				<?php
					if ($post_type == "portfolio"){
						$cws_stored_meta = $cws_stored_meta[0];
						if (isset($cws_stored_meta["cws-portfolio-use_rel_projects"])){
							$title = isset( $cws_stored_meta["cws-portfolio-rel_projects_title"] ) ? $cws_stored_meta["cws-portfolio-rel_projects_title"] : "";
							$postspp = isset( $cws_stored_meta["cws-portfolio-rel_projects_num"] ) ? $cws_stored_meta["cws-portfolio-rel_projects_num"] : "";
							echo "<div class='grid-row'>";
							echo "<section class='photo_tour_section cws_widget'>";
							echo render_portfolio_carousel ($postspp, $title, '', 'portfolio');
							echo "</section>";
							echo "</div>";
						}
					}
				comments_template();
			?>
			</main>
		</div>
	</div>

<?php get_footer(); ?>