<?php get_header(); ?>

<div id="content">
	<div class="top"><div class="bottom">
	<div class="left"><div class="lpadding">
		<?php get_sidebar(); ?>
	</div></div>
	<div class="right">
		<?php if (have_posts()) : ?>
			
		<div style="margin: 10px;"><h3>Search Results</h3></div>

		<div class="post-nav"><?php next_posts_link('Next Results  &raquo;') ?>

		<?php previous_posts_link('&laquo; Previous Results') ?></div>

		<?php while (have_posts()) : the_post(); ?>
		
		<div class="title"><h1><a href="<?php the_permalink() ?>" target="_self"><?php the_title(); ?></a></h1></div>
		<!-- <div class="title2"><h4> <?php _e("Autor: "); ?><?php the_author() ?></h4><?php the_date('~ d/m/y','<h4>','</h4>'); ?><div class="clear"></div></div> -->
						
		<div class="post_block1"><div class="post_block2"><div class="post_block3"><div class="post_block4">
			<?php the_content(__('(more...)')); ?>
			<div class="clear"></div>
		</div></div></div></div>
		
        <!-- <div class="tags">Post tags: <?php the_tags('', ', ', ''); ?></div>
        
		<div class="permalink"><?php _e("Posted in:"); ?> <?php the_category(',') ?> | <?php edit_post_link(__('Edit')); ?> | <?php comments_popup_link(__('Comments (0)'), __('Comments (1)'), __('Comments (%)')); ?></div> -->
		
		<?php endwhile; ?>


			<div  class="post-nav"><?php next_posts_link('Next Results &raquo;') ?>

			<?php previous_posts_link('&laquo; Previous Results') ?></div>
			
		
		<?php else : ?>
	<br/>
			<div style="margin: 10px;"><h4>Nothing found. Try again.</h4></div>
			
			<?php/* include (TEMPLATEPATH . '/searchform.php'); */?>

		<div class="div1"></div>
		<?php endif; ?>

	</div>
	<div class="clear"></div>
	</div></div>
</div>

<?php get_footer(); ?>
