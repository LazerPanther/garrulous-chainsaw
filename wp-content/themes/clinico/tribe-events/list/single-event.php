<?php 
/**
 * List View Single Event
 * This file contains one event in the list view
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/single-event.php
 *
 * @package TribeEventsCalendar
 * @since  3.0
 * @author Modern Tribe Inc.
 *
 */

if ( !defined('ABSPATH') ) { die('-1'); } ?>

<?php 

// Setup an array of venue details for use later in the template
$venue_details = array();

if ($venue_name = tribe_get_meta( 'tribe_event_venue_name' ) ) {
	$venue_details[] = $venue_name;	
}

if ($venue_address = tribe_get_meta( 'tribe_event_venue_address' ) ) {
	$venue_details[] = $venue_address;	
}
// Venue microformats
$has_venue = ( $venue_details ) ? ' vcard': '';
$has_venue_address = ( $venue_address ) ? ' location': '';
?>

<!-- Event Title -->
<?php do_action( 'tribe_events_before_the_event_title' ) ?>
<h2 class="tribe-events-list-event-title summary">
	<a class="url" href="<?php echo tribe_get_event_link() ?>" title="<?php the_title() ?>" rel="bookmark">
		<?php the_title() ?>
		<!-- Event Cost -->
		<?php if ( tribe_get_cost() ) : ?> 
			<?php
				$cost = tribe_get_cost( null, true );
				if ( !empty($cost)  ){
					echo "<span class='tribe-events-event-cost'>&nbsp;&#8212;&nbsp;$cost</span>";
				}
			?>
		<?php endif; ?>
	</a>
</h2>
<?php do_action( 'tribe_events_after_the_event_title' ) ?>

<!-- Event Image -->
<?php
	$image = tribe_event_featured_image( );
	$r= preg_match("/<img.*src=\"([^\"]+)/",$image,$matches);
	$dims = cws_get_post_tmumbnail_dims("medium", null, null);
	$url = isset($matches[1]) ? $matches[1] : "";
	$bfi_url = bfi_thumb( $url, array('width'=>$dims['width'],'height'=>$dims['height']) );
	echo str_replace($url, $bfi_url, $image);
?>

<!-- Event Content -->
<?php do_action( 'tribe_events_before_the_content' ) ?>
<div class="tribe-events-list-event-description tribe-events-content description entry-summary">
	<?php the_excerpt() ?>
</div><!-- .tribe-events-list-event-description -->

<!-- Event Meta -->
<?php do_action( 'tribe_events_before_the_meta' ) ?>
<div class="tribe-events-event-meta <?php echo $has_venue . $has_venue_address; ?>">

	<!-- Schedule & Recurrence Details -->
	<div class="updated published time-details">
		<?php echo tribe_events_event_schedule_details() ?>
	</div>
	
	<?php if ( $venue_details ) : ?>
		<!-- Venue Display Info -->
		<div class="tribe-events-venue-details">
			<?php echo implode( ', ', $venue_details) ; ?>
		</div> <!-- .tribe-events-venue-details -->
	<?php endif; ?>

	<a href="<?php echo tribe_get_event_link() ?>" class="more fa fa-long-arrow-right" rel="bookmark"></a>

</div><!-- .tribe-events-event-meta -->
<?php do_action( 'tribe_events_after_the_meta' ) ?>

<?php do_action( 'tribe_events_after_the_content' ) ?>