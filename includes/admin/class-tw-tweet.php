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
    
    // ...
    
    /**
     * Class constructor
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function __construct() {

        // Loads allowed tags for tweet template
        $this->tags = $this->allowed_tags();
        
        // Required JS variables for the preview metabox
        add_action( 'admin_print_scripts', array( $this, 'mb_print_js' ) );
        
        // Handles tweeting on demand
        add_action( 'wp_ajax_tweet', 'ajax_tweet' );
        
    }
    
    // ...
    
    /**
     * Renders a custom tweet template preview within a metbox on post edit screen
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return string (html)
     **/
    
    public function metabox_field_preview() {
        
        // Metabox framework doesn't pass post id and its an admin area
        // so i decide to go for the most basic solution...
        if( ! isset( $_GET['post'] ) )
            return;
        
        $id = $_GET['post'];

        $html = '<div class="mb-tweet-preview">
            <div id="count"></div>
            <div class="tweets-column">
                
                <ul>
                    <li class="preview-box"><img class="avatar small" src="https://abs.twimg.com/sticky/default_profile_images/default_profile_6_bigger.png"><div id="tweet-preview-box" class="pull-right"><div id="tweet-preview">'.$this->parse( $id, $this->get_tweet( $id ) ).'</div></div></li>
                    <li class="fake-tweet">
                        <img class="avatar" src="https://abs.twimg.com/sticky/default_profile_images/default_profile_6_bigger.png"><p class="pull-right">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam consectetur libero nisi, a malesuada dolor amet.</p>
                    </li>
                </ul>
                
            </div>
            
        </div>';
        
        return $html;
        
    }
    
    // ...
    
    /**
     * Metabox JS variables - template tags.
     *
     * @TODO: Make it more flexible and automated, in case we wanted more tags.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function mb_print_js() {
        
        if( ! isset( $_GET['post'] ) )
            return;
        
        $id = $_GET['post'];
        
        ?>
        
        <script>
        
        var post_title = '<?php echo html_entity_decode(get_the_title($id),ENT_QUOTES,'UTF-8'); ?>';
        var post_url = '<?php echo get_permalink( $id ); ?>';
        
        </script>
        
        <?php
        
    }
    
    // ...
    
    /**
     * Returns a ready-to-go tweet; a tweet in its final form
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return string
     **/
    
    public function preview( $post_id ) {
        
        return $this->parse( $post_id, $this->get_tweet( $post_id ) );
        
    }
    
    // ...
    
    /**
     * Parses a tweet template; replaces tags with a proper values.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return string
     **/
    
    public function parse( $post_id, $tweet ) {
        
        if( empty( $this->tags ) )
            return;
        
        foreach( $this->tags as $tag => $func ) :
            
            $tweet = str_replace( '{{'.$tag.'}}', call_user_func( $func, $post_id, $tweet ), $tweet );
            
        endforeach; 
        
        return html_entity_decode( $tweet, ENT_QUOTES, 'UTF-8' );
        
    }
    
    // ...
    
    /**
     * Include allowed template tags. Feel free to add your own using the filter.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return array
     **/
    
    public function allowed_tags() {
        
        $tags = array(
            'URL' => 'tw_tweet_parse_url',
            'TITLE' => 'tw_tweet_parse_title'
        );
        
        $tags = apply_filters( 'tw_tweet_allowed_tags', $tags );
        
        return $tags;
        
    }
    
    // ...
    
    /**
     * Get tweet's custom text (without parsing)
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return string
     **/
    
    public function get_tweet( $post_id ) {
        
        $meta = get_post_meta($post_id, 'tweet_text'); 
        
        if( empty( $meta ) )
            return wpsf_get_setting( 'tw_settings', 'template', 'tweet_text' );
        
        return $meta[0];
        
    }
    
    // ...
    
    /**
     * The Magic!
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function tweet( $post_id = null ) {
        
        if( ! TW()->twitter()->is_authed() )
            return false;
        
        $auth = TW()->twitter()->get_auth_data();
        
        if( $post_id == null && ! TW()->queue()->has_queue_items() )
            return false;
        
        $post_id = $post_id != null ? $post_id : TW()->queue()->get_first_queued_item()->post_ID;

        $tweet = apply_filters( 'tw_tweet_text', $this->preview( $post_id ), $post_id );
        
        // Make sure a tweet is 140 chars. 
        // Consider it a user error and send the tweet anyway.
        if( strlen( $tweet ) > 140 )
            $tweet = substr( $tweet, 0, 140 );

        // Create a connection with Twitter
        $connection = new TwitterOAuth( 
            $auth->consumer_key, 
            $auth->consumer_secret,
            $auth->oauth_token,
            $auth->oauth_token_secret
        );

        // Sending a tweet....
        $response = $connection->post( "statuses/update", array( "status" => $tweet ) );

        if( is_array( $response->errors ) ) :
            
            do_action( 'tw_tweet_error', $post_id, $response );

            return false;
            
        endif;
        
        do_action( 'tw_before_tweet_dequeue', $post_id );
        
        // Remove post from the queue
        TW()->queue()->remove_post( $post_id );
        
        do_action( 'tw_after_tweet_dequeue', $post_id );
        
        // If loop goes infinitely
        if( wpsf_get_setting( 'tw_settings', 'timing', 'loop' ) == 1 )
            TW()->queue()->insert_post( $post_id );
        
        update_option( 'tw_last_tweet', array( 'ID' => $post_id, 'title' => get_the_title( $post_id ), 'text' => $tweet ) );
        
        do_action( 'tw_after_tweet', $post_id );

        return $post_id;
        
    }

}

// ...

/**
 * A callback for {{URL}} template tag
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function tw_tweet_parse_url( $post_id, $tweet ) {

    return get_permalink( $post_id );
    
}

// ...

/**
 * A callback for {{TITLE}} template tag
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

function tw_tweet_parse_title( $post_id, $tweet ) {
    
    return get_the_title( $post_id );
    
}

