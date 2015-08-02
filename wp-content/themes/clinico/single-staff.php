<?php
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
					<section class="news blog-post staff">
						<div class="item">
							<?php
							while ( have_posts() ): the_post();
								$cws_stored_meta = get_post_meta( get_the_ID(), 'cws-staff');
								$cws_stored_meta = $cws_stored_meta[0];
								$social_meta = $cws_stored_meta['social'];

								$thumbnail = has_post_thumbnail( $post->ID ) ? wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ),'full') : null;
								$thumbnail = $thumbnail ? $thumbnail[0] : null;
								$thumbnail = bfi_thumb( $thumbnail, array( 'width'=>250, 'height'=>250 ) );
								echo "<div class='wrapper'>";
								echo $thumbnail ? "<div class='pic'><img src='$thumbnail' /></div>" : "";
								echo "<div class='social-icons'>";
								for ($i=0;$i<count($social_meta);$i++){
									if ( (!empty($social_meta[$i]['cws-mb-socialgroup-fa'])) && (!empty($social_meta[$i]['cws-mb-socialgroup-url'])) ) {
										?>
										<span class="icon">
											<a href="<?php echo $social_meta[$i]['cws-mb-socialgroup-url'] ?>">
												<i class="fa fa-<?php echo $social_meta[$i]['cws-mb-socialgroup-fa']; ?>"></i>
											</a>
										</span>
										<?php
									}
								}
								echo "</div>";
								echo "</div>";
								$title = get_the_title();
								if ($title){
									?>
										<div class="widget-title"><?php echo $title; ?></div>
									<?php
								}
								echo apply_filters('the_content', $post->post_content);


								$staff_info = array(
									__('departments', THEME_SLUG ) => wp_get_post_terms( get_the_ID(), 'cws-staff-dept'),
									__('positions', THEME_SLUG ) => wp_get_post_terms( get_the_ID(), 'cws-staff-position'),
									__('treatments', THEME_SLUG ) => wp_get_post_terms( get_the_ID(), 'cws-staff-treatments'),
									__('procedures', THEME_SLUG ) => wp_get_post_terms( get_the_ID(), 'cws-staff-procedures'),
									__('degree', THEME_SLUG ) => isset( $cws_stored_meta['cws-staff-degree'] ) ? esc_attr( $cws_stored_meta['cws-staff-degree'] ) : '',
									__('office', THEME_SLUG ) => isset( $cws_stored_meta['cws-staff-office'] ) ? esc_attr( $cws_stored_meta['cws-staff-office'] ) : '',
									__('workingdays', THEME_SLUG ) => isset( $cws_stored_meta['cws-staff-workingdays'] ) ? $cws_stored_meta['cws-staff-workingdays'] : array()
								);
								echo "<section class='cats_group'>";
									$dow = array( __("Sunday",THEME_SLUG) , __("Monday",THEME_SLUG), __("Tuesday", THEME_SLUG), __("Wednesday", THEME_SLUG), __("Thursday", THEME_SLUG), __("Friday", THEME_SLUG), __("Saturday", THEME_SLUG) );
									foreach ($staff_info as $k => $v){
										if (!empty($v)){
											echo "<div class='cats'>";
												echo "<span class='cats_section_name'>$k</span>: ";
												if (is_array($v)){
													for($i=0;$i<count($v);$i++) {
														if($k == __('workingdays', THEME_SLUG )){
															echo $dow[(int)$v[$i]];
														}
														else{
															echo $v[$i]->name;
														}
														echo $i<count($v)-1 ? ", " : "";
													}													
												}
												else{
													echo $v;
												}
											echo "</div>";
										}
									}
								echo "</section>";

							endwhile;
							?>
						</div>
					</section>
				</div>
				<?php comments_template(); ?>
			</main>
		</div>
	</div>

<?php get_footer(); ?>