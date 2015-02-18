<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class TW_Tweet {
    
    private $tags;
    
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

        $this->tags = $this->allowed_tags();
        
        add_action( 'admin_print_scripts', array( $this, 'mb_print_js' ) );
        add_action( 'wp_ajax_tweet', 'ajax_tweet' );
        
    }
    
    public function metabox_field_preview() {
        
        $id = $_GET['post'];

        $html = '<div class="mb-tweet-preview">
            <div id="count">'.strlen( $this->parse( $id, $this->get_tweet( $id ) ) ).'</div>
            <div class="tweets-column">
                
                <ul>
                    <li class="preview-box"><img class="avatar small" src="https://abs.twimg.com/sticky/default_profile_images/default_profile_6_bigger.png"><textarea id="tweet-preview" class="autoresize pull-right" placeholder="What\'s happening?" tabindex="-1">'.$this->parse( $id, $this->get_tweet( $id ) ).'</textarea></li>
                    <li class="fake-tweet">
                        <img class="avatar" src="https://abs.twimg.com/sticky/default_profile_images/default_profile_6_bigger.png"><p class="pull-right">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam consectetur libero nisi, a malesuada dolor amet.</p>
                    </li>
                </ul>
                
            </div>
            
        </div>';
        
        return $html;
        
    }
    
    // ...
    
    public function mb_print_js() {
        
        if( ! isset( $_GET['post'] ) )
            return;
        
        $id = $_GET['post'];
        
        ?>
        
        <script>
        
        var post_title = '<?php echo get_the_title( $id ); ?>';
        var post_url = '<?php echo get_permalink( $id ); ?>';
        
        </script>
        
        <?php
        
    }
    
    public function preview( $post_id ) {
        
        return $this->parse( $post_id, $this->get_tweet( $post_id ) );
        
    }
    
    public function parse( $post_id, $tweet ) {
        
        if( empty( $this->tags ) )
            return;
        
        foreach( $this->tags as $tag => $func ) :
            
            $tweet = str_replace( '{{'.$tag.'}}', call_user_func( $func, $post_id, $tweet ), $tweet );
            
        endforeach; 
        
        return $tweet;
        
    }
    
    public function allowed_tags() {
        
        $tags = array(
            'URL' => 'tw_tweet_parse_url',
            'TITLE' => 'tw_tweet_parse_title'
        );
        
        $tags = apply_filters( 'tw_tweet_allowed_tags', $tags );
        
        return $tags;
        
    }
    
    public function get_tweet( $post_id ) {
        
        $meta = get_post_meta($post_id, 'tweet_text'); 
        
        if( empty( $meta ) )
            return wpsf_get_setting( 'tw_settings', 'template', 'tweet_text' );
        
        return $meta[0];
        
    }
    
    // ...
    
    /**
     * Twitter API wrappers below
     */
    
    public function tweet( $post_id = null ) {
        
        if( ! TW()->twitter()->is_authed() )
            return false;
        
        $auth = TW()->twitter()->get_auth_data();
        
        if( $post_id == null && ! TW()->queue()->has_queue_items() )
            return false;
        
        $post_id = $post_id != null ? $post_id : TW()->queue()->get_first_queued_item()->post_ID;

        // get from queue - $posts = get_posts( $args );

        $tweet = apply_filters( 'tw_tweet_text', $this->preview( $post_id ), $post_id );

        $connection = new TwitterOAuth( 
            $auth->consumer_key, 
            $auth->consumer_secret,
            $auth->oauth_token,
            $auth->oauth_token_secret
        );

        $response = $connection->post( "statuses/update", array( "status" => $tweet ) );

        if( is_array( $response->errors ) ) :
            
            do_action( 'tw_tweet_error', $post_id, $response );

            wp_mail( 'tomasz@nerdcow.co.uk', 'Tweet Wheel couldn\'t send a tweet', json_encode( $response ) );

            return false;
            
        endif;
        
        do_action( 'tw_before_tweet_dequeue', $post_id );
        
        // Remove post from the queue
        TW()->queue()->remove_post( $post_id );
        
        do_action( 'tw_after_tweet_dequeue', $post_id );
        
        // If loop goes infinitely
        if( wpsf_get_setting( 'tw_settings', 'timing', 'loop' ) == 1 )
            TW()->queue()->insert_post( $post_id );
        
        do_action( 'tw_after_tweet', $post_id );

        return $post_id;
        
    }

}

function tw_tweet_parse_url( $post_id, $tweet ) {

    return get_permalink( $post_id );
    
}


function tw_tweet_parse_title( $post_id, $tweet ) {
    
    return get_the_title( $post_id );
    
}

