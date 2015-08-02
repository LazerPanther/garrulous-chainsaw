<?php
/**
 * Events Navigation Bar Module Template
 * Renders our events navigation bar used across our views
 *
 * $filters and $views variables are loaded in and coming from
 * the show funcion in: lib/tribe-events-bar.class.php
 *
 * @package TribeEventsCalendar
 * @since  3.0
 * @author Modern Tribe Inc.
 *
 */
?>

<?php

$filters = tribe_events_get_filters();
$views   = tribe_events_get_views();

global $wp;
$current_url = esc_url( add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) );

 ?>

<?php //do_action('tribe_events_bar_before_template') ?>

<script type="text/javascript">
	jQuery("#tribe-events-bar").remove();
</script>

<div id="tribe-events-bar"  <?php if ( count( $views ) == 1 ) { ?> class="tribe-bar-collapse-toggle-full-width" <?php } ?>>
	<form id="tribe-bar-form" class="tribe-clearfix"  name="tribe-bar-form" method="post" action="<?php echo esc_attr( $current_url ); ?>">

		<!-- Mobile Filters Toggle -->
		<div id="tribe-bar-header">
			<div id="tribe-bar-collapse-toggle">
				<i class="fa <?php echo tribe_get_option('tribeDisableTribeBar') ? 'fa-calendar' : 'fa-search'; ?>"></i>
			</div>

			<!-- Views -->
			<?php if ( count( $views ) > 1 ) { ?>
			<div id="tribe-bar-views">
				<div class="tribe-bar-views-inner tribe-clearfix">
					<div class="icons_part">
						<div class="view_icons">
							<?php foreach ( $views as $view ) : ?>
								<?php echo "<a class='view_icon " . strtolower($view['anchor']) . " " . ( tribe_is_view( str_replace( "list","upcoming",strtolower( $view['anchor'] ) ) ) ? "selected " : "" ) . "' href='" . $view['url'] . "'> " . "<div class='view_icon_tooltip'>" . strtolower($view['anchor']) . "</div>" . " </a>"; ?>
							<?php endforeach; ?>
						</div>
					</div>


						<?php
						/*******************************************************************/
						ob_start();
						?>
							<h2 class="tribe-events-page-title"><?php tribe_events_title() ?></h2>
						<?php
						$title_content = ob_get_clean();
						/*******************************************************************/
						?>

						<?php do_action( 'tribe_events_before_header' );
						/*******************************************************************/
						$registered_views = tribe_events_get_views();
						foreach ( $registered_views as $view ) {
							$current_view = $view["displaying"];
							if ( tribe_is_view ( $current_view ) ) $selected_view = $current_view;
						}
						$selected_view = isset( $selected_view ) ? $selected_view : "month";
						$selected_view = $selected_view == "upcoming" ? "list" : $selected_view;
						/*******************************************************************/
						?>
						<div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>
							<?php do_action( 'tribe_events_before_the_title' ) ?>
							<?php 
								ob_start();
								tribe_get_template_part( in_array($selected_view,array("month","list")) ? $selected_view . "/nav" : "pro/" . $selected_view . "/nav" );
								$header_part = ob_get_clean();
								$pos = stripos($header_part,"</li>");
								if ( $pos ){
									$pos += 5;
								}
								else{
									$pos = stripos($header_part,"</ul>");
								}
								if (!$pos) $pos = 0;
								$header_content = substr_replace($header_part, $title_content, $pos, 0);  
								echo $header_content;
							?>
							<?php do_action( 'tribe_events_after_the_title' ) ?>
						</div>
						<?php
						do_action( 'tribe_events_after_header' );
						?>



				</div><!-- .tribe-bar-views-inner -->
			</div><!-- .tribe-bar-views -->
		<?php } // if ( count( $views ) > 1 ) ?>
		</div>

		<?php if ( !empty( $filters ) ) { ?>
		<div class="tribe-bar-filters" style="display:none;">
			<div class="tribe-bar-filters-inner tribe-clearfix">
				<?php foreach ( $filters as $filter ) : ?>
					<div class="<?php echo esc_attr( $filter['name'] ) ?>-filter">
						<label class="label-<?php echo esc_attr( $filter['name'] ) ?>" for="<?php echo esc_attr( $filter['name'] ) ?>"><?php echo $filter['caption'] ?></label>
						<?php echo $filter['html'] ?>
					</div>
				<?php endforeach; ?>
				<div class="tribe-bar-submit">
					<input class="tribe-events-button tribe-no-param" type="submit" name="submit-bar" value="<?php _e( 'Find Events', 'tribe-events-calendar' ) ?>" />
				</div><!-- .tribe-bar-submit -->
			</div><!-- .tribe-bar-filters-inner -->
		</div><!-- .tribe-bar-filters -->
		<?php } // if ( !empty( $filters ) ) ?>

	</form><!-- #tribe-bar-form -->

</div><!-- #tribe-events-bar -->
<?php //do_action('tribe_events_bar_after_template') ?>
