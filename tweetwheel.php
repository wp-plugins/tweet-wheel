<?php
/**
 * Plugin Name: Tweet Wheel
 * Plugin URI: http://www.tweetwheel.com
 * Description: A powerful tool that keeps your Twitter profile active. Even when you are busy.
 * Version: 0.1
 * Author: Tomasz Lisiecki from Nerd Cow
 * Author URI: https://nerdcow.co.uk
 * Requires at least: 3.8
 * Tested up to: 4.1
 *
 * Text Domain: tweetwheel
 * Domain Path: /i18n/languages/
 *
 * @package Tweet Wheel
 * @category Core
 * @author Nerd Cow
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'TweetWheel' ) ) :

/**
 * Main TweetWheel Class
 *
 * @class TweetWheel
 * @version	0.0.1
 */
final class TweetWheel {
    
    /**
     * @var string
     */
    public $version = '0.1';
    
    // ...
    
    /**
     * @var the singleton
     * @static
     */
    protected static $_instance = null;
    
    // ...
    
    /**
     * @var TW_Twitter object
     */
    public $twitter = null;
    
    // ...
    
    /**
     * @var TW_Queue object
     */
    public $queue = null;
    
    // ...
    
	/**
	 * Main TweetWheel Instance
	 *
	 * Ensures only one instance of TweetWheel is loaded or can be loaded.
	 *
	 * @since 0.0.1
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
	 * Cloning is forbidden.
	 *
	 * @since 0.0.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'tweetwheel' ), '0.1' );
	}
    
    // ...

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.0.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'tweetwheel' ), '0.1' );
	}
    
    // ...
    
    /**
     * TweetWheel Constructor
     *
     * @type function
     * @date 28/01/2015
     * @since 0.0.1
     *
     * @param N/A
     * @return N/A 
     */
    
    public function __construct() {

        // Define all necessary constants
        $this->constants();
        
        // Load dependencies
        $this->includes();
        
        // Hooks
        register_activation_hook( __FILE__, 'tw_install' );
        register_activation_hook( __FILE__, 'tw_load_settings' );
        register_activation_hook( __FILE__, 'tw_after_activate' );
        add_action( 'admin_init', array( $this, 'redirect' ) );
        
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
        
        // Hook after loading the plugin. You welcome.
        do_action( 'tweetwheel_loaded' );
        
    }
    
    // ...
    
    /**
     * Define constants used in the plugin
     *
     * @type function
     * @date 28/01/2015
     * @since 0.0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    private function constants() {
        
        // Plugin Version
        define( 'TW_VERSION', $this->version );
        
        // Paths
        define( 'TW_PLUGIN_FILE', __FILE__ );
        define( 'TW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        define( 'TW_PLUGIN_DIR', dirname( __FILE__ ) );
        define( 'TW_PLUGIN_URL', plugins_url( '/tweet-wheel' ) );
        
    }
    
    // ...
    
    /**
     * Include all dependencies, not loaded by autoload of course
     *
     * @type function
     * @date 28/01/2015
     * @since 0.0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    private function includes() {
        
        // initial stuff
        include_once( 'includes/install.php' );
        include_once( 'includes/helpers.php' );
        
        // Fundamental settings
        include_once( 'includes/admin/class-tw-menus.php' );
        include_once( 'includes/admin/class-tw-metaboxes.php' );
        include_once( 'includes/admin/class-tw-settings.php' );
        
        // Third-parties
        include_once( 'includes/libraries/twitteroauth/autoloader.php' );
        
        // Twitter Class
        include_once( 'includes/admin/class-tw-twitter.php' );
        
        // Tweet Class
        include_once( 'includes/admin/class-tw-tweet.php' );
        
        // Queue Class
        include_once( 'includes/admin/class-tw-queue.php' );
        
        // Dashboard Class
        include_once( 'includes/admin/class-tw-dashboard.php' );
        

        if( defined( 'DOING_AJAX' ) ) :
            $this->ajax_includes();
        endif;
        
    }
    
    // ..
    
    /**
     * Include admin assets
     */
    
    public function assets() {
        
        // Custom CSS
        wp_register_style( 'tw-style', TW_PLUGIN_URL . '/assets/css/tweet-wheel.css' );
        wp_enqueue_style( 'tw-style' );
        
        // ...
        
        // WP Core
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        
        // Other JS Libraries
        wp_register_script( 'autosize', TW_PLUGIN_URL . '/assets/js/autosize.js' );
        wp_enqueue_script( 'autosize' );
        
        // Custom JS
        wp_register_script( 'tw-js', TW_PLUGIN_URL . '/assets/js/tweet-wheel.js' );
        wp_enqueue_script( 'tw-js' );
        
    }
    
    // ...
    
    /**
     * Include all dependencies for AJAX needs
     *
     * @type function
     * @date 28/01/2015
     * @since 0.0.1
     *
     * @param N/A
     * @return N/A
     **/
    
	public function ajax_includes() {
        
		include_once( 'includes/class-tw-ajax.php' );
        
	}
    
    // ...
    
    /**
     * Initialize the plugin! Woop!
     *
     * @type function
     * @date 28/01/2015
     * @since 0.0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function init() {
        
        // Another gift.. Hook before plugin init
        do_action( 'before_tweetwheel_init' );
        
        // Load Twitter class instance
        $this->twitter = $this->twitter();
        
        // Load Queue class instance
        $this->queue = $this->queue();
        
        // Hook right after init
        do_action( 'tweetwheel_init' );
        
    }
    
    /*
    
    
    
    */

    public function redirect() {
        if (get_option('tw_activation_redirect', false)) {
            delete_option('tw_activation_redirect');
            if(!isset($_GET['activate-multi']))
            {
                wp_redirect(admin_url('/admin.php?page=tweetwheel'));
            }
        }
    }
    
    /*
    
    
    
    */
    
    // ... Helpers ...
    
	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
    
    /*
    
    
    
    */
    
    // ... Class Instances ...
    
    /**
     * Get TW Twitter Instance
     */
    public function twitter() {
        return TW_Twitter::instance();
    }
    
    /**
     * Get TW Tweet Instance
     */
    public function tweet() {
        return TW_Tweet::instance();
    }
    
    /**
     * Get TW Queue Instance
     */
    public function queue() {
        return TW_Queue::instance();
    }
    
}

/**
 * Returns the main instance of TW
 *
 * @since  0.0.1
 * @return TweetWheel
 */
function TW() {
	return TweetWheel::instance();
}
TW();
    
endif;