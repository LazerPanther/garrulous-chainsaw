<?php

if ( isset( $_POST[ 'brute_action' ] ) && $_POST[ 'brute_action' ] == 'unlink_owner_from_site' ) {
    global $brute_success;

    $nonce = $_POST[ 'brute_nonce' ];
    if ( !wp_verify_nonce( $nonce, 'brute_unlink' ) ) {
        return;
    }

    $this->unlink_site();
    $brute_success = 'This site was successfully disconnected from My.BruteProtect.com';

    return;
}

if ( isset( $_POST[ 'brute_action' ] ) && $_POST[ 'brute_action' ] == 'get_api_key' ) {
    global $wp_version, $brute_error, $brute_success;
    $nonce = $_POST[ 'brute_nonce' ];

    if ( !wp_verify_nonce( $nonce, 'brute_get_key' ) ) {
        return;
    }

    if ( !is_email( $_POST[ 'email_address' ] ) ) {
        $brute_error = 'You did not enter a valid email address.';

        return;
    }

    $host = $this->brute_get_local_host();
    $post_host = $this->get_bruteprotect_host() . 'endpoints/get_key';
    $brute_ua = "WordPress/{$wp_version} | ";
    $brute_ua .= 'BruteProtect/' . constant( 'BRUTEPROTECT_VERSION' );

    $request[ 'email' ] = $_POST[ 'email_address' ];
    $request[ 'site' ] = $host;
    $request[ 'directory_url' ] = $this->is_subdirectory();
    $request[ 'url_protocol' ] = $this->brute_get_protocol();

    $args = array(
        'body'        => $request,
        'user-agent'  => $brute_ua,
        'httpversion' => '1.0',
        'timeout'     => 15
    );

    $response_json = wp_remote_post( $post_host, $args );

    if ( is_wp_error( $response_json ) ) {
        $brute_error = 'There was an error generating your API key.  Please try again later.  Sorry!';
    }


    if ( isset( $response_json[ 'response' ][ 'code' ] ) && $response_json[ 'response' ][ 'code' ] == 200 ) {
        $key = $response_json[ 'body' ];
        update_site_option( 'bruteprotect_api_key', $key );
        $brute_success = 'Your BruteProtect API Key was successfully created.';
        // when a new key is obtained, we should force them to set their privacy settings
        // even if they've had a key before
        delete_site_option( 'brute_privacy_options_saved' );

        return;
    } else {
        $brute_error = 'There was an error generating your API key.  Please try again later.  Sorry!';

        return;
    }
}

if ( isset( $_POST[ 'brute_action' ] ) && $_POST[ 'brute_action' ] == 'update_key' ) {
    global $brute_error, $brute_success;

    $nonce = $_POST[ 'brute_nonce' ];
    if ( !wp_verify_nonce( $nonce, 'brute_update_key' ) ) {
        return;
    }

    $apikey = $_POST[ 'brute_api_key' ];
    if ( strlen( $apikey ) != 40 ) {
        $brute_error = 'That is not a valid API key.';

        return;
    }
    // when an api key is updated, we should force them to set their privacy settings
    delete_site_option( 'brute_privacy_options_saved' );
    update_site_option( 'bruteprotect_api_key', $apikey );
    $brute_success = 'API key updated.';
}

if ( isset( $_POST[ 'brute_action' ] ) && $_POST[ 'brute_action' ] == 'remove_key' ) {
    global $brute_success, $current_user;
    $nonce = $_POST[ 'brute_nonce' ];
    if ( !wp_verify_nonce( $nonce, 'brute_remove_key' ) ) {
        return;
    }
    $delete_all = true;
    $this->unlink_site( $delete_all );
    $brute_success = 'API key removed.';

    return;
}

