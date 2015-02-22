<?php

/**
 * Helpers
 * 
 * This files consists of functions helping with managing settings framework.
 * Anything to do with options is here...
 */

// ...

/**
 * Retrieves an option group
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return array
 **/

if( !function_exists('wpsf_get_option_group') ){
    /**
     * Converts the settings file name to option group id
     *
     * @param string settings file
     * @return string option group id
     */
    function wpsf_get_option_group( $settings_file ){
        $option_group = preg_replace("/[^a-z0-9]+/i", "", basename( $settings_file, '.php' ));
        return $option_group;
    }
}

// ...

/**
 * Retrieves settings from an option group
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return array
 **/

if( !function_exists('wpsf_get_settings') ){
    /**
     * Get the settings from a settings file/option group
     *
     * @param string option group id
     * @return array settings
     */
    function wpsf_get_settings( $option_group ){
        return get_option( $option_group .'_settings' );
    }
}

// ...

/**
 * Retrieves a single setting
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return string | int | array | ...
 **/

if( !function_exists('wpsf_get_setting') ){
    /**
     * Get a setting from an option group
     *
     * @param string option group id
     * @param string section id
     * @param string field id
     * @return mixed setting or false if no setting exists
     */
    function wpsf_get_setting( $option_group, $section_id, $field_id ){
        $options = get_option( $option_group .'_settings' );
        if(isset($options[$option_group .'_'. $section_id .'_'. $field_id])){
            return $options[$option_group .'_'. $section_id .'_'. $field_id];
        }
        return false;
    }
}

// ...

/**
 * Deletes settings from an option group
 *
 * @type function
 * @date 28/01/2015
 * @since 0.1
 *
 * @param N/A
 * @return N/A
 **/

if( !function_exists('wpsf_delete_settings') ){
    /**
     * Delete all the saved settings from a settings file/option group
     *
     * @param string option group id
     */
    function wpsf_delete_settings( $option_group ){
        delete_option( $option_group .'_settings' );
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
 * @param N/A
 * @return false or int | string | array | ...
 **/

if( !function_exists('wpsf_update_settings') ){
    /**
     * Update the settings from a settings file/option group
     *
     * @param string option group id
     * @return array new value
     */
    function wpsf_update_settings( $option_group, $new_value ){
        return update_option( $option_group .'_settings', $new_value );
    }
}