<?php
	if ( post_password_required() ) { ?>
		<p class="no-comments"><?php __('Sorry, this post is password protected.', THEME_SLUG); ?></p>
	<?php
		return;
	}
?>
<?php
$show_comments = cws_get_option('show_comments');
$comments_opened = comments_open(get_queried_object()->ID);
$bIsCommentAllowed = is_page() ? isset($show_comments['pages']) && $comments_opened : isset($show_comments['posts']) && $comments_opened;
if ($bIsCommentAllowed) {
	$comment_form_args = array ('comment_notes_before' => '',
								'comment_notes_after' => '',
								'title_reply' => __( 'Leave a Comment', THEME_SLUG )
								);
	$comment_list_args = array('type'=>'comment',
								'avatar_size'=>'70',
								'callback'=>'cws_clinico_comment_callback',
								'style'=>'div');
	ob_start();
	echo "<div class='grid-row'>";
	if ( have_comments() ) :
?>
	<section class="comments-part">

			<div class='widget-title'><?php echo __('Comments (', THEME_SLUG) . get_comments_number() . ')'; ?></div>

			<div class="comments">
				<?php wp_list_comments($comment_list_args); ?>
			</div>

			<?php
			$nav_prev = get_previous_comments_link( __('Older Comments', THEME_SLUG));
			$nav_next = get_next_comments_link( __('Newer Comments', THEME_SLUG));
			if( $nav_prev || $nav_next ) {
			?>
				<div class="pagination">
					<?php if ($nav_prev) {?>
						<?php echo $nav_prev; ?>
					<?php } ?>
					<?php if ($nav_next) {?>
						<?php echo $nav_next; ?>
					<?php } ?>
				</div>
			<?php } ?>

			<?php comment_form($comment_form_args, get_queried_object()->ID); ?>

	</section>
<?php else :
	if ( !$comments_opened ) : ?>
		<p class="no-comments"><?php __('Comments are closed.', THEME_SLUG); ?></p>
	<?php else: ?>
		<section class="comments-part">
				<?php comment_form($comment_form_args, get_queried_object()->ID); ?>
		</section>
	<?php endif; ?>
<?php endif;
	echo "</div>";
	ob_end_flush();
	}
?>