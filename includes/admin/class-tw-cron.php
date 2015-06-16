<?php

class TW_Cron {
    
    public static $_instance = null;

    // ...
    
	/**
	 * Main TweetWheel Cron Instance
	 *
	 * Ensures only one instance of TweetWheel Cron is loaded or can be loaded.
     * @type function
	 * @date 14/03/2015
	 * @since 0.4
     *
	 * @static
     * @param N/A
	 * @return TW_Cron - Main instance
	 */
    
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
    /**
     * Class constructor
     *
     * @type function
     * @date 14/03/2015
     * @since 0.4
     *
     * @param N/A
     * @return N/A
     **/
    
    public function __construct() {
        
        // Add 15 minutes cron job
        add_filter( 'cron_schedules', array( $this, 'interval' ), 10, 1 );

        // An actual cron task to be run by WP Cron
        add_action( 'tweet_wheel', array( $this, 'task' ) );
        
        // ...
        
        if( $this->is_wp_cron_disabled() == true && ! get_transient( '_tw_wp_cron_alert_' . get_current_user_id() )  )
            add_action( 'admin_notices', array( $this, 'cron_error_notice' ) );
        
        add_action( 'wp_ajax_wp_cron_alert', 'ajax_wp_cron_alert' );
    }
    
    // ...
    
    /**
     * Adds a custom interval to cron schedule (every minute)
     *
     * @type function
     * @date 05/04/2015
     * @since 0.4
     *
     * @param array
     * @return array
     **/
    
    public function interval( $schedules ) {
        
     	// Adds every 15 minutes to the existing schedules.
     	$schedules['minutely'] = array(
     		'interval' => 60,
     		'display' => __( 'Every Minute', 'tweetwheel' )
     	);
        
     	return apply_filters( 'tw_cron_interval', $schedules );
        
     }

    // ...
    
    /**
     * Cron job
     * Checks if it is apprioriate time to tweet and tweets eventually
     *
     * @type function
     * @date 28/01/2015
     * @update 05/04/2015 (0.4)
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function task() {
        
        do_action( 'tw_before_cron' );
        
        // If queue is paused...
        if( 'running' != get_option( 'tw_queue_status' ) )
            return;
        
        $last_tweet = get_option( 'tw_last_tweet' ) ? get_option( 'tw_last_tweet' ) : 0;
        
        // Check schedule
        // @TODO - turn it into a function "should_tweet" or "maybe_tweet"... to keep code organised
        
        $days = is_array( TW()->schedule()->get_days() ) ? TW()->schedule()->get_days() : array();
        
        $is_day = isset( $days[date('N')] ) && $days[date('N')] == 1 ? true : false;
        
        if( ! $is_day )
            return false;
        
        if( ! TW()->schedule()->has_times() )
            return false;
        
        $closest_time = TW()->schedule()->get_closest_time();

        if( $closest_time == false )
            return false;
        
        $last_tweet = get_option( 'tw_last_tweet' );
        $last_tweeted_time = $last_tweet['time'] ? $last_tweet['time'] : 0;
        
        // Last tweeted time is greater than latest time in the schedule, so it don't tweet again
        if( $last_tweeted_time > $closest_time )
            return false;
        
        // Go ahead then =]        
        if( TW()->queue()->has_queue_items() == true ) :

            $queue_items = TW()->queue()->get_queued_items();

            // Try until something is tweeted...
            foreach( $queue_items as $q ) :

                // If no error and Tweet was published, break out of the loop 
                if( TW()->tweet()->tweet( $q->post_ID ) != false ) :
                    
                    return true;
                    
                endif;
            
            endforeach;
        
        endif;
        
        do_action( 'tw_after_cron' );
        
        return false;
    
    }
    
    // ...
    
    /**
     * Shows hideable error about WP cron being disabled
     *
     * @type function
     * @date 05/04/2015
     * @since 0.4
     *
     * @param N/A
     * @return N/A
     **/

    public function cron_error_notice() {
        
        ?>
        <div class="tw-wp-cron-alert error">
            <p><?php _e( 'Tweet Wheel needs WP Cron to be enabled!', 'tweet-wheel' ); ?><a id="wp-cron-alert-hide" href="#" class="button" style="margin-left:10px;">I know, don't bug me.</a></p>
        </div>

        <?php
        
    }
    
    // ...
    
    /**
     * Helpers
     */
    
    /**
     * Checks WP cron status
     *
     * @type function
     * @date 05/04/2015
     * @since 0.4
     *
     * @param N/A
     * @return N/A
     **/
    
    public function is_wp_cron_disabled() {
        
        if( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON == true )
            return true;
        
        return false;
        
    }
    
    
}

/**
 * Returns the main instance of TW_Cron
 *
 * @since  0.4
 * @return TW_Cron
 */
function TW_Cron() {
	return TW_Cron::instance();
}
TW_Cron();