
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
				echo do_shortcode('[ourteam mode=\'all\' /]');
			?>
		</main>
		</div>
	</div>

<?php get_footer(); ?>
