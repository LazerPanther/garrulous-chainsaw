<?php
$tmp = explode( 'wp-content', __FILE__ );
$wp_path = $tmp[0];
require_once( $wp_path . '/wp-load.php' );

require_once( 'sc-settings.php' );

$shortcode = trim( $_GET['shortcode'] );
$selection = '';
if ( isset($_GET['sel']) ) {
	$selection = stripslashes(trim( $_GET['sel'] ));
}

$theme_color = '#237dc8'; // should be adjusted dynamically

?>
<!DOCTYPE html>
<html>
<head>
	<script src="<?php echo THEME_URI . '/core/popup.js' ?>"></script>
</head>
<body>

<div class="cws-shortcode-container" id="cws-shortcode-container">
	<input type="hidden" name="cws-shortcode-type" id="cws-shortcode-type" value="<?php echo $shortcode ?>">
	<input type="hidden" name="cws-shortcode-selection" id="cws-shortcode-selection" value="<?php echo esc_attr($selection); ?>">
	<input type="hidden" name="cws-theme-color" id="cws-theme-color" value="<?php echo $theme_color ?>">

	<?php echo '<section id="cws-' . $shortcode . '" class="cws-shortcode-section">'; ?>
	<?php
	 echo cws_shortcode_html_gen($cws_shortcode_attr, $shortcode, $selection, '', true);
	?>
	</section>
	<br/>
	<input type="submit" class="button button-primary button-large" id="cws_insert_button" value="<?php _e('Insert Shortcode',THEME_SLUG) ?>">
</div>
<script>cws_shortcode_init();</script>
</body>
</html>