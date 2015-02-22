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
    global $tw_db_version;
    
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

    // Store table version as an option for later comparison
	add_option( 'tw_db_version', $tw_db_version );
    
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
    
    $default = array(
        'tw_settings_global_queue_new_post' => 0,
        'tw_settings_template_tweet_text' => '{{TITLE}} - {{URL}}',
        'tw_settings_timing_post_interval' => 180,
        'tw_settings_timing_loop' => 1
    );
    
    add_option( 'tw_queue_status', 'running' );
    add_option( 'tw_settings_settings', $default );
    
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

