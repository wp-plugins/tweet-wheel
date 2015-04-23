<?php

/**
 * Gets an instance of TW_Twitter class
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return object
 **/

// Declare global database / table version variable
global $tw_db_version;

// Define current database / table version
$tw_db_version = '1.0';

/**
 * Function that runs on plugin activation / installation
 *
 * @type function
 * @date 30/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function tw_install() {
    
    global $wpdb;
    
    
    $table_name = $wpdb->prefix . 'tw_queue';
    
    $charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		ID mediumint(9) NOT NULL AUTO_INCREMENT,
        post_ID bigint(20) UNSIGNED NOT NULL,
		queue mediumint(9) NOT NULL,
		UNIQUE KEY id (ID)
	) $charset_collate;";

    // Create / Upgrade table structure
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

    // Load default settings
    tw_load_settings();
    tw_schedule_task();
    
}

// ...

/**
 * Provide plugin with default settings on plugin activation (if not defined)
 *
 * @type function
 * @date 30/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function tw_load_settings() {
    
    global $tw_db_version;
    
    $default = array(
		'post_type' => array( 0 => 'post' ),
        'queue_new_post' => 0,
        'tweet_text' => '{{TITLE}} - {{URL}}',
        'loop' => 1
    );
    
    add_option( 'tw_queue_status', 'paused' );
    add_option( 'tw_settings_options', $default );
    add_option( 'tw_db_version', $tw_db_version );
    
}

// ...

/**
 * Prevent redirection on plugin's re-activation. Once is enough :)
 *
 * @type function
 * @date 30/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function tw_after_activate() {
    add_option('tw_activation_redirect', true);
}

// ...

/**
 * Schedule Cron Job
 *
 * @type function
 * @date 03/04/2015
 * @since 0.4
 *
 * @param N/A
 * @return N/A
 **/

function tw_schedule_task() {
    
    if( ! wp_next_scheduled( 'tweet_wheel' ) )
        wp_schedule_event( time(), 'minutely', 'tweet_wheel' );

}  