<?php

require_once( TW_PLUGIN_DIR . '/includes/libraries/twitteroauth/autoloader.php' );

use Abraham\TwitterOAuth\TwitterOAuth;

class TW_Twitter {
    
    private $auth;
    
    private $state;
    
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
    
        $this->auth = (object) array(
            'consumer_key' => 'EHfVZKOX8r6I6OmgoZBhzyPJK',
            'consumer_secret' => 'Xiy6FFX3YtYVN8TdNxUwBqS2mQ2uD5mhpGuGEnDF1iLzRovhqj',
            'oauth_token' => wpsf_get_setting( 'tw_twitter_auth', 'twitter_auth', 'oauth_token' ),
            'oauth_token_secret' => wpsf_get_setting( 'tw_twitter_auth', 'twitter_auth', 'oauth_token_secret' )
        );

        add_action( 'init', array( $this, 'maybe_handle_response' ) );
        
    }
    
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
                $new_options = wp_parse_args(
                    wpsf_get_settings( 'tw_twitter_auth' ),
                    array(
                        'tw_twitter_auth_twitter_auth_oauth_token' => $auth_tokens['oauth_token'],
                        'tw_twitter_auth_twitter_auth_oauth_token_secret' => $auth_tokens['oauth_token_secret'],
                        'tw_twitter_auth_twitter_auth_is_authed' => 1
                    )
                );
                
                wpsf_update_settings( 'tw_twitter_auth', $new_options );
                
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
    
    public function get_auth_url_field() {
        
        if( $this->auth->consumer_key == '' || $this->auth->consumer_secret == '' )
            return "Please provide above values to continue with authorization.";
        
        return $this->get_auth_url();        
        
    }
    
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
    
            return '<a href="' . $url . '" class="button button-primary">Authorize &raquo;</a><p>You will be redirected to twitter.com and brought back after authorization.';
        
        } catch ( Exception $e ) {
            
            return "<span style='color:red'>Invalid consumer key and/or consumer secret.</span>";
            
        }
        
    }
    
    public function get_auth_data() {
        
        return $this->auth;
        
    }
    
    public static function is_authed() {
        
        if( wpsf_get_setting( 'tw_twitter_auth', 'twitter_auth', 'is_authed' ) == 1 )
            return true;
        
        return false;
        
    }
    
    public function get_deauth_url() {
     
        return '<a href="' . admin_url( '/admin.php?page=tw_settings&deauth=true' ) . '" class="button button-primary" style="background:#D3000D;border-color:#9A0009">De-Authorize &raquo;</a><p>Tweet Wheel will cease from working after de-authorization. Re-authorization will be required to resume the plugin.</p>';
        
    }
    
    public function deauthorize() {

        if( self::is_authed() == true ) :
            
            wpsf_delete_settings( 'tw_twitter_auth' );
            
            wp_redirect( admin_url( '/admin.php?page=tw_twitter_auth' ) );
            
        endif;
        
        return;
        
    }
    
}

