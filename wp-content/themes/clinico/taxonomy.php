<?php
if (isset($_GET['asearch'])) {
		get_template_part('search-staff');
		return;
}
	get_header();
	?>
	<div class="page-content">
		<div class="container">
		<main>
		<?php
			$txn = get_query_var( 'taxonomy' );
			$term = get_query_var( 'term' );
			$pg = get_query_var('page');
			$pg = ($pg === 0) ? 1 : $pg;
			$posts_per_page = (int)get_option('posts_per_page');
			$args = array('post_status' => 'publish',
					'post_type' => 'staff',
					'posts_per_page'=>$posts_per_page,
					'tax_query' => array(
						array(
							'taxonomy' => $txn,
							'field' => 'slug',
							'terms' => $term,
						),
					),
					'paged'=>$pg);
			$r = new WP_Query($args);
			$total_post_count = $r->found_posts;
			$max_paged = $total_post_count % $posts_per_page ? ceil( $total_post_count / $posts_per_page ) : $total_post_count / $posts_per_page ;
			$output = '';

			if ($r->have_posts()) {
				$output .= '<div class="cws_widget"><div class="cws_widget_content">';
				$output  .= !empty( $title ) ? "<div class='widget-title'>$title</div>" : "";
				$output .= '<div class="our_team">';
				$output .= '<div class="grid isotope">';
				while ( $r->have_posts() ) {
					$r->the_post();
					$output .= '<div class="item">';
					$output .= '<div class="pic">';
					$img = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
					$output .= '<img src="' . bfi_thumb($img[0], array('width' => 270, 'height' => 270) ) . '" alt="">';
					$output .= '<div class="hover-effect"></div>';
					$output .= '<div class="links">';
					$cws_stored_meta = get_post_meta( get_the_ID(), 'cws-staff');
					$cws_stored_meta = isset( $cws_stored_meta[0]['social'] ) ? $cws_stored_meta[0]['social'] : array();

					if (count($cws_stored_meta)>0) {
						foreach ($cws_stored_meta as $social_item) {
							$url = $social_item['cws-mb-socialgroup-url'];
							$title = $social_item['cws-mb-socialgroup-title'];
							$fa =  $social_item['cws-mb-socialgroup-fa'];
							$output .= '<a ' . ( $url ? "href='$url' " : "" ) . ( $title ? "title='$title' " : "" ) . ( $fa ? "class='fa fa-$fa' " : "" ) . '></a>';
						}
					}

					$output .= "</div></div><div class='team_member_info'>";

					$name = get_the_title();
					$link = get_permalink();
					$output .= $name ?  "<a href='$link'><div class='name'>" . $name . "</div></a>" : "";

					$terms = wp_get_post_terms(get_the_ID(), 'cws-staff-position');
					if ( count($terms) ):
						$output .= "<div class='positions'>";
						$i = 0;
						foreach ($terms as $k=>$v) {
							$i++;
							$output .= $v->name;
							if ($i < count($terms)) {
								$output .= ', ';
							}
						}
						$output .= "</div>";
					endif;

					$output .= "</div></div>";
				}
				$output .= "</div></div></div></div>";
			}
			echo $output;
			$pagination = paginate_links(
				array(
					'format' => '?page=%#%',
					'current' => max( 1, get_query_var('page') ),
					'total' => $r->max_num_pages
				)
			);
			echo "<div class='pagination'>" . $pagination . "</div>";
		?>
		</main>
		</div>
	</div>

<?php get_footer(); ?>