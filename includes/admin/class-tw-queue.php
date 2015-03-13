<?php

class TW_Queue {
    
    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TW_Queue Instance
	 *
	 * Ensures only one instance of TW_Queue is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return TW_Queue object
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TW_Queue _construct
     *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function __construct() {
        
        // Settings only for authed users
        if( ! TW()->twitter()->is_authed() )
            return;

        // Add admin menu
        add_filter( 'tw_load_admin_menu', array( $this, 'menu' ) );
        
        // Add some post actions to the post list screen
        add_filter( 'post_row_actions', array( $this, 'post_row_queue' ), 10, 2);
        add_filter( 'admin_footer-edit.php', array( $this, 'bulk_queue_option' ) );
        add_action( 'load-edit.php', array( $this, 'bulk_queue' ) );
        add_action( 'admin_notices', array( $this, 'bulk_queue_admin_notice' ) );
        
        // Add 15 minutes cron job
        add_filter( 'cron_schedules', array( $this, 'cron_add_every_fifteen_minutes' ) );
        
        // Hooks to action on particular post status changes
        add_action( 'publish_post', array( $this, 'on_publish_post' ), 999, 1 );
        add_action( 'transition_post_status', array( $this, 'on_unpublish_post' ), 999, 3 );
        
        // AJAX actions        
        add_action( 'wp_ajax_save_queue', 'ajax_save_queue' );
        add_action( 'wp_ajax_empty_queue_alert', 'ajax_hide_empty_queue_alert' );
        add_action( 'wp_ajax_change_queue_status', 'ajax_change_queue_status' );
        add_action( 'wp_ajax_remove_from_queue', 'ajax_remove_from_queue' );
        add_action( 'wp_ajax_add_to_queue', 'ajax_add_to_queue' );
        
        // CRON
        add_action( 'init', array( $this, 'cron' ), 999 );
        add_action( 'tw_cron_job', array( $this, 'run_queue' ) );
        
        // Display notice about empty queue
        if( $this->has_queue_items() == false && ! get_transient( '_tw_empty_queue_alert_' . get_current_user_id() ) )
            add_action( 'admin_notices', array( $this, 'alert_empty_queue' ), 999 );
        
        if( isset( $_REQUEST['tw_fill_up'] ) && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'tw_queue' ) :
            $this->fill_up();
            wp_safe_redirect( admin_url( '/admin.php?page=tw_queue' ) ); exit;
        endif;
        
        if( isset( $_REQUEST['tw_remove_all'] ) && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'tw_queue' ):
            $this->remove_all();
            wp_safe_redirect( admin_url( '/admin.php?page=tw_queue' ) ); exit;
        endif;
        
        if( isset( $_REQUEST['tw_queue'] ) )
            $this->insert_post( $_REQUEST['tw_queue'] );
        
        if( isset( $_REQUEST['tw_dequeue'] ) )
            $this->remove_post( $_REQUEST['tw_dequeue'] );
        
    }
    
    // ...
    
    // ...
    
