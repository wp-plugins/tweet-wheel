<?php

/**
 * Sanitize filters
 *
 * @author Matt Gates <http://mgates.me>
 * @package WordPress
 */


if ( ! class_exists( 'SF_Sanitize' ) ) {

	class SF_Sanitize
	{


		/**
		 * Hooks
		 */
		function __construct()
		{
			add_filter( 'geczy_sanitize_color', 'sanitize_text_field' );
			add_filter( 'geczy_sanitize_text', 'sanitize_text_field' );
			add_filter( 'geczy_sanitize_number', array( 'SF_Sanitize', 'sanitize_number_field' ) );
			add_filter( 'geczy_sanitize_textarea', array( 'SF_Sanitize', 'sanitize_textarea' ) );
			add_filter( 'geczy_sanitize_wysiwyg', array( 'SF_Sanitize', 'sanitize_wysiwyg' ) );
			add_filter( 'geczy_sanitize_checkbox', array( 'SF_Sanitize', 'sanitize_checkbox' ), 10, 2 );
			add_filter( 'geczy_sanitize_radio', array( 'SF_Sanitize', 'sanitize_enum' ), 10, 2 );
			add_filter( 'geczy_sanitize_select', array( 'SF_Sanitize', 'sanitize_enum' ), 10, 2 );
			add_filter( 'geczy_sanitize_single_select_page', array( 'SF_Sanitize', 'sanitize_select_pages' ), 10, 2 );
            add_filter( 'geczy_sanitize_times', array( 'SF_Sanitize', 'sanitize_schedule_times' ), 10, 2 );
			add_filter( 'geczy_sanitize_post_type', array( 'SF_Sanitize', 'sanitize_post_type' ), 10, 1 );
            
		}


		/**
		 * Numeric sanitization
		 *
		 * @param int     $input
		 * @return int
		 */
		public function sanitize_number_field( $input )
		{
			$output = is_numeric( $input ) ? (float) $input : false;
			return $input;
		}


		/**
		 * Textarea sanitization
		 *
		 * @param string  $input
		 * @return string
		 */
		public function sanitize_textarea( $input )
		{
			global $allowedposttags;
			$output = wp_kses( $input, $allowedposttags );
			return $output;
		}


		/**
		 * WYSIWYG sanitization
		 *
		 * @param string  $input
		 * @return string
		 */
		public function sanitize_wysiwyg( $input )
		{
			return $input;
		}


		/**
		 * Checkbox sanitization
		 *
		 * @param int     $input
		 * @param unknown $option
		 * @return int
		 */
		public function sanitize_checkbox( $input, $option )
		{
			if ( !empty( $option['multiple'] ) ) {

				$defaults = array_keys( $option['options'] );

				foreach ( $defaults as $value ) {

					if ( !is_array( $input ) ) {
						$output[$value] = 0;
					} else {
						$output[$value] = in_array( $value, $input ) ? 1 : 0;
					}

				}

				//$output = serialize( $output );
			} else {
				$output = $input ? 1 : 0;
			}

			return $output;
		}


		/**
		 * Array sanitization
		 *
		 * @param unknown $input
		 * @param array   $option
		 * @return bool
		 */
		public function sanitize_enum( $input, $option )
		{
			$output = $input;

			if ( is_array( $input ) ) {
				foreach ( $input as $value ) {
					if ( !SF_Sanitize::sanitize_enum( $value, $option ) ) {
						$output = false;
						break;
					}
				}
				$output = $output ? serialize( $output ) : $output;
			} else {
				$output = array_key_exists( $input, $option['options'] ) ? $input : false;
			}

			return $output;
		}


		/**
		 * Select box for pages sanitize
		 *
		 * @param int     $input
		 * @param int     $option
		 * @return int
		 */
		public function sanitize_select_pages( $input, $option )
		{
			$output = get_page( $input ) ? (int) $input : 0;
			return $output;
		}
        
        // ...
        
        public function sanitize_schedule_times( $input, $option ) { 
            
            $timestamps = array();
            $output = array();
            
            if ( is_array( $input ) ) :
				
                foreach( $input as $t ) :
            
                    // turn into seconds
                    $timestamps[] = ( $t['hour']*3600 ) + ( $t['minute']*60 );

                endforeach;
            
                $unique = array_unique( $timestamps );
            
                if( ! empty( $unique ) ) :
            
                    foreach( $unique as $u ) :
            
                        $hours = floor($u / 3600);
                        $minutes = floor(($u / 60) % 60);

                        $output[] = array( 'hour' => $hours, 'minute' => $minutes );

                    endforeach;
                
                endif;

            endif;
            
            return $output;
        
        }
	
		// ...
	
	    public function sanitize_post_type( $input ) { 
		
	        return $input;
    
	    }
		
	}
	

}
