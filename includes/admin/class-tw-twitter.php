<?php

// Library that handles Twitter API
require_once( TW_PLUGIN_DIR . '/includes/libraries/twitteroauth/autoloader.php' );

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * TW_Twitter Class
 *
 * @class TW_Twitter
 */

class TW_Twitter {
    
    // Keeps Twitter OAuth data
    private $auth;
    
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
    
        // Load auth data to the plugin
        $this->auth = (object) array(
            'consumer_key' => 'EHfVZKOX8r6I6OmgoZBhzyPJK',
            'consumer_secret' => 'Xiy6FFX3YtYVN8TdNxUwBqS2mQ2uD5mhpGuGEnDF1iLzRovhqj',
            'oauth_token' => get_option( 'tw_twitter_oauth_token' ),
            'oauth_token_secret' => get_option( 'tw_twitter_oauth_token_secret' )
        );
        
        add_action( 'tw_settings_options_type_deauth', array( $this, 'show_deauth_url' ) );

        // Check if there is a response from Twitter to handle
        add_action( 'init', array( $this, 'maybe_handle_response' ) );
        
        if( ! $this->is_authed() )
            add_filter( 'tw_load_admin_menu', array( $this, 'menu' ) );
        
    }
    
    // ...
    
    /**
     * Adds "Authorize" menu link
     *
     * @type function
     * @date 28/01/2015
	 * @since 0.1
     *
     * @param array
	 * @return array
     */
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => 'Authorize',
            'menu_title' => 'Authorize',
            'menu_slug'  => 'tw_twitter_auth',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    // ...
    
    /**
     * Authorize page content
     *
     * @type function
     * @date 28/01/2015
	 * @since 0.1
     *
     * @param n/a
	 * @return n/a
     */
    
    public function page() {
        
        if( $this->is_authed() )
            return;
        
	    ?>
        
		<div class="wrap tweet-wheel about-wrap">
            
            <div class="headline-feature">
                <h2>One more thing before we continue...</h2>
                <div class="feature-image">
                    <img style="margin:auto;display:block" src="<?php echo TW_PLUGIN_URL ?>/assets/images/tweet-wheel-auth-pic.png">
                </div>
                
                <div class="feature-section" style="text-align:center">
                    <h3>Twitter Authorization</h3>
                    <p>Before you can unleash the awesomeness of Tweet Wheel, you need to authorize our app to access your Twitter account. We promise to behave :)</p>
                    <p><?php echo $this->get_auth_url(); ?></p>
                </div>
            
            </div>
            
        </div>

		<?php
        
    }    
    
    // ...
    
    /**
     * Talks to Twitter. Handles authorisation, deauthorisation.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function maybe_handle_response() {
        
        if( isset( $_GET['deauth'] ) )
            $this->deauthorize();
        
        if( ! isset( $_GET['oauth_verifier'] ) && ! isset( $_GET['oauth_token'] ) )
            return;
        
        // Create Twitter API object
        $connection = new TwitterOAuth( 
            $this->auth->consumer_key, 
            $this->auth->consumer_secret,
            get_transient( 'tw_temp_oauth_token' ),
            get_transient( 'tw_temp_oauth_token_secret' )
        );

        // Try to authorize with given values
        try {
            
            $auth_tokens = $connection->oauth( 'oauth/access_token', array(
                'oauth_verifier' => $_GET['oauth_verifier']
            ) );
            
            if( isset( $auth_tokens['oauth_token'] ) && isset( $auth_tokens['oauth_token_secret'] ) ) :
                
                // On successful authorization, update the settings group to reflect that fact            
                update_option( 'tw_twitter_oauth_token', $auth_tokens['oauth_token'] );
                update_option( 'tw_twitter_oauth_token_secret', $auth_tokens['oauth_token_secret'] );
                update_option( 'tw_twitter_is_authed', 1 );
                
                delete_transient( 'tw_temp_oauth_token' );
                delete_transient( 'tw_temp_oauth_token_secret' );
                
                if( self::is_authed() == 1 )
                    wp_redirect( admin_url( '/admin.php?page=tw_settings' ) );
                exit;
                
            endif;
            
        } catch ( Exception $e ) {

            echo "There was an error returned by Twitter. Not your fault, though. Try again.";
            
        }
        
    }
    
    // ...
    
    /**
     * Builds an authorisation button.
     * User clicks and is redirected to Twitter to complete the process.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return string (html)
     **/
    
    public function get_auth_url() {
        
        $connection = new TwitterOAuth( 
            $this->auth->consumer_key, 
            $this->auth->consumer_secret
        );

        try {
            
            $request_token = $connection->oauth( 'oauth/request_token', array('oauth_callback' => admin_url( '/admin.php?page=' . $_GET['page'] ) ) );
            
            set_transient('tw_temp_oauth_token', $request_token['oauth_token'], 60*60);
            
            set_transient('tw_temp_oauth_token_secret', $request_token['oauth_token_secret'], 60*60);
        
            $url = $connection->url( 'oauth/authorize', 
                array( 'oauth_token' => get_transient('tw_temp_oauth_token' ) )
            );
    
            return '<a href="' . $url . '" class="tw-start-button button">Authorize &raquo;</a><p>You will be redirected to twitter.com and brought back after authorization.';
        
        } catch ( Exception $e ) {
            
            return "<span style='color:red'>Invalid consumer key and/or consumer secret.</span>";
            
        }
        
    }
    
    // ...
    
    /**
     * Returns user's authorisation data
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return object
     **/
    
    public function get_auth_data() {
        
        return $this->auth;
        
    }
    
    // ...
    
    /**
     * Determines if user is authorised with Twitter
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return boolean
     **/
    
    public static function is_authed() {
        
        if( get_option( 'tw_twitter_is_authed' ) == 1 )
            return true;
        
        return false;
        
    }
    
    // ...
    
    /**
     * Build a deauthorisation button on settings page
     *
     * @type function
     * @date 02/02/2015
     * @since 0.1
     *
     * @param N/A
     * @return string (html)
     **/
    
    public function get_deauth_url() {
        
        return '<a href="' . admin_url( '/admin.php?page=tw_settings&deauth=true' ) . '" class="button button-primary" style="background:#D3000D;border-color:#9A0009">De-Authorize &raquo;</a><p>Tweet Wheel will cease from working after de-authorization. Re-authorization will be required to resume the plugin.</p>';
        
    }
    
    // ...
    
    public static function show_deauth_url() {
     
        echo '<a href="' . admin_url( '/admin.php?page=tw_settings&deauth=true' ) . '" class="button button-primary" style="background:#D3000D;border-color:#9A0009">De-Authorize &raquo;</a><p>Tweet Wheel will cease from working after de-authorization. Re-authorization will be required to resume the plugin.</p>';
        
    }
    
    // ...
    
    /**
     * Deauthorises and redirects to authorisation screen
     *
     * @type function
     * @date 02/02/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function deauthorize() {

        if( self::is_authed() == true ) :
            
            delete_option( 'tw_twitter_oauth_token' );
            delete_option( 'tw_twitter_oauth_token_secret' );
            delete_option( 'tw_twitter_is_authed' );

            wp_redirect( admin_url( '/admin.php?page=tw_twitter_auth' ) );
            
        endif;
        
        return;
        
    }
    
}

