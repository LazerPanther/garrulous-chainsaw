<?php
/**
 * Map View Content
 * The content template for the map view of events. This template is also used for
 * the response that is returned on map view ajax requests.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/map/content.php
 *
 * @package TribeEventsCalendar
 * @since  3.0
 * @author Modern Tribe Inc.
 *
 */

if ( !defined('ABSPATH') ) { die('-1'); } ?>

<div id="tribe-events-content" class="tribe-events-list tribe-events-map">

		<?php
		tribe_events_the_notices();
		?>
	
		<!-- Events Loop -->
		<?php if ( have_posts() ) : ?>
			<?php do_action( 'tribe_events_before_loop' ); ?>
			<div id="tribe-geo-results" class="tribe-events-loop hfeed vcalendar">
			<?php tribe_get_template_part( 'pro/map/loop' ) ?>
			</div> <!-- #tribe-geo-results -->
			<?php do_action( 'tribe_events_after_loop' ); ?>
		<?php endif; ?>

		<!-- List Footer -->
		<?php do_action( 'tribe_events_before_footer' ); ?>
		<div id="tribe-events-footer">

			<!-- Footer Navigation -->
			<?php do_action( 'tribe_events_before_footer_nav' ); ?>
			<?php tribe_get_template_part( 'pro/map/nav', 'footer' ); ?>
			<?php do_action( 'tribe_events_after_footer_nav' ); ?>

		</div><!-- #tribe-events-footer -->
		<?php do_action( 'tribe_events_after_footer' ) ?>

</div><!-- #tribe-events-content -->
