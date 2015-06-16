<?php

/**
 * Main class TW_Menus
 *
 * The idea is to be the superior class handling menus.
 * I wanted it to be extensible by hooks. Maybe it will come useful later.
 *
 * @class TW_Menus
 */

class TW_Menus {
    
    private $menus = array();
    
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
        
        $this->menus[] = array(
            'page_title' => 'About',
            'menu_title' => 'About',
            'capability' => 'administrator',
            'menu_slug' => 'tweetwheel',
            'auth_only' => false
        );
        
        add_action( 'admin_menu', array( $this, 'menu' ), 10 );
        add_action( 'admin_menu', array( $this, 'submenu' ), 10 );
        add_filter( 'tw_load_admin_menu', array( $this, 'submenu_pro' ), 9999, 1 );
        
    }
    
    // ...
    
    /**
     * Adds main parent menu tab Tweet Wheel
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function menu() {
        
        add_menu_page( 
            __( 'Tweet Wheel', 'tweetwheel' ), 
            __( 'Tweet Wheel', 'tweetwheel' ), 
            'administrator', 
            'tweetwheel', 
            'TW_Dashboard::page', TW_PLUGIN_URL . '/assets/images/tweet-wheel-menu-icon.png'
        );

        
    }
    
    // ...
    
    /**
     * Add submenus. Here is where other classes add their own tabs.
     *
     * @type function
     * @date 28/01/2015
     * @since 0.1
     *
     * @param N/A
     * @return N/A
     **/
    
    public function submenu() {
        
        $this->menus = apply_filters( 'tw_load_admin_menu', $this->menus );

        foreach( $this->menus as $menu ) :
            
            $menu = wp_parse_args( $menu, array(
                'parent_slug' => 'tweetwheel',
                'page_title' => 'Menu...',
                'menu_title' => 'Menu...',
                'capability' => 'administrator',
                'menu_slug' => 'menu_',
                'function' => '__return_false',
                'auth_only' => false
            ) );
            
            add_submenu_page( $menu['parent_slug'], __( $menu['page_title'], 'tweetwheel' ), __( $menu['menu_title'], 'tweetwheel' ), $menu['capability'], $menu['menu_slug'], $menu['function'] );
            
        endforeach;
        
    }
    
    public function submenu_pro( $menus ) {
        
        $menus[] = array(
            'parent_slug' => 'tweetwheel',
            'page_title' => 'Upgrade to Pro',
            'menu_title' => 'Upgrade to Pro',
            'capability' => 'administrator',
            'menu_slug' => 'tw_upgrade_to_pro',
            'function' => 'TW_Dashboard::upgrade',
            'auth_only' => false
        );
        
        return $menus;
        
    }
    
}

// Initiate
new TW_Menus;