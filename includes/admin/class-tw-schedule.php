<?php

/**
 * Main class of TW_Schedule
 *
 * @class TW_Schedule
 * @since 0.4
 */

class TW_Schedule {
    
    public static $_instance = null;
    
    private $settings;
    
    // ...
    
	/**
	 * Main TW_Schedule Instance
	 *
	 * Ensures only one instance of TW_Schedule is loaded or can be loaded.
	 *
	 * @since 0.4
	 * @static
	 * @return TW_Schedule object
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
    
    // ...
    
	/**
	 * TW_Schedule __construct
     *
	 * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return n/a
	 */
    
    public function __construct() {
        
        // Settings only for authed users
        if( ! TW()->twitter()->is_authed() )
            return;

        add_filter( 'tw_settings_options_tab-schedule', array( $this, 'times_options' ) );
        add_action( 'tw_settings_options_type_times', array( $this, 'times_settings' ) );
        add_filter( 'tw_settings_tab_options-schedule', array( $this, 'times_value' ), 10, 2 );
        add_action( 'tw_settings_after_form_tab-schedule', array( $this, 'time_template_js' ) );
        
        $this->settings = tw_get_option( 'tw_settings' );

    }
                   
    // ...
    
    /**
     * Hooks into WP Geczy settings framework to add custom tweeting time options
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param array
	 * @return array
     */
    
    public function times_options( $options ) {
        
        $options[] = array(
            'name' => __( 'Times', 'tweet-wheel' ),
            'label' => false,
            'id' => 'times',
            'type' => 'times'
        );
        
        return $options;

    }
          
    // ...
    
    /**
     * Hooks into added custom tweeting time option and displays field
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param array | object
	 * @return array
     */
    
    public function times_value( $tabs, $post ) {

        if( ! isset( $tabs['schedule'] ) )
            return $tabs;
        
        if( ! isset( $post['times'] ) )
            $post['times'] = array();

        $tabs['schedule'][] = array(
            'name' => __( 'Times', 'tweet-wheel' ),
            'label' => false,
            'id' => 'times',
            'type' => 'times',
            'options' => $post['times']
        );
        
        return $tabs;
        
    }
    
    // ...
    
    /**
     * An actual tweet times field with form fields
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return n/a
     */
    
    public function times_settings() {
		
        ?>

        <div class="times-wrapper">
            
            <h4><?php _e( 'Tweeting Times', 'tweet-wheel' ); ?></h4>

            <a href="#" id="add-new-time" class="button">Add a Tweeting Time</a>

            <ul class="times">
            
                <?php
        
                    if( $this->has_times() ) :
        
                        $k = 0;
        
                        foreach( $this->get_times() as $time ) :
        
                            $hours = '';
                            for($i = 0; $i < 24; $hours .= '<option value="' . $i . '" ' . selected( $time['hour'], $i, false ) . '>' . ( strlen( $i ) < 2 ? '0' . $i : $i ) . '</option>', $i++);

                            $minutes = '';
                            for($i = 0; $i < 60; $minutes .= '<option value="' . $i . '"' . selected( $time['minute'], $i, false ) . '>' . ( strlen( $i ) < 2 ? '0' . $i : $i ) . '</option>', $i++);

                            echo '<li data-index="'.$k.'">'.sprintf( $this->time_template(), $k, $hours, $k, $minutes ).'</li>';
        
                            $k++;
        
                        endforeach;
        
                    endif;
        
                ?>
                
            </ul>

        </div>

        <?php

    }
    
    // ...
    
    /**
     * Displays hidden new time template on relevant settings page (Schedule tab)
     * To be used by JS for adding new fields dynamically
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return n/a
     */
    
    public function time_template_js() {
     
        ?>

        <div class="time-template" style="display:none">

            <?php
        
                $hours = '';
                for($i = 0; $i < 24; $hours .= '<option value="' . $i . '">' . ( strlen( $i ) < 2 ? '0' . $i : $i ) . '</option>', $i++);
        
                $minutes = '';
                for($i = 0; $i < 60; $minutes .= '<option value="' . $i . '">' . ( strlen( $i ) < 2 ? '0' . $i : $i ) . '</option>', $i++);
        
                printf( $this->time_template(), 0, $hours, 0, $minutes ); ?>
            
        </div>

        <?php
        
    }
    
