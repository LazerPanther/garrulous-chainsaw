<?php

if ( !class_exists( 'BruteProtect_Admin' ) ) {
    class BruteProtect_Admin extends BruteProtect
    {

        private $error_reporting_data;
        public $clef;
        public $menu_icon;

        function __construct()
        {

            $ip = $this->brute_get_ip();
            $key = get_site_option( 'bruteprotect_api_key' );

            if ( ( $ip == '127.0.0.1' || $ip == '::1' ) && !$key ) {
                add_action( 'admin_notices', array( &$this, 'bruteprotect_localhost_warning' ) );
            }

            add_action( 'admin_head', array( &$this, 'set_custom_font_icon' ) );
            add_action( 'wp_dashboard_setup', array( &$this, 'bruteprotect_dashboard_widgets' ) );
            add_action( 'wp_network_dashboard_setup', array( &$this, 'bruteprotect_dashboard_widgets' ) );

            add_filter( 'plugin_action_links', array( &$this, 'bruteprotect_plugin_action_links' ), 10, 2 );

            add_action( 'admin_menu', array( &$this, 'bruteprotect_admin_menu_non_multisite' ) );
            add_action( 'network_admin_menu', array( &$this, 'bruteprotect_admin_menu' ) );

            add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_bruteprotect_admin' ) );

            add_action( 'admin_init', array( &$this, 'activation_redirection' ) );
	        add_action( 'admin_init', array( &$this, 'dismiss_notice' ) );

        }

	    function dismiss_notice() {
		    if ( isset( $_GET['brute_dismiss_notice'] ) ) {
			    $dismiss = $_GET['brute_dismiss_notice'];
			    $expected = array( '1', '0' );
			    if ( in_array( $dismiss, $expected ) ) {
				    global $current_user;
				    update_user_meta( $current_user->ID, 'brute_dismiss_jetpack_notice', $dismiss );
			    }
		    }
	    }

        function activation_redirection()
        {
            if ( get_site_option( 'bruteprotect_do_activation_redirect', false ) ) {
                delete_site_option( 'bruteprotect_do_activation_redirect' );
                wp_redirect( admin_url( 'admin.php?page=bruteprotect-config' ) );
            }
        }

        function set_custom_font_icon()
        {
            if ( version_compare( get_bloginfo( 'version' ), '3.7.99', '>' ) ) :
                ?>
                <style type="text/css">

                    /* for top level menu pages replace `{menu-slug}` with the slug name passed to `add_menu_page()` */
                    #toplevel_page_bruteprotect-config .wp-menu-image:before {
                        font-family: bruteprotect !important;
                        content: "a" !important;
                    }

                </style>
            <?php
            endif;
        }

        function enqueue_bruteprotect_admin()
        {

            wp_enqueue_style( 'bruteprotect-css', plugins_url( '/admin/bruteprotect-admin.css', __FILE__ ), array(), BRUTEPROTECT_VERSION );
            if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'bruteprotect-config' ) {
                wp_enqueue_style( 'bpdash-css', MYBP_URL . 'assets/stylesheets/app.css', array(), BRUTEPROTECT_VERSION );
                wp_enqueue_style( 'fontawesome', plugins_url( '/bruteprotect/admin/fonts/font-awesome/css/font-awesome.min.css' ), array(), BRUTEPROTECT_VERSION );
                wp_enqueue_style( 'bpadmin-css', plugins_url( '/bruteprotect/admin/css/admin.css' ), array(), BRUTEPROTECT_VERSION );
                wp_register_script( 'modrnizer', plugins_url( '/bruteprotect/admin/js/modrnizer.js' ) );
                wp_register_script( 'foundation', plugins_url( '/bruteprotect/admin/js/foundation.min.js' ), array(
                    'jquery',
                    'modrnizer'
                ), BRUTEPROTECT_VERSION );
                wp_register_script( 'equalizer', plugins_url( '/bruteprotect/admin/js/foundation.equalizer.js' ), array( 'foundation' ), BRUTEPROTECT_VERSION );

                wp_enqueue_script( 'modrnizer' );
                wp_enqueue_script( 'foundation' );
                wp_enqueue_script( 'equalizer' );
            }
            if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'bruteprotect-debug' ) {
                wp_enqueue_style('bpadmin-css', plugins_url('/bruteprotect/admin/css/admin.css'), array(), BRUTEPROTECT_VERSION);
            }

        }

        function bruteprotect_localhost_warning()
        {
            global $bruteprotect_showing_warning;
            $bruteprotect_showing_warning = true;

            echo "<div id='bruteprotect-warning' class='updated fade'><p><strong>" . __( 'BruteProtect not enabled.' ) . "</strong> You have installed BruteProtect, but we have detected that you are running it on a local installation.   You can leave BruteProtect turned on, we will prompt you to generate a key when you migrate to a live server.</p></div>";
        }


        function check_bruteprotect_access()
        {
            global $wp_version;
            $api_url = get_site_option( 'brute_api_url', 'api.bruteprotect.com/' );
            $test = wp_remote_get( 'http://' . $api_url . 'api_check.php' );

            $report['wp_version']         = $wp_version;
            $report['connection_status']  = $test;
            $report['server']             = $_SERVER;
            $report['bp_version']         = BRUTEPROTECT_VERSION;
            $this->error_reporting_data   = base64_encode( serialize( $report ) );

            if ( !is_wp_error( $test ) && $test[ 'body' ] == 'ok' ) :
                return true;
            endif;

            return false;
        }

        function get_error_reporting_data()
        {
            $this->brute_call( 'brute_test' );
	        $api_url = $this->get_bruteprotect_host();
            $error =  unserialize( base64_decode( $this->error_reporting_data ) );
            $data = "Server Info:\n";
            $data .= print_r( $error, true);
            $data .= "\n\nLast Request to $api_url:\n";
            $data .= print_r( $this->last_request, true );
            $data .= "\n\nLast Response (raw):\n";
            $data .= print_r( $this->last_response_raw, true );
            $data .= "\n\nLast Response (pretty):\n";
            $data .= print_r( $this->last_response, true );
            return $data;
        }



        /////////////////////////////////////////////////////////////////////
        // Admin Dashboard Widget
        /////////////////////////////////////////////////////////////////////
        function bruteprotect_dashboard_widgets()
        {

            if ( is_multisite() && !is_network_admin() ) {
                $brute_dashboard_widget_hide = get_site_option( 'brute_dashboard_widget_hide' );
                if ( $brute_dashboard_widget_hide == 1 ) {
                    return;
                }
            }

            $brute_dashboard_widget_admin_only = get_site_option( 'brute_dashboard_widget_admin_only' );
            if ( $brute_dashboard_widget_admin_only == 1 && !current_user_can( 'manage_options' ) ) {
                return;
            }

            global $wp_meta_boxes;
            wp_add_dashboard_widget( 'bruteprotect_dashboard_widget', 'BruteProtect Stats', array(
                &$this,
                'bruteprotect_dashboard_widget'
            ) );
        }

        function bruteprotect_dashboard_widget()
        {

            $response = $this->brute_call( 'check_key' );

            if ( !isset( $response[ 'status' ] ) ) {
                // we cannot connect to the api, lets not show the stats
                echo '<div style="text-align: center;"><strong>Statistics are currently unavailable.</strong></div>';

                return;
            }

            bruteprotect_save_pro_info( $response );
            $key = get_site_option( 'bruteprotect_api_key' );
            $ckval = get_site_option( 'bruteprotect_ckval' );

            $stats = wp_remote_get( $this->get_bruteprotect_host() . "index.php/ui/dashboard/index/" . $key );

            if ( is_wp_error( $stats ) ) {
                echo '<div style="text-align: center;"><strong>Statistics are currently unavailable.</strong></div>';

                return;
            }

            print_r( $stats[ 'body' ] );
        }

        function bruteprotect_plugin_action_links( $links, $file )
        {
            if ( $file == plugin_basename( dirname( __FILE__ ) . '/bruteprotect.php' ) ) {
                $links[ ] = '<a href="' . esc_url( admin_url( 'admin.php?page=bruteprotect-config' ) ) . '">' . __( 'Settings' ) . '</a>';
            }

            return $links;
        }


        function bruteprotect_admin_menu_non_multisite()
        {
            if ( version_compare( get_bloginfo( 'version' ), '3.7.99', '>' ) ) {
                $this->menu_icon = ''; // no need for icon .png if in mp6

            } else {
                $this->menu_icon = plugins_url( '/images/menu_icon.png', __FILE__ );
            }

            // registers a debug page with a null parent so it won't show up in a menu
            add_submenu_page(
                null,
                'BruteProtect Debug',
                'BruteProtect Debu',
                'manage_options',
                'bruteprotect-debug',
                array( &$this, 'bruteprotect_debug_page')
            );

            if ( is_multisite() ) {
                add_menu_page( __( 'BruteProtect' ), __( 'BruteProtect' ), 'manage_options', 'bruteprotect-config', array(
                    &$this,
                    'bruteprotect_conf_ms_notice'
                ), $this->menu_icon );

                return;
            }
            $this->bruteprotect_admin_menu();
        }

        function bruteprotect_admin_menu()
        {
            if ( version_compare( get_bloginfo( 'version' ), '3.7.99', '>' ) ) {
                $this->menu_icon = ''; // no need for icon .png if in mp6

            } else {
                $this->menu_icon = plugins_url( '/images/menu_icon.png', __FILE__ );
            }
            add_menu_page( __( 'BruteProtect' ), __( 'BruteProtect' ), 'manage_options', 'bruteprotect-config', array(
                &$this,
                'bruteprotect_dashboard'
            ), $this->menu_icon );


            $key = get_site_option( 'bruteprotect_api_key' );
            $error = get_site_option( 'bruteprotect_error' );

            if ( !$key ) {
                add_action( 'admin_notices', array( &$this, 'bruteprotect_warning' ) );

                return;
            } elseif ( $error && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] != 'bruteprotect-config' ) {
                add_action( 'admin_notices', array( &$this, 'bruteprotect_invalid_key_warning' ) );

                return;
            }
        }

        function send_error_report($error_info) {
            $nonce = isset( $_POST['bp_debug_nonce'] ) ? $_POST['bp_debug_nonce'] : '';
            if( ! wp_verify_nonce( $nonce, 'brute_error_report' ) ) {
                return false;
            }
            $to = 'rocco@hcg.bz';
            $subject = 'BruteProtect Connection Report from ' . home_url();
            wp_mail( $to, $subject, $error_info );
            return true;
        }

        function change_endpoints() {
            $nonce = isset( $_POST['bp_debug_nonce'] ) ? $_POST['bp_debug_nonce'] : '';
            if( ! wp_verify_nonce( $nonce, 'brute_change_endpoints' ) ) {
                return false;
            }
            $brute_endpoint = isset( $_POST['brute_endpoint'] ) ? $_POST['brute_endpoint'] : '';
            if( $brute_endpoint == 'testing' ) {
                update_site_option( 'brute_my_url', 'my.bpdv.co/' );
                update_site_option( 'brute_api_url', 'api.bpdv.co/' );
                update_site_option( 'brute_endpoint_location', 'testing' );
            } else {
                update_site_option( 'brute_my_url', 'my.bruteprotect.com/' );
                update_site_option( 'brute_api_url', 'api.bruteprotect.com/' );
                update_site_option( 'brute_endpoint_location', 'live' );
            }
        }

        function bruteprotect_debug_page() {
            $has_access = $this->check_bruteprotect_access();
            $error_info = $this->get_error_reporting_data();

            if( isset( $_POST['bp_debug_action']) && $_POST['bp_debug_action'] == 'send_error_report' ) {
                $this->send_error_report($error_info);
                $debug_notice = 'Error report sent!';
            }

            if( isset( $_POST['bp_debug_action']) && $_POST['bp_debug_action'] == 'change_endpoints' ) {
                $this->change_endpoints();
                $debug_notice = 'Endpoints changed!';
            }

            $error_report_nonce = wp_create_nonce( 'brute_error_report' );
            $endpoint_nonce     = wp_create_nonce( 'brute_change_endpoints' );
            $endpoint_location  = get_site_option( 'brute_endpoint_location', 'live' );
            $live_checked       = ( $endpoint_location == 'live' ) ? 'checked="checked"' : '';
            $testing_checked    = ( $endpoint_location == 'testing' ) ? 'checked="checked"' : '';

            include 'admin/inc/debug.php';
        }

        function bruteprotect_warning()
        {
            global $bruteprotect_showing_warning;
            $bruteprotect_showing_warning = true;

            //Don't trigger the warning on the config page
            if ( isset( $_GET[ 'page' ] ) && 'bruteprotect-config' == $_GET[ 'page' ] ) {
                return;
            }

            $ip = $this->brute_get_ip();
            //Don't trigger the warning on localhost, since we're not going to let them set up the API yet anyway...
            if ( $ip == '127.0.0.1' || $ip == '::1' ) {
                return;
            }

            $selfinstalling = get_option( 'bp_selfinstall_attempt' );
            if ( $selfinstalling ) {
                return;
            }


            echo "<div id='bruteprotect-warning' class='error fade'><p><strong>" . __( 'BruteProtect is almost ready.' ) . "</strong> " . sprintf( __( 'You must <a href="%1$s">enter your BruteProtect API key</a> for it to work.  <a href="%1$s">Obtain a key for free</a>.' ), esc_url( admin_url( 'admin.php?page=bruteprotect-config' ) ) ) . "</p></div>
			";
        }

        function bruteprotect_invalid_key_warning()
        {
            if ( isset( $_GET[ 'get_key' ] ) && $_GET[ 'get_key' ] == 'success' ) {
                return true;
            }
            global $bruteprotect_showing_warning;
            $bruteprotect_showing_warning = true;
            echo "
			<div id='bruteprotect-warning' class='error fade'><p><strong>" . __( 'There is a problem with your BruteProtect API key' ) . "</strong> " . sprintf( __( ' <a href="%1$s">Please correct the error</a>, your site will not be protected until you do.' ), esc_url( admin_url( 'admin.php?page=bruteprotect-config' ) ) ) . "</p></div>
			";
        }

        function bruteprotect_dashboard()
        {
            include 'admin/main.php';
        }

        /**
         * Unlinks the current user from my.bruteprotect.com
         *
         * if there is a flag to delete_all, all other linked users will also be disconnected, and the api key removed
         *
         * @param bool $delete_all
         */
        function unlink_site( $delete_all = false )
        {
            global $current_user;
            $user_linked = get_user_meta( $current_user->ID, 'bruteprotect_user_linked', true );
            if ( !empty( $user_linked ) ) {
                delete_user_meta( $current_user->ID, 'bruteprotect_user_linked' );
                $action = 'unlink_owner_from_site';
                $additional_data = array(
                    'wp_user_id' => strval( $current_user->ID ),
                );
                $sign = true;

                $response = $this->brute_call( $action, $additional_data, $sign );
            }

            $bp_users = get_bruteprotect_users();
            if ( empty( $bp_users ) ) {
                delete_site_option( 'bruteprotect_user_linked' );
            } else if ( $delete_all == true ) {
                foreach ( $bp_users as $u ) {
                    delete_user_meta( $u->ID, 'bruteprotect_user_linked' );
                    $action = 'unlink_owner_from_site';
                    $additional_data = array(
                        'wp_user_id' => strval( $u->ID ),
                    );
                    $sign = true;

                    $response = $this->brute_call( $action, $additional_data, $sign );
                }
                delete_site_option( 'bruteprotect_user_linked' );
            }

            if ( $delete_all === true ) {
                delete_site_option( 'bruteprotect_api_key' );
            }

        }

        function bruteprotect_conf_ms_notice()
        {
            ?>
            <div class="wrap">
                <div id="bruteapi">

                    <h2 id="header">
                        <img src="<?php echo BRUTEPROTECT_PLUGIN_URL ?>images/BruteProtect-Logo-Text-Only-40.png"
                             alt="BruteProtect" width="250"> &nbsp; Setup</h2>

                    <div class="brutecontainer">
                        <div class="box left clear">
                            <h3 class="green">BruteProtect is ready to protect your network</h3>
                            <?php if ( current_user_can( 'manage_network' ) ): ?>
                                <p>BruteProtect only needs one API key per network.</strong>  <a
                                        href="<?php echo network_home_url( '/wp-admin/network/admin.php?page=bruteprotect-config' ) ?>">Manage
                                        your key here.</a></p>
                            <?php else: ?>
                                <p>But, only Super-Admins can configure BruteProtect settings. Contact your network
                                    administrator to make sure everything is working perfectly</p>
                            <?php endif ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php
        }


    }
}