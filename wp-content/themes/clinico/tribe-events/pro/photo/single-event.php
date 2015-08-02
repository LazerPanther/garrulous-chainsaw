<?php 
/**
 * Photo View Single Event
 * This file contains one event in the photo view
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/photo/single-event.php
 *
 * @package TribeEventsCalendar
 * @since  3.0
 * @author Modern Tribe Inc.
 *
 */

if ( !defined('ABSPATH') ) { die('-1'); } ?>

<?php 

global $post;

 ?>

<div class="tribe-events-photo-event-wrap">

 <div class="tribe-events-event-details tribe-clearfix">

 	<!-- Event Title -->
	<?php do_action( 'tribe_events_before_the_event_title' ); ?>
	<h2 class="tribe-events-list-event-title entry-title summary">
		<a class="url" href="<?php echo tribe_get_event_link() ?>" title="<?php the_title() ?>" rel="bookmark">
			<?php the_title(); ?>
		</a>
	</h2>
	<?php do_action( 'tribe_events_after_the_event_title' ); ?>

	<!-- Event Image -->
	<?php
		$image = tribe_event_featured_image( );
		$r= preg_match("/<img.*src=\"([^\"]+)/",$image,$matches);
		$dims = cws_get_post_tmumbnail_dims("pinterest", 4, null);
		$url = isset($matches[1]) ? $matches[1] : "";
		$bfi_url = bfi_thumb( $url, array('width'=>$dims['width'],'height'=>$dims['height']) );
		echo str_replace($url, $bfi_url, $image);
	?>

	<!-- Event Content -->
	<?php do_action( 'tribe_events_before_the_content' ); ?>
	<div class="tribe-events-list-photo-description tribe-events-content entry-summary description">
		<?php echo tribe_events_get_the_excerpt() ?>
	</div>
	<?php do_action( 'tribe_events_after_the_content' ) ?>

	<!-- Event Meta -->
	<?php do_action( 'tribe_events_before_the_meta' ); ?>
		<div class="tribe-events-event-meta">
			<div class="updated published time-details">
				<?php if ( ! empty( $post->distance ) ) : ?>
				<strong>[<?php echo tribe_get_distance_with_unit( $post->distance ); ?>]</strong>
				<?php endif; ?>
				<?php echo tribe_events_event_schedule_details(); ?>
			</div>
		</div><!-- .tribe-events-event-meta -->
	<?php do_action( 'tribe_events_after_the_meta' ); ?>

</div><!-- /.tribe-events-event-details -->

</div><!-- /.tribe-events-photo-event-wrap -->
