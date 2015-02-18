<?php

global $tw_db_version;
$tw_db_version = '1.0';

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

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'tw_db_version', $tw_db_version );
    
}

function tw_load_settings() {
    
    $default = array(
        'tw_settings_global_queue_new_post' => 0,
        'tw_settings_template_tweet_text' => '{{TITLE}} - {{URL}}',
        'tw_settings_timing_post_interval' => 180,
        'tw_settings_timing_loop' => 1
    );
    
    add_option( 'tw_settings_settings', $default );
    
}

function tw_after_activate() {
    add_option('tw_activation_redirect', true);
}

