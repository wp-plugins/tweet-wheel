<?php

/**
 * This files consists of all AJAX related functions.
 * They are called from all over the place. Mainly classes.
 * Most of them are simple wrappers around class methods.
 */

/**
 * Handles saving of the queue. Saves posts in a new order.
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return string
 **/

function ajax_save_queue() {

    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :

        // Dump the current queue in case something goes wrong...
        $old_queue = TW()->queue()->get_queued_items();

        // Read new queue
        $new_queue = (array) $_POST['queue_order'];

        TW()->queue()->remove_all();
    
        foreach( $new_queue as $post ) :
        
            $insert = TW()->queue()->insert_post( $post );
        
            // If one saving fails, restore the backup
            if( $insert == false ) :
                TW()->queue()->remove_all();
                TW()->queue()->fill_up( $old_queue, 'post_ID' );
                echo json_encode( array( 'response' => 'error' ) );
                exit;
            endif;
        
        endforeach;
    
        echo json_encode( array( 'response' => 'ok' ) );
        
        exit;
    
    endif;
    
    echo json_encode( array( 'response' => 'error', 'message' => 'Not enough permissions' ) );
    
    exit;
    
}

// ...

/**
 * When a queue is empty, it shows an admin notification. This allows to hide it for a week.
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function ajax_hide_empty_queue_alert() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        set_transient( '_tw_empty_queue_alert_' . get_current_user_id(), 'hide', 60*60*24*7 ); // hide for a week
        
        echo json_encode( array( 'response' => 'ok' ) );
    
    endif;
    
    exit;
    
}

// ...

/**
 * When WP cron is disabled and user acknowledges the problem, hide a message.
 *
 * @type function
 * @date 14/03/2015
 * @since 0.4
 *
 * @param N/A
 * @return N/A
 **/

function ajax_wp_cron_alert() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );
	

    if ( current_user_can( 'manage_options' ) ) :
    
        set_transient( '_tw_wp_cron_alert_' . get_current_user_id(), 'hide', 60*60*24*7 ); // hide for a week
        
        echo json_encode( array( 'response' => 'ok' ) );
    
    endif;
    
    exit;
    
}

// ...

/**
 * Resumes or pauses the queue. Used by WP Cron.
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return string
 **/

function ajax_change_queue_status() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :

        $status = TW()->queue()->get_queue_status();
    
        if( $status == "paused" ) :
            TW()->queue()->resume();
        endif;
    
        if( $status == "running" ) :
            TW()->queue()->pause();
        endif;
        
        echo json_encode( array( 'response' => TW()->queue()->get_queue_status() ) );
        
        exit;

    endif;
    
    echo json_encode( array( 'response' => 'error', 'message' => 'Not enough permissions to perform this action' ) );
    
    exit;
    
}

// ...

/**
 * Removes a single post from the queue. Nice and easy ;)
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return json
 **/

function ajax_remove_from_queue() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        if( TW()->queue()->remove_post( $_POST['post_id'] ) ) :
            
            echo json_encode( array( 'response' => 'ok' ) );
            
            exit;
            
        endif;
    
    endif;
    
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
    
}

// ...

/**
 * Adds a single post to the queue. Nice and easy ;)
 *
 * @type function
 * @date 01/03/2015
 * @since 0.3
 *
 * @param N/A
 * @return json
 **/

function ajax_add_to_queue() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
    
        if( TW()->queue()->insert_post( $_POST['post_id'] ) ) :
            
            echo json_encode( array( 'response' => 'ok' ) );
            
            exit;
            
        endif;
    
    endif;
    
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
    
}

// ...

/**
 * Sends a tweet.
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return json
 **/

function ajax_tweet() {
    
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :

        if( false != TW()->tweet()->tweet( $_POST['post_id'] ) ) :
            
            echo json_encode( array( 'response' => 'ok' ) );
            
            exit;
            
        endif;
        
        echo json_encode( array( 'response' => 'error', 'message' => 'Cannot send a tweet. More likely problem with API.' ) );
        
        exit;
    
    endif;
    
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
    
}

// ...

/**
 * Retrieves registered post types
 *
 * @type function
 * @date 22/04/2015
 * @since 0.5
 *
 * @param N/A
 * @return json
 **/

function ajax_get_post_types() {
	
    check_admin_referer( 'tweet-wheel-nonce', 'twnonce' );

    if ( current_user_can( 'manage_options' ) ) :
		
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		
		if( empty( $post_types ) ) :
			
			echo json_encode( array( 'response' => 'error', 'message' => 'No public post types enabled.' ) );
			
			exit;
			
		endif;

		echo json_encode( array( 'response' => 'success', 'data' => $post_types ) );
		
		exit;
		
	endif;
	
    echo json_encode( array( 'response' => 'error' ) );
    
    exit;
	
}

// ...