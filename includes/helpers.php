<?php

/**
 * Helpers
 */

// ...

/**
 * Retrieves a single setting
 *
 * @type function
 * @date 05/04/2015
 * @since 0.4
 *
 * @param string | (optional) string
 * @return string | array
 **/

if( !function_exists('tw_get_option') ){

    function tw_get_option( $option, $key = null ){
        
        $data = get_option( $option . '_options' );
        
        if( $key != null && isset( $data[$key] ) )
            return $data[$key];
        
        return $data;
        
    }
}

// ...

/**
 * Deletes option
 *
 * @type function
 * @date 05/04/2015
 * @since 0.4
 *
 * @param string
 * @return N/A
 **/

if( !function_exists('tw_delete_settings') ){

    function tw_delete_settings( $option ){
        
        delete_option( $option . '_options' );
        
    }
}

// ...

/**
 * Updates settings in an option group
 *
 * @type function
 * @date 30/01/2015
 * @since 0.1
 *
 * @param string | anything..
 * @return false or int | string | array | ...
 **/

if( !function_exists('tw_update_settings') ){

    function tw_update_settings( $option, $new_value ){
        
        return update_option( $option . '_options', $new_value );
        
    }
}

// ...

/**
 * Finds next key in an array based on provided one
 * If given one is last one, reverses to the first key (clever, huh)
 *
 * @type function
 * @date 05/04/2015
 * @since 0.4
 *
 * @param array | string / int
 * @return string / int
 **/

function get_next_in_array($array, $key) {
    
    $keys = array_keys( $array );
    
    foreach( $keys as $k ) :
    
        if( isset( $array[$k] ) && $array[$k] == $array[$key] ) :
            
            // check if there is next one (false for the last array element)
            if( isset( $array[$key+1] ) )
                return $array[$key+1];
    
            // nothing else to be done here...
            break;
    
        endif;
    
    endforeach;
    
    // Fallback - return first element
    reset( $array );
    return $array[ key($array) ];

}

// ...

/**
 * Basically brings two string to the simplest form and compares them
 *
 * @type function
 * @date 05/04/2015
 * @since 0.4
 *
 * @param string | string
 * @return boolean
 **/

function compare_tweet_templates( $t1, $t2 ) {
     
    $t1 = sanitize_title_with_dashes( $t1 );
    $t2 = sanitize_title_with_dashes( $t2 );
    
    if( $t1 == $t2 )
        return true;
    
    return false;

}