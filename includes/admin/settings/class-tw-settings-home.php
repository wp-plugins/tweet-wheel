<?php

class TW_Settings_Home {
    
    private $plugin_path;
    private $wpsf;
    
    public function __construct() {
        
        $this->plugin_path = plugin_dir_path( __FILE__ );

        $this->wpsf = new TW_Settings();
        $this->wpsf->set_setting( $this->plugin_path .'settings/home.php', 'my_example_settings' );
        // Add an optional settings validation filter (recommended)
        add_filter( $this->wpsf->get_option_group() .'_settings_validate', array(&$this, 'validate_settings') );
        
    }
    
    public function page() {
        
	    ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>WP Settings Framework Example</h2>
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
new TW_Settings_Home;