	/**
	 * Adds "Queue" item to the Tweet Wheel menu tab
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => 'Queue',
            'menu_title' => 'Queue',
            'menu_slug'  => 'tw_queue',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    // ...
    
	/**
	 * Loads the Queue screen
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function page() {
        
        ?>
        
		<div class="wrap tweet-wheel tw-queue-page">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TW_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"> Queue</h2>
        
            <div id="tw-queue" <?php echo $this->has_queue_items() == false ? 'class="empty"' : ''; ?>>
                
                <?php 

                if( $this->has_queue_items() == true ) :
                    
                    $this->tools();
                    
                    $this->display_queued_items();

                else : ?>
                
                    <p>Your queue is currently empty. Don't be shy!</p>
                    <a href="<?php echo admin_url('/admin.php?page=tw_queue&tw_fill_up=true'); ?>" class="button button-primary tw-fill-up">Fill-Up The Queue</a>
                
                <?php endif; ?>
                
            </div>
            
        </div>
        
        <?php
        
    }
    
    // ...
    
	/**
	 * Admin notice showed when queue is empty
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function alert_empty_queue() {
        ?>
        <div class="error tw-empty-queue-alert">
            <p><?php _e( 'Your Tweet Wheel Queue is empty! Go ahead and fill it up to start sharing! <a href="'.admin_url('/admin.php?page=tw_queue&tw_fill_up=true').'" class="button" style="margin-left:10px;">Fill-Up The Queue</a><a id="empty-queue-alert-hide" href="#" class="button" style="margin-left:10px;">Hide this</a>', 'tweet-wheel' ); ?></p>
        </div>
        <?php
    }
    
    // ...
    
	/**
	 * Toolbar for each item in the queue
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function item_tools( $post_id ) {
        
        $item_tools = array();
        
        $item_tools = apply_filters( 
            'tw_queue_item_tools', 
            array(
                array(
                    'button_label' => 'Tweet Now',
                    'button_class' => 'tweet-now',
                    'button_attrs' => array(
                        'data-post-id=' . $post_id
                    )
                ),
                array(
                    'button_label' => 'Remove',
                    'button_class' => 'tw-dequeue',
                    'button_attrs' => array(
                        'data-post-id=' . $post_id
                    ) 
                ),
                array(
                    'button_label' => 'Edit Post',
                    'button_href' => get_edit_post_link( $post_id )
                )
            ), 
            $item_tools 
        );
        
        if( ! is_array( $item_tools ) || empty( $item_tools ) )
            return;
        
        echo '<ul class="queue-item-tools">';
        
        foreach( $item_tools as $item ) : 
            
            $item = wp_parse_args( $item, array(
                'button_id' => '',
                'button_class' => '',
                'button_href' => '#',
                'button_label' => 'Button!',
                'button_attrs' => array()
            ) );
            
            extract( $item );
        
            ?>
        
            <li><a id="<?php echo $button_id; ?>" class="<?php echo $button_class; ?>" href="<?php echo $button_href; ?>" <?php echo implode( ' ', $button_attrs ); ?>><?php echo $button_label; ?></a></li>

            <?php
        
        endforeach;
        
        echo '</ul>';
        
    }
    
    // ...
    
	/**
	 * Queue tools / buttons
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function tools() {

        ?>
        <ul class="queue-tools">
            <li><a href="#" id="save-the-queue" class="button button-primary disabled">All Saved</a></li>
            <li><a href="#" id="change-queue-status" class="button"><?php echo $this->get_queue_status() == 'paused' ? 'Resume' : 'Pause' ?></a></li>
            <li><a href="<?php echo admin_url( '/admin.php?page=tw_queue&tw_fill_up=true' ); ?>" class="button">Refill</a></li>
            <li><a id="tw-empty-queue" href="#" class="button">Empty</a></li>
            <li><a id="tw-simple-view" href="#" class="button">Simple View</a></li>
        </ul>
        <span id="queue-status">Status: <?php echo $this->get_queue_status() == 'paused' ? 'Paused' : 'Running' ?></span>
        <script>
        $(document).ready(function(){
           $('#tw-empty-queue').click(function(){
               var r = confirm("Are you sure? This will remove ALL your posts from Tweet Wheel's Queue!");
               if (r == true) {
                   window.location.href = '<?php echo admin_url( '/admin.php?page=tw_queue&tw_remove_all=true' ); ?>';
               }
           });
        });
        </script>
        
        <?php
        
    }
    
    // ...
    
	/**
	 * Fills up the queue with ALL blog posts
     * 
     * @TODO: give user a bit more control over it
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function fill_up( $data = null ) {
        
        if( $data == null ) :
            
            $args = apply_filters( 'tw_queue_fill_up_args', array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => -1
            ) );
        
            $posts = get_posts( $args );
        
        else :
            
            $posts = $data;
            
        endif;
        
        if( empty( $posts ) )
            return false;
        
        foreach( $posts as $p ) :
            
            $this->insert_post( $p->ID );
            
        endforeach;
        
    }
    
    // ...
    
	/**
	 * Inserts a post to the queue. Performs checks for duplication and exclusion. 
     * The check be skipped giving "true" as a value for last two parameters.
	 *
	 * @since 0.1
	 * @return WP Insert | false
	 */
    
    public function insert_post( $post_id, $skip_queue = false, $skip_exclusion = false ) {
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        
        global $wpdb;
        
        if( $this->is_item_queued( $post_id ) == true && $skip_queue == false )
            return false;
        
        if( $this->is_item_excluded( $post_id ) == true && $skip_exclusion == false )
            return false;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'tw_queue',
            array(
                'queue' => $this->get_last_queued()+1,
                'post_ID' => $post_id
            )
        );
        
