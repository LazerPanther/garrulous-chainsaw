<?php get_header();?>

<div id="content">
	<div class="top"><div class="bottom">
	<div class="left"><div class="lpadding">
		<?php get_sidebar(); ?>
	</div></div>
	<div class="right">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<div class="title"><h1><a href="<?php the_permalink() ?>" target="_self"><?php the_title(); ?></a></h1></div>		
		
		<div class="post_block1"><div class="post_block2"><div class="post_block3"><div class="post_block4"><div class="post">
			<?php the_content(__('(more...)')); ?>
			</div>
			<div class="clear"></div>
		</div></div></div></div>

		<div class="div1"></div>

	<?php endwhile; else: ?>

	<?php endif; ?>

	<div class="post-nav"><?php posts_nav_link(' &#8212; ', __('&laquo; Newer Posts'), __('Older Posts &raquo;')); ?></div>
	

	</div>
	<div class="clear"></div>
	</div></div>
</div>

<?php get_footer(); ?>