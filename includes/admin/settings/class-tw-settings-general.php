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
		
		add_action( 'wp_ajax_get_post_types', 'ajax_get_post_types' );

		add_filter( 'tw_settings_tab_options-general', array( $this, 'post_type_value' ), 10, 2 );
		add_action( 'tw_settings_options_type_post_type', array( $this, 'post_type_settings' ) );
		add_action( 'tw_settings_after_form_tab-general', array( $this, 'post_type_js' ) );

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
	
	public function post_type_settings() {
		
		echo '<div id="post_type_wrapper"></div>';
		
	}
	
    public function post_type_value( $tabs, $post ) {

        if( ! isset( $tabs['general'] ) )
            return $tabs;
        
        if( ! isset( $post['post_type'] ) )
            $post['post_type'] = array();

        $tabs['general'][] = array(
            'name' => __( 'Allowed post types', 'tweet-wheel' ),
            'id' => 'post_type',
            'type' => 'post_type',
            'options' => $post['post_type']
        );
        
        return $tabs;
        
    }
	
	public function post_type_js() {
		
		$options = tw_get_option( 'tw_settings', 'post_type' );
		
		?>
		
		<script>
		jQuery.noConflict();
		jQuery(window).load(function(){
	
			var el = jQuery('#post_type_wrapper');
			var post_types = jQuery.parseJSON('<?php echo json_encode($options); ?>');
		
			if( el.length == 0 )
				return;
		
			el.text( 'Loading...' );
		
			jQuery.get(
				ajaxurl, 
				{
					action: 'get_post_types',
					twnonce: TWAJAX.twNonce
				},
				function( response ) {
				
					var data = jQuery.parseJSON( response );
				
					if( data.response == 'error' ) {
						el.text( data.message );
					}
				
					el.empty();

					jQuery.each( data.data, function( k,v ) {
						
						var is_checked = jQuery.inArray( k, post_types ) != -1 ? true : false;
					
						var html = '<label for="post_type_'+k+'"><input name="tw_settings_options[post_type][]" id="post_type_'+k+'" type="checkbox" value="'+k+'" '+( is_checked ? 'checked' : '' )+'>'+v.label+'</label><br/>';

						el.append(html);
					
					} );
				
				}
			);
		
		});
		
		</script>
		
		<?php		
	}

}
new TW_Settings_General;