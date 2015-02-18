<?php

function ajax_save_queue() {

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
            TW()->queue()->fill_up($old_queue);
            echo 'error';
            exit;
        endif;
        
    endforeach;
    
    echo 'ok';
    exit;
    
}

function ajax_hide_empty_queue_alert() {
    
    set_transient( '_tw_empty_queue_alert_' . get_current_user_id(), 'hide', 60*60*24*7 ); // hide for a week
    
}

function ajax_change_queue_status() {

    $status = TW()->queue()->get_queue_status();
    
    if( $status == 0 ) :
        TW()->queue()->resume();
        echo TW()->queue()->get_queue_status(); exit;
    endif;
    
    if( $status == 1 ) :
        TW()->queue()->pause();
        echo TW()->queue()->get_queue_status(); exit;
    endif;
    
    echo 'error';
    exit;
    
}

function ajax_remove_from_queue() {
    
    if( TW()->queue()->remove_post( $_POST['post_id'] ) ) :
        echo json_encode( array( 'response' => 'OK' ) );
        exit;
    endif;
    
    echo json_encode( array( 'response' => 'error' ) );
    exit;
    
}

function ajax_tweet() {
    
    if( TW()->tweet()->tweet( $_POST['post_id'] ) == true ) :
        echo json_encode( array( 'response' => 'OK' ) );
        exit;
    endif;
    
    echo json_encode( array( 'response' => 'error' ) );
    exit;
    
}