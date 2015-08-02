<?php 
if (isset($_GET['asearch'])) {
		get_template_part('search-staff');
		return;
	}
?>
<?php get_header(); ?>
	<div class="page-content">
		<div class="container">
			<main>
				<div class='grid-row' id="header-404">
					<div id='title-404'>
						<?php _e("oh no! it&#8217;s a", THEME_SLUG); ?>
					</div>
				</div>
				<div class='grid-row' id="block-404">
					<div id="block-404-substrate">
					</div>
					<div id="block-404-icon">
					</div>
				</div>
				<div class='grid-row' id="text-404">
					<div class='sel'>
					<?php _e("Looks like the page you are looking for does not exists", THEME_SLUG); ?>
					</div>
					<?php _e("If you come here from a bookmark, please remember to update your bookmark", THEME_SLUG); ?>
				</div>
				<div class='grid-row' id="button-404">
					<a class="cws_button arrow" href="<?php echo home_url(); ?>">
					<?php _e("go back to home page", THEME_SLUG); ?>
					</a>
				</div>
			</main>
		</div>
	</div>
<?php get_footer(); ?>