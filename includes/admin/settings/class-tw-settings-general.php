<?php

class TW_Settings_General {
	
	private $settings_framework = null;
    
    public function __construct() {
        
        // Settings only for authed users
        if( TW()->twitter()->is_authed() == 0 )
            return;
        
        add_filter( 'tw_load_admin_menu', array( $this, 'menu' ) );

		$this->settings_framework = new SF_Settings_API( $id = 'tw_settings', $title = '', __FILE__);
		
		$this->settings_framework->load_options( dirname( __FILE__ ) . '/options/option.php');

    }
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => 'Settings',
            'menu_title' => 'Settings',
            'menu_slug'  => 'tw_settings',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    public function page() {
        
	    ?>
		<div class="wrap tw-settings-page">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TW_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"><?php _e( 'Tweet Wheel Settings', 'tweetwheel' ); ?></h2>
			<?php $this->settings_framework->init_settings_page(); ?>
		</div>
		<?php
        
    }
	
    
}
new TW_Settings_General;