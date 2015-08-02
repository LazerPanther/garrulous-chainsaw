<?php
/**
 * Displays the 3 step configuration page for BruteProtect and connection to my.bruteprotect.com
 *
 * @package BruteProtect
 *
 * @since 1.0
 */

global $brute_success, $brute_error, $privacy_opt_in, $remote_security_options, $local_host, $bruteprotect_api_key;

$local_host = str_replace( 'http://', '', home_url() );
$local_host = str_replace( 'https://', '', $local_host );

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' ) {
    // load processing scripts if necessary
    require 'mybp-post_processing.php';
}

// grab default variables
$bruteprotect_api_key = get_site_option( 'bruteprotect_api_key' );
$invalid_key = false;
$remote_security_options = array(
    'remote_updates' => __( 'Remotely track the versions of WordPress, plugins, & themes you have installed, & remotely update your site' ),
	'uptime_monitor' => __( 'Remotely monitor your site uptime.' ),
);

// reset any errors
delete_site_option( 'bruteprotect_error' );
// check the api
$response = $this->brute_call( 'check_key' );

// determine if the api key is valid, show error message if needed
if ( isset( $response[ 'error' ] ) ) :
    if ( $response[ 'error' ] == 'Invalid API Key' ) :
        $invalid_key = true;
        if ( empty( $brute_error ) ) {
            $brute_error = 'Sorry, your API Key is invalid';
        }
    endif;
    if ( $response[ 'error' ] == 'Host match error' ) :
        $invalid_key = true;
        // their api key is used on an other site. no error message is required. just prompt them to get a new key
    endif;
    if ( $response[ 'error' ] == 'API Key Required' ) :
        $invalid_key = true;
        // they don't have a key yet. no error message is required.
    endif;
endif;

// save info from api
bruteprotect_save_pro_info( $response );
$privacy_opt_in = get_site_option( 'brute_privacy_opt_in' );
// load in override styles
?>


<?php if ( !empty( $brute_error ) ) : ?>
    <div class="error">
        <?php _e( $brute_error ); ?>
    </div>
<?php endif; ?>
<?php if ( !empty( $brute_success ) && empty( $brute_error ) ) : ?>
    <div class="alert-box success">
        <?php _e( $brute_success ); ?>
    </div>
<?php endif; ?>
<?php

if ( isset( $_GET[ 'force_mybp_step' ] ) ) {
    $acceptable_steps = array( 'register', 'step_1', 'step_2', 'step_3' );
    $step = $_GET[ 'force_mybp_step' ];
    if ( !in_array( $step, $acceptable_steps ) ) {
        $step = 'step_3';
    }
    include 'mybp-sections/' . $step . '.php';

    return;
}

// determine where we are in the BruteProtect process
// step 1: get key
// step 2: save privacy settings
// step 3: link your site to my.bruteprotect.com
// we will include an output file based on what step we are on

if ( $invalid_key === true ) {
    // we are not working with a valid api key, let's prompt them to generate a new one
    include 'mybp-sections/step_1.php';

    return;
}

$brute_privacy_options_saved = get_site_option( 'brute_privacy_options_saved', false );
if ( empty( $brute_privacy_options_saved ) ) {
    // there is no evidence of the user setting their privacy settings, we need to prompt them to save their settings
    include 'mybp-sections/step_2.php';

    return;
}

// if the api key is valid and there are privacy settings, let's show the tabs
include 'mybp-sections/step_3.php';
return;
?>