<?php

/**
 * Function that runs on plugin uninstallation (not deactivation)
 *
 * @type function
 * @date 01/03/2015
 * @since 0.3
 *
 * @param N/A
 * @return N/A
 **/

function tw_uninstall() {
    
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'tw_queue'; 

    //drop a custom db table
    $wpdb->query( "DROP TABLE IF EXISTS " . $table_name );
    
    tw_unload_settings();
    
}

// ...

/**
 * Remove plugin's default settings on plugin uninstallation
 *
 * @type function
 * @date 01/03/2015
 * @since 0.3
 *
 * @param N/A
 * @return N/A
 **/

function tw_unload_settings() {
    
    delete_option( 'tw_queue_status' );
    delete_option( 'tw_settings_settings' );
    delete_option( 'tw_twitter_auth_settings' );
    delete_option( 'tw_db_version' );
    delete_option( 'tw_last_tweet_time' );
    delete_option( 'tw_last_tweet' );
    
}