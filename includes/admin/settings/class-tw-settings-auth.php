<?php

class TW_Settings_Auth {
    
    private $plugin_path;
    private $wpsf;
    
    public function __construct() {
        
        if( TW()->twitter()->is_authed() )
            return;
        
        add_filter( 'tw_load_admin_menu', array( $this, 'menu' ) );
        
        $this->plugin_path = plugin_dir_path( __FILE__ );

        $this->wpsf = new TW_Settings();
        $this->wpsf->set_setting( $this->plugin_path .'settings/auth.php', 'tw_twitter_auth' );
        // Add an optional settings validation filter (recommended)
        add_filter( $this->wpsf->get_option_group() .'_settings_validate', array(&$this, 'validate_settings') );
        
    }
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => 'Authorize',
            'menu_title' => 'Authorize',
            'menu_slug'  => 'tw_twitter_auth',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    public function page() {
        
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
                    <p><?php echo TW()->twitter()->get_auth_url(); ?></p>
                </div>
            
            </div>
            
        </div>

		<?php
        
    }

    
}
return new TW_Settings_Auth;