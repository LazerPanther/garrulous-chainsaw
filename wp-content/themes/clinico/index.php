<?php
if (isset($_GET['asearch'])) {
		get_template_part('search-staff');
		return;
	}
	$cat = get_query_var("cat");
	$tag = get_query_var("tag");
/**************************** VARIABLES *********************************/
	$blogtype= "medium";
	$category = !empty($cat) ? array($cat) : cws_get_option("def-home-category");
	$posts_per_page = (int)get_option('posts_per_page');
	$paged = !empty($_POST['paged']) ? (int)$_POST['paged'] : (!empty($_GET['paged']) ? (int)$_GET['paged'] : ( get_query_var("paged") ? get_query_var("paged") : 1 ) ) ;

	$sticky = (1 === $paged) ? array() : get_option( 'sticky_posts' );

	$post_type_array = array("post");
	$events_in_blog = false;
	if ( function_exists('tribe_get_option') ){
		$events_in_blog = tribe_get_option("showEventsInMainLoop");
	}
	if ( $events_in_blog ) array_push($post_type_array, "tribe_events");
	$args = array("post_type"=>$post_type_array,
					'post_status' => 'publish',
					'ignore_sticky_posts' => false,
					'post__not_in' => $sticky,
					'posts_per_page'=>$posts_per_page,
					'paged'=>$paged);
	if ( is_date() ){
		$year = get_query_var( "year" );
		$month = get_query_var( "monthnum" );
		$day = get_query_var( "day" );
		if ( !empty( $year ) ){
			$args['year'] = $year;
		}
		if ( !empty( $month ) ){
			$args['monthnum'] = $month;
		}
		if ( !empty( $day ) ){
			$args['day'] = $day;
		}
	}
	/***************** Techinical Category *****************/
	$tech_cat = cws_get_option('tech-category');
	$tech_cat_array = $tech_cat; // copy
	if ( count( $tech_cat ) > 0 ){
		for ( $i = 0; $i < count( $tech_cat ); $i ++ ){
			$tech_cat[$i] = '-' . $tech_cat[$i];
		}
		$tech_cat = implode( ',', $tech_cat );
		$args["cat"] = $tech_cat;
	}
	if ( count($category)>0 ){
		for ( $i = 0; $i < count( $category ); $i ++ ){
			if ( in_array( $category[$i], $tech_cat_array ) ) array_splice( $category, $i, 1 );
		}
		if ( count( $category ) > 0 ){
			$category = implode( ",", $category );;
			if (isset($args["cat"])){
				$args["cat"] .= "," . $category;
			}
			else{
				$args["cat"] = $category;
			}
		}
	}
	/*****************\ Techinical Category *****************/
	if (!empty($tag)){
		$args["tag"] = $tag;
	}
	$r = new WP_Query($args);
	$total_post_count = $r->found_posts;
	$max_paged = $total_post_count % $posts_per_page ? ceil( $total_post_count / $posts_per_page ) : $total_post_count / $posts_per_page ;
	$sb = cws_GetSbClasses();
	$sb_block = $sb['sidebar_pos'];
/****************************\ VARIABLES *********************************/
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
					<section class="news <?php echo $blogtype ? 'news-'. $blogtype : '';?>">
						<div class="grid isotope">
							<?php
								cws_blog_output($r, $total_post_count, $posts_per_page, $blogtype, "", $sb_block, $paged);
							 ?>
							</div>
							<?php
							if ( $max_paged>1 ){
									cws_pagination($paged,$max_paged);
							}
							?>
						</section>
					</div>
			</main>
		</div>
	</div>
<?php get_footer();

?>
