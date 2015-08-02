<?php
/*
Adapted from Purge Transients by Seebz
https://github.com/Seebz/Snippets/tree/master/Wordpress/plugins/purge-transients
*/

if ( !function_exists( 'bp_purge_transients' ) ) {
    function bp_purge_transients( $older_than = '1 day' )
    {
        global $wpdb;

        $older_than_time = strtotime( '-' . $older_than );
        if ( $older_than_time > time() || $older_than_time < 1 ) {
            return false;
        }

	    $sql =

        $sql = $wpdb->prepare( "
SELECT REPLACE(option_name, '_site_transient_timeout_brute_', '') AS transient_name
FROM {$wpdb->options}
WHERE option_name LIKE '\_site\_transient\_timeout\_brute\__%%'
AND option_value < %s
", $older_than_time );

        $transients = $wpdb->get_col( $sql );

        $options_names = array();

        foreach ( $transients as $transient ) {
            $options_names[ ] = '_site_transient_brute_' . $transient;
            $options_names[ ] = '_site_transient_timeout_brute_' . $transient;
        }

        if ( $options_names ) {
            $option_names_string = implode( ', ', array_fill( 0, count( $options_names ), '%s') );
	        $delete_sql = "DELETE FROM {$wpdb->options} WHERE option_name IN ($option_names_string)";

	        $delete_sql = call_user_func_array( array($wpdb, 'prepare'), array_merge( array( $delete_sql ), $options_names ) );

            $result = $wpdb->query( $delete_sql );
            if ( !$result ) {
                return false;
            }

        }

        return $transients;
    }
}


function bp_purge_transients_activation()
{
    if ( !wp_next_scheduled( 'bp_purge_transients_cron' ) ) {
        wp_schedule_event( time(), 'daily', 'bp_purge_transients_cron' );
    }
}

// register_activation_hook(__FILE__, 'bp_purge_transients_activation');
add_action( 'admin_init', 'bp_purge_transients_activation' );

function do_bp_purge_transients_cron()
{
    $o = bp_purge_transients();
}

add_action( 'bp_purge_transients_cron', 'do_bp_purge_transients_cron' );
