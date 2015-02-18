<?php

class TW_Menus {
    
    private $menus = array();
    
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
        
    }
    
    /**
     * Add main Tweet Wheel plugin menu tab
     */
    
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
     * Add submenus
     */
    
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
    
}

new TW_Menus;