<?php

class TW_Queue {
    
    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TweetWheel Twitter Instance
	 *
	 * Ensures only one instance of TweetWheel Twitter is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return TweetWheel - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    public function __construct() {
        
        // Settings only for authed users
        if( ! TW()->twitter()->is_authed() )
            return;

        // Add admin menu
        add_filter( 'tw_load_admin_menu', array( $this, 'menu' ) );
        
        // Add some post actions to the post list
        add_filter( 'post_row_actions', array( $this, 'post_row_action' ), 10, 2);
        
        // Hooks to action on particular post status changes
        //add_action( 'publish_post', array( $this, 'on_publish_post' ), 999, 1 );
        add_action( 'publish_post', array( $this, 'on_publish_post' ), 999, 1 );
        add_action( 'transition_post_status', array( $this, 'on_unpublish_post' ), 999, 3 );
        
        // AJAX actions
        add_action( 'wp_ajax_save_queue', 'ajax_save_queue' );
        add_action( 'wp_ajax_empty_queue_alert', 'ajax_hide_empty_queue_alert' );
        add_action( 'wp_ajax_remove_from_queue', 'ajax_remove_from_queue' );
        
        // CRON
        add_action( 'wp', array( $this, 'cron' ) );
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
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => 'Queue',
            'menu_title' => 'Queue',
            'menu_slug'  => 'tw_queue',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
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
    
    public function alert_empty_queue() {
        ?>
        <div class="error tw-empty-queue-alert">
            <p><?php _e( 'Your Tweet Wheel Queue is empty! Go ahead and fill it up to start sharing! <a href="'.admin_url('/admin.php?page=tw_queue&tw_fill_up=true').'" class="button" style="margin-left:10px;">Fill-Up The Queue</a><a id="empty-queue-alert-hide" href="#" class="button" style="margin-left:10px;">Hide this</a>', 'tweet-wheel' ); ?></p>
        </div>
        <?php
    }
    
    public function item_tools( $post_id ) {
        
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
    
    public function tools() {

        ?>
        <ul class="queue-tools">
            <li><a href="#" id="save-the-queue" class="button button-primary disabled">All Saved</a></li>
            <li><a href="#" id="change-queue-status" class="button"><?php echo $this->get_queue_status() == 0 ? 'Resume' : 'Pause' ?></a></li>
            <li><a href="<?php echo admin_url( '/admin.php?page=tw_queue&tw_fill_up=true' ); ?>" class="button">Refill</a></li>
            <li><a id="tw-empty-queue" href="#" class="button">Empty</a></li>
            <li><a id="tw-simple-view" href="#" class="button">Simple View</a></li>
        </ul>
        <span id="queue-status">Status: <?php echo $this->get_queue_status() == 0 ? 'Paused' : 'Running' ?></span>
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
    
    public function insert_post( $post_id, $skip = false ) {
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        
        global $wpdb;
        
        if( $this->is_item_queued( $post_id ) == true && $skip == false )
            return false;
        
        if( $this->is_item_excluded( $post_id ) == true && $skip == false )
            return;
        
        $result = $wpdb->insert(
            $wpdb->prefix . 'tw_queue',
            array(
                'queue' => $this->get_last_queued()->queue+1,
                'post_ID' => $post_id
            )
        );
        
        return $result;
        
    }
    
    public function remove_post( $post_id, $skip = false ) {
        
        global $wpdb;
        
        $result = $wpdb->query(
            "DELETE FROM " . $wpdb->prefix . "tw_queue WHERE post_ID = " . $post_id
        );
        
        return $result;
        
    }
    
    public function post_row_action( $actions, $post ) {
        
        //check for your post type
        if ( $post->post_type == "post" && $post->post_status == "publish" ) :

            if( $this->is_item_excluded( $post->ID ) ) 
                
                $actions['excluded'] = '<span style="color:#aaa">Excluded</span>';
            
            else if( $this->is_item_queued( $post->ID ) ) :
                
                $actions['dequeue'] = '<a style="color:#a00"  href="'.admin_url('/edit.php?tw_dequeue=' . $post->ID).'">Dequeue</a>';
                
            else :
                
                $actions['queue'] = '<a href="'.admin_url('/edit.php?tw_queue=' . $post->ID).'">Queue</a>';
                
            endif;
            
        endif;
        
        return $actions;
        
    }
    
    public function display_queued_items() {
        
        $in_queue = $this->get_queued_items();
        
        ?>
        
        <div id="the-queue">
            <!--<div class="the-queue-item tweeted">
                <div class="post-header">
                    <span class="title">Recently Tweeted...</span>
                    <time>Tweeted Today at 12:05</time>
                </div>
                <div class="post-content">
                    Test test test...
                </div>
            </div>-->
            <ul>
            <?php foreach( $in_queue as $q ) : ?>
                <li class="the-queue-item" id="<?php echo $q->post_ID; ?>">
                    <div class="post-header">
                        <span class="title"><?php echo get_the_title( $q->post_ID ); ?></span>
                        <span class="drag-handler"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAARCAYAAADHeGwwAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAKtwAACrcB78nXjgAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAETSURBVDiNtdUxK4VhFAfw33mTmVkyWJRRKawyiPItLCYfQN3LonQXExOLbAwm2X0DsVEGFgY2cQzeV+6bxXvdf52eznPqnPP8z//0BMaxhVkcoJ2Z7xARoxjRDM+ZeQPHyB+2kpmwhvda7K+2X2CmVrny51E07L7CwoAvWjZ+XB6W5yaGMdYw+RNa4avLRUzjKDOvGyb8FVHy3TcEhrCqVFFmnnwHIwoMNsz9VqlxV/fk58pXLeNBcwW9YB3ua4GdssBZD8kruy/wTUmJyt/DY0N64BWdvs+g7yoqIqKIiKWIaEfExH8XCLR0b/JkZl5FxBQ6etxkuNM9+e2StlO9q+i2wGWtcuVf4KNh9xXOQ5//g0/HsKumwosbiwAAAABJRU5ErkJgggb398138661cc24bd414674af8ee54426"/></span>
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
    
    public function remove_all() {
        
        global $wpdb;
        
        $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'tw_queue' );
        
    }
    
    public function pause() {
        
        update_option( 'tw_queue_running', 0 );
        
    }
    
    public function resume() {
        
        update_option( 'tw_queue_running', 1 );
        
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
            $this->insert_post( $post_id, true );
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
    
    public function cron() {
    
        if ( ! wp_next_scheduled( 'tw_cron_job' ) ) {
            wp_schedule_event( time(), 'hourly', 'tw_cron_job' );
        }
        
    }   
    
    // ...
    
    public function run_queue() {
        
        do_action( 'tw_before_cron' );
        
        // If queue is paused...
        if( 0 == get_option( 'tw_queue_running' ) )
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
                    update_option( 'tw_last_tweet', array( 'ID' => $q->post_ID, 'text' => $tweet ) );
                    
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
        
        if( is_array( $excluded ) && $excluded[0] == 1 )
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
        
        return get_option( 'tw_queue_running' );
        
    }
    
    public function get_last_queued() {
        
        global $wpdb;
        
        if( $this->has_queue_items() == false )
            return 0;
        
        $the_one = $wpdb->get_row(
            "SELECT * FROM " . $wpdb->prefix . "tw_queue ORDER BY ID DESC LIMIT 1"
        );
        
        return $the_one;
        
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