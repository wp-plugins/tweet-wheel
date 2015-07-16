<?php

/**
 * Main class TW_Debug
 */

class TW_Debug {
    
    public static $_instance = null;
    
    // ...
    
	/**
	 * Main TW_Debug Instance
	 *
	 * Ensures only one instance of TW_Debug is loaded or can be loaded.
	 *
	 * @since 0.4
	 * @static
	 * @return TW_Debug object
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TW_Debug constructor
     *
	 * @type function
     * @date 28/03/2015
     * @since 0.4
     *
     * @param N/A
	 * @return n/a
	 */

    public function __construct() {
        
        // Add admin menu
        add_filter( 'tw_load_admin_menu', array( $this, 'menu' ), 999 );
        
    }
    
    // ...

	/**
	 * Adds "Health Check" item to the Tweet Wheel menu tab
	 *
     * @type function
     * @date 28/03/2015
	 * @since 0.4
     *
     * @param array
	 * @return array
	 */
    
    public function menu( $menu ) {
        
        $menu[] = array(
            'page_title' => 'Health Check',
            'menu_title' => 'Health Check',
            'menu_slug'  => 'tw_debug',
            'function'   => array( $this, 'page' )
        );
        
        return $menu;
        
    }
    
    // ...
    
	/**
	 * Loads the Debug screen
	 *
     * @type function
     * @date 28/03/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function page() {
        
        ?>
        
		<div class="wrap tweet-wheel tw-debug-page">
			<h2><img class="alignleft" style="margin-right:10px;" src="<?php echo TW_PLUGIN_URL . '/assets/images/tweet-wheel-page-icon.png'; ?>"> Health Check</h2>
        
           <table class="tw-report-table widefat" style="margin-top:20px;" cellspacing="0">
               
               <?php
                   
               foreach( $this->health_check() as $c ) :
                   
                   ?>
                   
                   <thead>
                       
                       <tr>
                           
                           <th colspan="2"><?php echo $c[0]; ?></th>
                           
                       </tr>
                       
                   </thead>
                   
                   <tbody>
                       
                       <?php foreach( $c[1] as $check ) : ?>
                           
                           <tr>
                               <td><?php echo $check[0]; ?></td>
                               <td><?php echo $check[1]; ?></td>
                           </tr>
                           
                       <?php endforeach; ?>
                       
                   </tbody>
                   
                   <?php
                   
               endforeach;
                   
               ?>
               
           </table>
            
        </div>
        
        <?php
        
    }
    
    // ...
    
    /**
     * Array of checks to perform
     *
     * @type function
     * @date 28/03/2015
     * @since 0.4
     *
     * @param n/a
     * @return array
     */
    
    public function health_check() {
        
        global $tw_db_version;
        
        $checks = array(
            array(
                'Tweet Wheel',
                array(
                    array( 'Version', TW_VERSION ),
                    array( 'Database Version', $tw_db_version )
                )
            ),
            array(
                'WordPress Installation',
                array(
                    array( 'Home URL', get_bloginfo( 'url' ) ),
                    array( 'Site URL', site_url() ),
                    array( 'WP Multisite', ( is_multisite() ? 'Yes' : 'No' ) ),
                    array( 'WP Version', get_bloginfo( 'version' ) ),
                    array( 'WP Cron', TW_Cron()->is_wp_cron_disabled() ? '<span style="color:red">Disabled</span>' : '<span style="color:green">Enabled</span>' )
                )
            ),
            array(
                'Server Environment',
                array(
                    array( 'Web Server', $_SERVER['SERVER_SOFTWARE'] ),
                    array( 'cURL Module', ( function_exists('curl_version') ? '<span style="color:green">Installed</span>' : '<span style="color:red">Not Installed</span>' ) )
                )
            )
        );
        
        return apply_filters( 'tw_debug_health_checks', $checks );
        
    }
    
}

/**
 * Returns the main instance of TW_Debug
 *
 * @since  0.4
 * @return TW_Debug
 */
function TW_Debug() {
	return TW_Debug::instance();
}
TW_Debug();