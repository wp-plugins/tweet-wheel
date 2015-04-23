<?php

use Abraham\TwitterOAuth\TwitterOAuth;

class TW_Tweet {
    
    private $tags;
    
    public static $_instance = null;

    // ...
    
	/**
	 * Main TW_Tweet Instance
	 *
	 * Ensures only one instance of TW_Tweet is loaded or can be loaded.
	 *
	 * @since 0.1
	 * @static
	 * @return TW_Tweet - Main instance
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
        
        // Required JS variables for template tags
        add_action( 'admin_print_scripts', array( $this, 'mb_print_js' ) );
        
        // Handles tweeting on demand
        add_action( 'wp_ajax_tweet', 'ajax_tweet' );
        
    }
    
    // ...
    
    /**
     * Metabox JS variables - template tags.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function mb_print_js() {
        
        global $post;
        
        if( $post == null || empty( $this->tags ) )
            return;
        
        $id = $post->ID;
        
        ?>

        <script>
            var tw_template_tags = {
        <?php
        
        $i = 1;
        
        foreach( $this->tags as $tag => $func ) :
        
            ?>
            <?php echo strtoupper( $tag ); ?> : '<?php echo call_user_func( $func, $post_id, $tweet ); ?>'<?php echo $i != count($this->tags) ? ',' : ''; ?>
            <?php
            
            $i++;
        
        endforeach; 
        
        ?>
                
            };
        
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
        
        return $this->parse( $post_id );
        
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
    
    public function parse( $post_id, $tweet = null ) {
        
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
    
    // BACKWARDS COMPATIBILITY - DON'T USE AS IT WILL BE REMOVED COMPLETELY
    public function get_tweet( $post_id ) {
        
        return $this->get_default_template();
        
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

        $order = $this->get_tweeting_order( $post_id );
        
        switch( $order ) :
        
            case 'random';
            $raw_tweet = $this->get_random_template( $post_id );
            break;
        
            default:
            $raw_tweet = $this->get_next_template( $post_id );
            break;
        
        endswitch;

        $tweet = apply_filters( 'tw_tweet_text', $raw_tweet, $post_id );
        
        $tweet = $this->parse( $post_id, $raw_tweet );
        
        // Make sure a tweet is 140 chars. 
        // Consider it a user error and send the tweet anyway.
        if( tw_character_counter( $tweet, $post_id ) > 140 )
            return false;

        // Create a connection with Twitter
        $connection = new TwitterOAuth( 
            $auth->consumer_key, 
            $auth->consumer_secret,
            $auth->oauth_token,
            $auth->oauth_token_secret
        );

        // Sending a tweet....
        $response = $connection->post( "statuses/update", array( "status" => $tweet ) );

        if( isset( $response->errors ) && is_array( $response->errors ) ) :
            
            do_action( 'tw_tweet_error', $post_id, $response );

            return false;
            
        endif;
        
        do_action( 'tw_before_tweet_dequeue', $post_id );
        
        // Remove post from the queue
        TW()->queue()->remove_post( $post_id );
        
        do_action( 'tw_after_tweet_dequeue', $post_id );
        
        // If loop goes infinitely
        if( tw_get_option( 'tw_settings', 'loop' ) == 1 )
            TW()->queue()->insert_post( $post_id );
        
        update_option( 
            'tw_last_tweet', 
            array( 
                'ID' => $post_id, 
                'title' => get_the_title( $post_id ), 
                'text' => $tweet, 
                'time' => current_time('timestamp') 
            ) 
        );
        
        update_post_meta( $post_id,  'tw_last_tweeted_template', $raw_tweet );
        
        do_action( 'tw_after_tweet', $post_id );

        return $post_id;
        
    }
    
    // ...
    
    /**
     * Check if a post has multiple templates
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param int
	 * @return boolean
     */
    