        return $result;
        
    }
    
    // ...
    
	/**
	 * Removes post from the queue
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function remove_post( $post_id, $skip = false ) {
        
        global $wpdb;
        
        $result = $wpdb->query(
            "DELETE FROM " . $wpdb->prefix . "tw_queue WHERE post_ID = " . $post_id
        );
        
        return $result;
        
    }
    
    // ...
    
	/**
	 * Excludes a post from the queue (and removes if exists)
	 *
	 * @since 0.3
	 * @return boolean
	 */
    
    public function exclude_post( $post_id ) {
        
        $this->remove_post( $post_id );
        
        $excluded = array( 0 => 1 );
        $excluded = array_shift( $excluded );

        update_post_meta( $post_id, 'post_exclude', $excluded );
        
        return true;
        
    }
    
    // ...
    
	/**
	 * Unchecks the Post Exclude option (doesn't insert a post)
	 *
	 * @since 0.3
	 * @return boolean
	 */
    
    public function include_post( $post_id ) {
        
        $excluded = array();

        update_post_meta( $post_id, 'post_exclude', $excluded );
        
        return true;
        
    }
    
    // ...
    
	/**
	 * Adds an action to posts on the edit.php screen
	 *
	 * @since 0.1
     * @update 0.3
	 * @return n/a
	 */
    
    public function post_row_queue( $actions, $post ) {
        
        //check for your post type
        if ( $post->post_type == "post" && $post->post_status == "publish" ) :

            if( $this->is_item_excluded( $post->ID ) ) 
                
                $actions['excluded'] = '<span style="color:#aaa">Excluded</span>';
            
            else if( $this->is_item_queued( $post->ID ) ) :
                
                $actions['dequeue'] = '<a class="tw-dequeue-post" style="color:#a00" data-post-id="'.$post->ID.'">Dequeue</a>';
                
            else :
                
                $actions['queue'] = '<a class="tw-queue-post" href="#" data-post-id="'.$post->ID.'">Queue</a>';
                
            endif;
            
        endif;
        
        return $actions;
        
    }
    
    // ...
    
	/**
	 * Injects options to Bulk Actions dropdown on the edit.php screen
	 *
	 * @since 0.3
	 * @return n/a
	 */
    
    public function bulk_queue_option() {
        
        global $post_type;
 
          if($post_type == 'post') {
            ?>
            <script type="text/javascript">
              jQuery(document).ready(function() {
                  jQuery("select[name='action']").append('<option disabled></option><option disabled>Tweet Wheel</option>');
                  jQuery('<option>').val('queue').text('- <?php _e('Queue')?>').appendTo("select[name='action']");
                  jQuery('<option>').val('queue').text('- <?php _e('Queue')?>').appendTo("select[name='action2']");
                  jQuery('<option>').val('dequeue').text('- <?php _e('Dequeue')?>').appendTo("select[name='action']");
                  jQuery('<option>').val('dequeue').text('- <?php _e('Dequeue')?>').appendTo("select[name='action2']");
                  jQuery('<option>').val('exclude').text('- <?php _e('Exclude')?>').appendTo("select[name='action']");
                  jQuery('<option>').val('exclude').text('- <?php _e('Exclude')?>').appendTo("select[name='action2']");
              });
            </script>
            <?php
          }
        
    }
    
    // ...
    
	/**
	 * Handles bulk actions
	 *
	 * @since 0.3
	 * @return n/a
	 */
    
    public function bulk_queue() {

        // 1. get the action
        $wp_list_table = _get_list_table('WP_Posts_List_Table');
        $action = $wp_list_table->current_action();
        
		if(isset($_REQUEST['post'])) {
			$post_ids = array_map('intval', $_REQUEST['post']);
		}
		
        if(empty($post_ids)) return;
        
        // 2. security check
        check_admin_referer('bulk-posts');

        switch($action) {

        // 3. Perform the action
        case 'queue':

            $queued = 0;

            foreach( $post_ids as $post_id ) {
                if ( $this->include_post( $post_id ) && $this->insert_post($post_id) )
                    $queued++;
            }

            // build the redirect url
            $sendback = add_query_arg( array('queued' => $queued ), $sendback );

            break;

        case 'dequeue':
            
            $dequeued = 0;

            foreach( $post_ids as $post_id ) {
                if ( $this->remove_post($post_id) )
                    $dequeued++;
            }

            // build the redirect url
            $sendback = add_query_arg( array('dequeued' => $dequeued ), $sendback );
            
            break;
            
        case 'exclude':
            
            $excluded = 0;

            foreach( $post_ids as $post_id ) {
                if ( $this->exclude_post($post_id) )
                    $excluded++;
            }

            // build the redirect url
            $sendback = add_query_arg( array('excluded' => $excluded ), $sendback );
            
            break;

        default: return;

        }

        // ...

        // 4. Redirect client
        wp_redirect($sendback);

        exit();
        
    }
    
    // ...
    
	/**
	 * Display relevant notice after a bulk action has been performed
	 *
	 * @since 0.3
	 * @return n/a
	 */
    
    public function bulk_queue_admin_notice() {
 
      global $post_type, $pagenow;
 
      // Posts queued
 
      if($pagenow == 'edit.php' && $post_type == 'post' &&
         isset($_REQUEST['queued']) && (int) $_REQUEST['queued']) {
        $message = sprintf( _n( 'Post queued.', '%s posts queued.', $_REQUEST['queued'] ), number_format_i18n( $_REQUEST['queued'] ) );
        echo '<div class="updated"><p>' . $message . '</p></div>';
      }
      
      // ...
      
      // Posts dequeued
      
      if($pagenow == 'edit.php' && $post_type == 'post' &&
         isset($_REQUEST['dequeued']) && (int) $_REQUEST['dequeued']) {
        $message = sprintf( _n( 'Post dequeued.', '%s posts dequeued.', $_REQUEST['dequeued'] ), number_format_i18n( $_REQUEST['dequeued'] ) );
        echo '<div class="updated"><p>' . $message . '</p></div>';
      }
      
      // ...
      
      // Posts excluded
      
      if($pagenow == 'edit.php' && $post_type == 'post' &&
         isset($_REQUEST['excluded']) && (int) $_REQUEST['excluded']) {
        $message = sprintf( _n( 'Post excluded.', '%s posts excluded.', $_REQUEST['excluded'] ), number_format_i18n( $_REQUEST['excluded'] ) );
        echo '<div class="updated"><p>' .$message .'</p></div>';
      }
      
    }
    
    // ...
    
	/**
	 * Displays the queue of items
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function display_queued_items() {
        
        $in_queue = $this->get_queued_items();
        
        ?>
        
        <div id="the-queue">
            <?php
                
            if( '' != get_option( 'tw_last_tweet' ) ) :
                
                $tweet = get_option( 'tw_last_tweet' );
                
            ?>
            
                <div class="the-queue-item tweeted">
                    <div class="post-header">
                        <span class="title"><?php echo isset( $tweet['title'] ) ? $tweet['title'] : get_the_title( $tweet['ID'] ); ?></span>
                        <time>Tweeted <?php echo date( 'H:i:s d-m-y', get_option( 'tw_last_tweet_time' ) ); ?></time>
                    </div>
                    <div class="post-content">
                        <?php echo $tweet['text']; ?>
                    </div>
                </div>
            
            <?php endif; ?>
            <ul>
            <?php foreach( $in_queue as $q ) : ?>
                <li class="the-queue-item" id="<?php echo $q->post_ID; ?>">
                    <div class="post-header">
                        <span class="title"><?php echo get_the_title( $q->post_ID ); ?></span>
                        <span class="drag-handler"><img src="<?php echo TW_PLUGIN_URL; ?>/assets/images/reorder.png"/></span>
                        <?php $this->item_tools( $q->post_ID ); ?>
                    </div>
                    <div class="post-content">
                        <?php echo TW()->tweet()->preview( $q->post_ID ); ?>
                    </div>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        
        <?php
        
    }
    
    // ...
    
	/**
	 * Empties the queue
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function remove_all() {
        
        global $wpdb;
        
        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'tw_queue' );
        
    }
    
    // ...
    
	/**
	 * Pauses the queue
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function pause() {
        
        update_option( 'tw_queue_status', 'paused' );
        
    }
    
    // ...
    
	/**
	 * Resumes the queue
	 *
	 * @since 0.1
	 * @return n/a
	 */
    
    public function resume() {
        
        update_option( 'tw_queue_status', 'running' );
        
    }
    
    // ...
    
    /**
     * Hooks
     */
    
    public function on_publish_post( $post_id ) {
        
        // If new and should be excluded
        if(
            wpsf_get_setting( 'tw_settings', 'global', 'queue_new_post' ) == 1 &&
            get_post_meta( $post_id, 'post_exclude' ) == ''  
        )
            return;
        
        // check if post is only just published...
        // I know the new_post hook, but this works just fine
        // if there is no post_meta simply means its fresh post
        // or is switching from excluded to included in the queue
        if( 
            get_post_meta( $post_id, 'post_exclude' ) == '' && 
            ! isset( $_POST['post_exclude'] ) ||
            is_array( get_post_meta( $post_id, 'post_exclude' ) ) && 
            ! isset( $_POST['post_exclude'] )
        ) :
            $this->insert_post( $post_id, false, true );
            return;
        endif;
            
        
        // Switching from included to excluded - queue it
        if( 
            is_array( get_post_meta( $post_id, 'post_exclude' ) ) && 
            isset( $_POST['post_exclude'] ) &&
            $_POST['post_exclude']['cmb-field-0'] == 1
        ) 
            $this->remove_post( $post_id, true );
            
        return;
        
    }
    
    // ...
    
    public function on_unpublish_post( $new_status, $old_status, $post ) {
        
        if ( $old_status == 'publish'  &&  $new_status != 'publish' ) {
            $this->remove_post( $post->ID );
        }
    
        return;
        
    }
    
    // ...
    
    public function cron_add_every_fifteen_minutes( $schedules ) {
        
     	// Adds every 15 minutes to the existing schedules.
     	$schedules['fifteen_minutes'] = array(
     		'interval' => 900,
     		'display' => __( 'Every 15 Minutes' )
     	);
        
     	return $schedules;
        
     }
    
    // ...
    
    public function cron() {
    
        if ( ! wp_next_scheduled( 'tw_cron_job' ) ) {
            wp_schedule_event( time(), 'fifteen_minutes', 'tw_cron_job' );
        }
        
    }   
    
    // ...
    
    public function run_queue() {
        
        do_action( 'tw_before_cron' );
        
        // If queue is paused...
        if( 'running' != get_option( 'tw_queue_status' ) )
            return;
        
        $interval = wpsf_get_setting( 'tw_settings', 'timing', 'post_interval' );
        $last_tweet = get_option( 'tw_last_tweet_time' ) ? get_option( 'tw_last_tweet_time' ) : 0;
        
        $delay = $interval*60;
        
        // Prevents queue being ran to often. Let's not abuse the Tweet Wheel!
        if( time() < $last_tweet + $delay ) :
            do_action( 'tw_before_cron_too_often' );
            return;
        endif;
        
        if( TW()->queue()->has_queue_items() == true ) :

            $queue_items = TW()->queue()->get_queued_items();

            foreach( $queue_items as $q ) :

                $response = TW()->tweet()->tweet( $q->post_ID );

                // If no error and Tweet was published, break out of the loop 
                if( $response != false ) :
                    
                    $tweet = TW()->tweet()->preview( $q->post_ID );
        
                    update_option( 'tw_last_tweet_time', time() );
                    update_option( 'tw_last_tweet', array( 'ID' => $q->post_ID, 'title' => get_the_title( $q->post_ID ), 'text' => $tweet ) );
                    
                    break;
                    
                endif;
            
            endforeach;
        
        endif;
        
        do_action( 'tw_after_cron' );
        
        return false;
    
    }
    
    /**
     * Conditions
     **/
        
    public function has_queue_items() {

        $items = $this->get_queued_items();

        if( ! is_array( $items ) )
            return false;

        if( empty( $items ) )
            return false;
        
        return true;
        
    }
    
    public function is_item_queued( $post_id ) {
        
        global $wpdb;
        
        $results = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "tw_queue WHERE post_ID = " . $post_id
        );
        
        if( empty( $results ) )
            return false;
        
        return true;
        
    }
    
    public function is_item_excluded( $post_id ) {
        
        // If is excluded and is not forced
        $excluded = get_post_meta( $post_id, 'post_exclude' );
        
        if( is_array( $excluded ) && isset( $excluded[0] ) && $excluded[0] == 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * All the Get's
     */
    
    public function get_queued_items() {
        
        global $wpdb;
        
        $results = $wpdb->get_results(
            "SELECT * FROM " . $wpdb->prefix . "tw_queue"
        );
        
        return $results;
        
    }
    
    public function get_queue_status() {
        
        return get_option( 'tw_queue_status' );
        
    }
    
    public function get_last_queued() {
        
        global $wpdb;
        
        if( $this->has_queue_items() == false )
            return 0;
        
        $query = $wpdb->get_row(
            "SELECT * FROM " . $wpdb->prefix . "tw_queue ORDER BY ID DESC LIMIT 1"
        );
        
        return $query->queue;
        
    }
    
    public function get_first_queued_item() {
        
        global $wpdb;
        
        if( ! $this->has_queue_items() )
            return;
        
        return $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'tw_queue WHERE queue = 1' );
        
    }
    
}

/**
 * Returns the main instance of TW
 *
 * @since  0.0.1
 * @return TweetWheel
 */
function TW_Queue() {
	return TW_Queue::instance();
}