<?php
	if (!empty($_POST['ajax'])) {
		require_once('../../../wp-config.php');
	}

/**************************** VARIABLES *********************************/
	$post_id = !empty($_POST['post_id']) ? $_POST['post_id'] : $post->ID;
	$cws_stored_meta = get_post_meta( $post_id, 'cws-mb' );
	$cws_stored_meta = isset($cws_stored_meta[0]) ? $cws_stored_meta[0] : array();
	$blogtype = isset($cws_stored_meta["cws-mb-blogtype"]) ? $cws_stored_meta["cws-mb-blogtype"] : "medium";
	$pinterest_layout = (isset($cws_stored_meta["cws-mb-pinterest_layout"]) && ($blogtype == 'pinterest')) ? $cws_stored_meta["cws-mb-pinterest_layout"] : '';
	$category = isset($cws_stored_meta["cws-mb-category"]) ? implode(",", $cws_stored_meta["cws-mb-category"]) :null;
	$posts_per_page = (int)get_option('posts_per_page');
	$paged = !empty($_POST['paged']) ? (int)$_POST['paged'] : (!empty($_GET['paged']) ? (int)$_GET['paged'] : ( get_query_var("paged") ? get_query_var("paged") : 1 ) ) ;

	$sticky = (1 === $paged) ? array() : get_option( 'sticky_posts' );

	$post_type_array = array("post");
	$events_in_blog = false;
	if ( function_exists('tribe_get_option') ){
		$events_in_blog = tribe_get_option("showEventsInMainLoop");
		if ( $events_in_blog ) array_push($post_type_array, "tribe_events");
	}
	$args = array("post_type"=>$post_type_array,
					'post_status' => 'publish',
					'ignore_sticky_posts' => false,
					'post__not_in' => $sticky,
					'posts_per_page'=>$posts_per_page,
					'paged'=>$paged);
	/***************** Techinical Category *****************/
	$tech_cat = cws_get_option('tech-category');
	$tech_cat = isset( $tech_cat ) ? $tech_cat : array();
	$tech_cat_array = $tech_cat; // copy
	if ( count( $tech_cat ) > 0 ){
		for ( $i = 0; $i < count( $tech_cat ); $i ++ ){
			$tech_cat[$i] = '-' . $tech_cat[$i];
		}
		$tech_cat = implode( ',', $tech_cat );
		$args["cat"] = $tech_cat;
	}
	if ( !empty($category) ){
		$category = explode( ",", $category );
		for ( $i = 0; $i < count( $category ); $i ++ ){
			$category[$i] = get_category_by_slug( $category[$i] )->term_id;
		}
		for ( $i = 0; $i < count( $category ); $i ++ ){
			if ( in_array( $category[$i], $tech_cat_array ) ) array_splice( $category, $i, 1 );
		}
		if ( count( $category ) > 0 ){
			$category = implode( ",", $category );
			if (isset($args["cat"])){
				$args["cat"] .= "," . $category;
			}
			else{
				$args["cat"] = $category;
			}
		}
	}
	/*****************\ Techinical Category *****************/
	$r = new WP_Query($args);
	$total_post_count = $r->found_posts;
	$max_paged = $total_post_count % $posts_per_page ? ceil( $total_post_count / $posts_per_page ) : $total_post_count / $posts_per_page ;
	$sb = cws_GetSbClasses($post_id);
	$sb_block = $sb['sidebar_pos'];
/****************************\ VARIABLES *********************************/
/**************************** IF NOT AJAX *********************************/
	if (empty($_POST['ajax'])):
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
				<?php
					echo apply_filters('the_content', get_post($post_id)->post_content);
				?>
				<div class="grid-row">
					<section class="news <?php echo $blogtype ? 'news-'. $blogtype : '';?> <?php echo !empty($pinterest_layout) ? 'news-' . $pinterest_layout : ''; ?>">
						<div class="grid isotope">
							<?php
							endif;
/****************************\ IF NOT AJAX *********************************/
							if (!empty($_POST['ajax'])) echo "<div>";
							cws_blog_output($r, $total_post_count, $posts_per_page, $blogtype, $pinterest_layout, $sb_block, $paged);
							if (!empty($_POST['ajax'])) echo "</div>";
/**************************** IF NOT AJAX *********************************/
							if (empty($_POST['ajax'])) :
							 ?>
							</div>
							<?php
							if ( $max_paged>1 ){
								if ( $blogtype == 'pinterest' ){
									cws_load_more(".news .grid",strval($paged),$max_paged,$post_id);
								}
								else{
									cws_pagination($paged,$max_paged);
								}
							}
							?>
						</section>
					</div>
					<?php comments_template(); ?>
			</main>
		</div>
	</div>
<?php get_footer();
endif;
/****************************\ IF NOT AJAX *********************************/
?>