    public function has_multiple_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'tw_post_templates', true );
        
        if( count( $meta ) > 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Count post templates
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param int
	 * @return int | null
     */
    
    public function count_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'tw_post_templates', true );
        
        return count( $meta );
        
    }
    
    // ...
    
    /**
     * Checks if a post has any custom templates (even one)
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param int
	 * @return boolean
     */
    
    public function has_custom_templates( $post_id ) {
     
        if( $post_id == null )
            return;
        
        $meta = get_post_meta( $post_id, 'tw_post_templates', true );
        
        if( $meta == '' || count( $meta ) == 0 )
            return false;
        
        return true;
        
    }
    
    // ...
    
    /**
     * Retrieve post's all custom templates
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param int
	 * @return null | array
     */
    
    public function get_custom_templates( $post_id ) {
        
        if( $post_id == null )
            return;
        
        return get_post_meta( $post_id, 'tw_post_templates', true );
        
    }
    
    // ...
    
    /**
     * Retrieves default template setting
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return string
     */
    
    public function get_default_template() {
     
        return tw_get_option( 'tw_settings', 'tweet_template' );

    }
    
    // ...
    
    /**
     * Retrieves last tweeted template for a post
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param int
	 * @return string | false
     */

    public function get_last_tweeted_template( $post_id ) {
        
        $template = get_post_meta( $post_id, 'tw_last_tweeted_template', true );
        
        if( '' != $template )
            return $template;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Retrieves tweeting order for a post (random or following the order)
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param int
	 * @return string
     */
    
    public function get_tweeting_order( $post_id ) {
        
        return get_post_meta( $post_id, 'tw_templates_order', true ); 
        
    }
    
    // ...
    
    /**
     * Retrieves random template for a post
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param int
	 * @return string
     */
    
    public function get_random_template( $post_id ) {
        
        // fallback if misused on single-templated post
        if( ! TW()->tweet()->has_multiple_templates( $post_id ) )
            return $this->get_next_template( $post_id );
        
        $meta = TW()->tweet()->get_custom_templates( $post_id );
        $sanitized = '';

        foreach( $meta as $k => $v ) :
        
            $sanitized[$k] = sanitize_title_with_dashes( $v );
        
        endforeach;
        
        // check for last tweeted
        $last_tweeted_template = $this->get_last_tweeted_template( $post_id );
        
        if( $last_tweeted_template ) :
        
            $last_tweeted_template = sanitize_title_with_dashes( $last_tweeted_template );

            $key = array_search( $last_tweeted_template, $sanitized );

            if( false !== $key && isset( $meta[$key] ) )
                unset( $meta[$key] );
        
        endif;
        
        return $meta[array_rand( $meta )];
        
    }
    
    // ...
    
    /**
     * Retrieves next template for a post (the one after recently tweeted one)
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param int
	 * @return string
     */
    
    public function get_next_template( $post_id ) {
        
        // custom & multiple
        if( $this->has_multiple_templates( $post_id ) ) :
        
            $meta = $this->get_custom_templates( $post_id );
            $sanitized = '';

            foreach( $meta as $k => $v ) :

                $sanitized[$k] = sanitize_title_with_dashes( $v );

            endforeach;
        
            // @TODO - get it from post meta or sth
            $last_tweeted_template = sanitize_title_with_dashes( $this->get_last_tweeted_template( $post_id ) );
        
            $key = array_search( $last_tweeted_template, $sanitized );
        
            // If last tweeted template no longer exist, fallback to first in the array
            if( $key === false ) :
                
                $key = key($sanitized);
        
                return $meta[$key];    
            
            // If last tweeted template exists, go for next!
            else :
                
                return get_next_in_array( $meta, $key );
                    
            endif;
        
        endif;
    
        // custom template
        if( $this->has_custom_templates( $post_id ) )
            return array_shift( $this->get_custom_templates( $post_id ) );
        
        // fallback to default
        return $this->get_default_template();
        
    }

}
// has to be here for js tags templates var to be printed in admin header... not sure why, gotta sort it later!
new TW_Tweet;

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
    
    return html_entity_decode(get_the_title($post_id),ENT_QUOTES,'UTF-8');
    
}

// ...

/**
 * Counts characters in Twitter way
 *
 * @type function
 * @date 24/03/2015
 * @since 0.4
 *
 * @param string
 * @return int
 */

function tw_character_counter( $raw, $post_id = null ) {
    
    global $post;
    
    if( $post_id == null )
        $post_id = $post->ID;
    
    // Max characters accepted for a single tweet
    $maxCharacters = 140;
    
    // Load custom tweet text to a variable
    $tweet_template = $raw;
    
    // ...
    
    $tags = TW()->tweet()->allowed_tags();

    if( ! empty( $tags ) ) : 
    
        foreach( $tags as $t => $func ) :
    
            $tweet_template = str_replace( '{{' . $t . '}}', call_user_func( $func, $post_id, null ), $tweet_template );
    
        endforeach;
    
    endif;
    
    /**
     * Calculate a whole string length
     */
    
    $current_length = strlen( $tweet_template );

    // ...
    
    /**
     * Amend character limit if URL is detected (22 characters per url)
     */
    
    $url_chars = 22;

    // urls will be an array of URL matches
    preg_match_all("/(?:(?:https?|ftp):\\/\\/)?(?:\\S+(?::\\S*)?@)?(?:(?!(?:10|127)(?:\\.\\d{1,3}){3})(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))|(?:(?:[a-z\\x{00a1}-\\x{ffff}0-9]+-?)*[a-z\\x{00a1}-\\x{ffff}0-9]+)(?:\\.(?:[a-z\\x{00a1}-\\x{ffff}0-9]+-?)*[a-z\\x{00a1}-\\x{ffff}0-9]+)*(?:\\.(?:[a-z\\x{00a1}-\\x{ffff}]{2,})))(?::\\d{2,5})?(?:\\/?[^\\s]*)?/u", $tweet_template, $urls);
    
    $urls = array_shift( $urls );
    
    // If urls were found, play the max character value accordingly
    if( ! empty( $urls ) ) {
        
        foreach( $urls as $u ) {
            
            // get url length difference
            $diff = $url_chars - strlen( $u );
            
            // apply difference
            $current_length = $current_length + $diff;
           
        }
        
    }
    
    // return actually tweet length
    return $current_length;
    
}
