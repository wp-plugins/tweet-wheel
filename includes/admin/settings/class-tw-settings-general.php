<?php

class TW_Settings_General {
    
    private $plugin_path;
    private $wpsf;
    
    public function __construct() {
        
        // Settings only for authed users
        if( TW()->twitter()->is_authed() == 0 )
            return;
        
        add_filter( 'tw_load_admin_menu', array( $this, 'menu' ) );
        
        $this->plugin_path = plugin_dir_path( __FILE__ );

        $this->wpsf = new TW_Settings();
        $this->wpsf->set_setting( $this->plugin_path .'settings/settings.php', 'tw_settings' );
        // Add an optional settings validation filter (recommended)
        add_filter( $this->wpsf->get_option_group() .'_settings_validate', array(&$this, 'validate_settings') );
        
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
		<div class="wrap">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TW_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"> General Settings</h2>
			<?php
			// Output your settings form
			$this->wpsf->settings();
			?>
		</div>
		<?php
        
    }
    
	function validate_settings( $input )
	{
	    // Do your settings validation here
	    // Same as $sanitize_callback from http://codex.wordpress.org/Function_Reference/register_setting
    	return $input;
	}
    
}
return new TW_Settings_General;