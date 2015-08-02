<?php
if (isset($_GET['asearch'])) {
		get_template_part('search-staff');
		return;
	}
	$cws_stored_meta = get_post_meta( $post->ID, 'cws-mb' );
	if (isset( $cws_stored_meta[0]['cws-mb-sb_override'] )) {
		get_template_part('blog');
		return;
	}

	get_header();

	$pid = get_query_var("page_id");
	$pid = !empty($pid) ? $pid : get_queried_object_id();
	$sb = cws_GetSbClasses($pid);
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
			<?php
				if (have_posts()):
					while ( have_posts() ): the_post();
						the_content();
					endwhile;
				endif;
			?>
			<?php comments_template(); ?>

		</main>
		</div>
	</div>

<?php get_footer(); ?>