    // ..
    
    /**
     * Global new time template for Schedule settings.
     * Used to populate already saved times on page reload
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return string
     */
    
    public function time_template() {
     
        $html = '<span class="remove-time dashicons dashicons-no-alt"></span><select name="tw_settings_options[times][%d][hour]">%s</select>
        <select name="tw_settings_options[times][%d][minute]">%s</select>';
        
        return $html;
        
    }
    
    // ...
    
    /**
     * Check if there are any tweeting times set
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return boolean
     */
    
    public function has_times() {

        if( ! isset( $this->settings['times'] ) || empty( $this->settings['times'] ) )
            return false;
        
        return true;
        
    }
    
    // ...
    
    /**
     * Retrieve tweeting times in unchanged form (human readable)
     * Sorted by time in ASC order
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return array
     */
    
    public function get_times() {
     
        if( ! isset( $this->settings['times'] ) || empty( $this->settings['times'] ) )
            return false;
        
        return $this->sort_times( $this->settings['times'] );
        
    }
    
    // ...
    
    /**
     * Retrieve tweeting times as timestamps
     * Sorted by value in ASC order
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return array
     */
    
    public function get_times_in_seconds() {
        
        if( ! isset( $this->settings['times'] ) || empty( $this->settings['times'] ) )
            return false;
        
        $times = $this->settings['times'];
     
        $timestamps = array();
        
        foreach( $times as $t ) :
            
            // turn into seconds
            $timestamps[] = ( $t['hour']*3600 ) + ( $t['minute']*60 );
        
        endforeach;
        
        return $timestamps;
        
    }
    
    // ...
    
    /**
     * Sorts given array of tweeting times in ASC order
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return array
     */
    
    public function sort_times( $arr ) {
     
        $timestamps = array();
        
        foreach( $arr as $t ) :
            
            // turn into seconds
            $timestamps[] = ( $t['hour']*3600 ) + ( $t['minute']*60 );
        
        endforeach;
        
        /* Sort by air_time (descending) */
        sort($timestamps);
        
        $times = array();
        
        foreach( $timestamps as $ts ) :
        
            $hours = floor($ts / 3600);
            $minutes = floor(($ts / 60) % 60);
        
            $times[] = array( 'hour' => $hours, 'minute' => $minutes );
        
        endforeach;
        
        return $times;
        
    }
    
    // ...
    
    /**
     * Finds closest tweeting time in the past from now
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return int | false
     */
    
    public function get_closest_time() {
     
        $times = $this->get_times_in_seconds();
        $dates = array();
        
        // turn into full valid timestamp including date
        foreach( $times as $t ) :

            $dates[] = strtotime( 'today midnight', current_time( 'timestamp' ) ) + $t;
        
        endforeach;
        
        // find closest
        $a = array(); 
        $return = array(); 
        
        foreach( $dates as $key => $val ) : 
        
            $a[$key] = abs( $val - current_time( 'timestamp' ) ); 
        
        endforeach;
        
        asort($a); 
        
        foreach( $a as $key => $val ) :
        
            $return[$key] = $dates[$key]; 
        
        endforeach;
        
        // At this point we have sorted array with the closest time at the top
        // just need to return first one that is in the past

        foreach( $return as $time ) :

            if( $time <= current_time( 'timestamp' ) )
                return $time;

        endforeach;
        
        return false;

    }
    
    // ...
    
    /**
     * Retrieves set days for tweeting
     *
     * @type function
     * @date 05/04/2015
	 * @since 0.4
     *
     * @param n/a
	 * @return array | false
     */
    
    public function get_days() {
     
        if( ! isset( $this->settings['days'] ) || empty( $this->settings['days'] ) )
            return false;
        
        return $this->settings['days'];
        
    }
    
}

/**
 * Returns the main instance of TW_Schedule
 *
 * @since  0.4
 * @return TW_Schedule
 */

function TW_Schedule() {
	return TW_Schedule::instance();
}