if ( isset( $_POST[ 'brute_action' ] ) && $_POST[ 'brute_action' ] == 'link_owner_to_site' ) {
    global $is_linking, $linking_error, $linking_success, $current_user;

    $nonce = $_POST[ 'brute_nonce' ];
    if ( !wp_verify_nonce( $nonce, 'brute_link_site' ) ) {
        return;
    }

    $is_linking = true;
    $action = 'link_with_access_token';
    $core_update = brute_protect_get_core_update();
    $plugin_updates = bruteprotect_get_out_of_date_plugins();
    $theme_updates = bruteprotect_get_out_of_date_themes();
    $additional_data = array(
        'access_token'   => $_POST[ 'access_token' ],
        'remote_id'      => strval( $current_user->ID ),
        'core_update'    => $core_update,
        'plugin_updates' => strval( count( $plugin_updates ) ),
        'theme_updates'  => strval( count( $theme_updates ) ),
    );
    $sign = true;

    $response = $this->brute_call( $action, $additional_data, $sign );
    if ( isset( $response[ 'link_key' ] ) ) {
        update_user_meta( $current_user->ID, 'bruteprotect_user_linked', $response[ 'link_key' ] );
        update_site_option( 'bruteprotect_user_linked', '1' );
        $linking_success = $response[ 'message' ];

        return;
    } else {
        $linking_error = $response[ 'message' ];

        return;
    }
}

// save privacy settings
if ( isset( $_POST[ 'brute_action' ] ) && $_POST[ 'brute_action' ] == 'privacy_settings' ) {
    global $privacy_success, $brute_success;

    $nonce = $_POST[ 'brute_nonce' ];
    if ( !wp_verify_nonce( $nonce, 'brute_privacy' ) ) {
        return;
    }

    update_site_option( 'brute_privacy_opt_in', $_POST[ 'privacy_opt_in' ] );
    $action = 'update_settings';
    $additional_data = array();
    $sign = true;
    $this->brute_call( $action, $additional_data, $sign );
    update_site_option( 'brute_privacy_options_saved', true );

    if ( isset( $_POST[ 'step_3' ] ) ) // show the message in step 3's tab
    {
        $privacy_success = 'Your privacy settings were saved.';
    } else // show the message up top
    {
        $brute_success = 'Your privacy settings were saved.';
    }

    return;
}

// process an general_update action which updates privacy settings. uses Bruteprotect::call()
if ( isset( $_POST[ 'brute_action' ] ) && $_POST[ 'brute_action' ] == 'general_update' ) {
    global $wordpress_success;

    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }

    $nonce = $_POST[ 'brute_nonce' ];
    if ( !wp_verify_nonce( $nonce, 'brute_general' ) ) {
        return;
    }

    // save dashboard widget settings
    if ( isset( $_POST[ 'brute_dashboard_widget_hide' ] ) ) {
        update_site_option( 'brute_dashboard_widget_hide', $_POST[ 'brute_dashboard_widget_hide' ] );
    }
    // save dashboard widget settings
    if ( isset( $_POST[ 'brute_dashboard_widget_admin_only' ] ) ) {
        update_site_option( 'brute_dashboard_widget_admin_only', $_POST[ 'brute_dashboard_widget_admin_only' ] );
    }
    $wordpress_success = 'Your WordPress settings were saved.';

}

if ( isset( $_POST[ 'brute_action' ] ) && $_POST[ 'brute_action' ] == 'update_brute_whitelist' ) {
    global $whitelist_success;

    $nonce = $_POST[ 'brute_nonce' ];
    if ( !wp_verify_nonce( $nonce, 'brute_whitelist' ) ) {
        return;
    }

    //check the whitelist to make sure that it's clean
    $whitelist = $_POST[ 'brute_ip_whitelist' ];

    $wl_items = explode( PHP_EOL, $whitelist );

    if ( is_array( $wl_items ) ) :  foreach ( $wl_items as $key => $item ) :
        $item = trim( $item );
        $ckitem = str_replace( '*', '1', $item );
        $ckval = ip2long( $ckitem );
        if ( !$ckval ) {
            unset( $wl_items[ $key ] );
            continue;
        }
        $exploded_item = explode( '.', $item );
        if ( $exploded_item[ 0 ] == '*' ) {
            unset( $wl_items[ $key ] );
        }

        if ( $exploded_item[ 1 ] == '*' && !( $exploded_item[ 2 ] == '*' && $exploded_item[ 3 ] == '*' ) ) {
            unset( $wl_items[ $key ] );
        }

        if ( $exploded_item[ 2 ] == '*' && $exploded_item[ 3 ] != '*' ) {
            unset( $wl_items[ $key ] );
        }

    endforeach; endif;

    $whitelist = implode( PHP_EOL, $wl_items );
    $whitelist_success = 'Your white list was updated.';

    update_site_option( 'brute_ip_whitelist', $whitelist );

    return;
}
