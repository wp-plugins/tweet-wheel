<?php
/**
 * WordPress Settings Framework
 *
 * @author Gilbert Pellegrom
 * @link https://github.com/gilbitron/WordPress-Settings-Framework
 * @license MIT
 */

/**
 * Define your settings
 */
add_filter( 'wpsf_register_settings', 'wpsf_settings' );
function wpsf_settings( $wpsf_settings ) {
    
    // Template section
    $wpsf_settings[] = array(
        'section_id' => 'global',
        'section_title' => 'Global Settings',
        'section_order' => 1,
        'fields' => array(
            array(
                'id' => 'queue_new_post',
                'title' => 'Exclude new posts from the queue?',
                'desc' => 'Check if you want new posts to be excluded from the queue by default.',
                'type' => 'checkbox',
                'choices' => array(
                    'exclude_by_default' => 1
                )
            )
        )
    );
    
    // Template section
    $wpsf_settings[] = array(
        'section_id' => 'template',
        'section_title' => 'Tweet Template',
        'section_order' => 3,
        'fields' => array(
            array(
                'id' => 'tweet_text',
                'title' => 'Default Tweet Text',
                'desc' => 'Default tweet text can be overriden by custom post tweet text setting available on edit page of each post. Allowed tags: {{TITLE}} for post title and {{URL}} for post permalink.',
                'type' => 'textarea',
                'placeholder' => 'What\'s happenng?'
            )
        )
    );

    // Timing Settings section
    $wpsf_settings[] = array(
        'section_id' => 'timing',
        'section_title' => 'Timing Settings',
        'section_description' => '',
        'section_order' => 5,
        'fields' => array(
            array(
                'id' => 'post_interval',
                'title' => 'Publish Interval (Minutes)',
                'desc' => 'Please provide an interval between each time Tweet Wheel should send next post to your Twitter wall. Minimum 60 minutes.',
                'type' => 'text',
                'placeholder' => 'eg. 60, 200, 360...'
            ),
            array(
                'id' => 'loop',
                'title' => 'Loop infinitely?',
                'desc' => 'Check if you want the most recent tweeted post to be re-queued automatically.',
                'type' => 'checkbox',
                'choices' => array(
                    'loop' => 1
                )
            ),
            
        )
    );
    
    $wpsf_settings[] = array(
        'section_id' => 'deauth',
        'section_title' => 'Disconnect Twitter Account',
        'section_description' => '',
        'section_order' => 10,
        'fields' => array(
            array(
                'id' => 'deauth',
                'title' => 'De-authorize',
                'type' => 'custom',
                'std' => TW()->twitter()->get_deauth_url()
                
            )
        )
    );
    
    // Advanced Settings section
    /*$wpsf_settings[] = array(
        'section_id' => 'advanced',
        'section_title' => 'Advanced Settings',
        'section_description' => '',
        'section_order' => 15,
        'fields' => array(
            array(
                'id' => 'cron',
                'title' => 'Cron Handler',
                'desc' => 'Decide which cron handler to use for Tweet Wheel. If you select CRON please consider options below, otherwise ignore them.',
                'type' => 'radio',
                'choices' => array(
                    'wp_cron' => 'WP Cron',
                    'cron' => 'CRON'
                )
            ),
            array(
                'id' => 'cron_pass',
                'title' => 'Cron Password',
                'desc' => 'Optional, but recommended. The password will be passed as CRON\'s URL parameter and compared to this one. Without knowing the password, cron cannot be ran and called from the outside even if someone knew the URL.',
                'type' => 'text',
                'placeholder' => 'eg. dog\'s name, birthday date...'
            ),
            array(
                'id' => 'cron_url',
                'title' => 'CRON URL',
                'type' => 'custom',
                'std' => '<code>' . TW_PLUGIN_URL . '/tweetwheel.php' . ( wpsf_get_setting( 'tw_settings', 'advanced', 'cron_pass' ) ? '?cronpass=' . md5( wpsf_get_setting( 'tw_settings', 'advanced', 'cron_pass' ) ) : '' ) . '</code>'
            )
        )
    );*/

    return $wpsf_settings;
}