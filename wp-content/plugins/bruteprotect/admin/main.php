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
    require 'save-configuration.php';
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

if ( $invalid_key === true ) {
    // we are not working with a valid api key, let's prompt them to generate a new one
    include 'inc/get_api_key.php';
	return;
}

// if the api key is valid lets show configuration
include 'inc/configuration.php';
return;
?>