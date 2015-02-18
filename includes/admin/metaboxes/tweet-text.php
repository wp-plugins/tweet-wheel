<?php
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function tw_twitter_text_metabox( $meta_boxes ) {

	// Example of all available fields

	$fields = array(

        array(
          'id' => 'post_exclude', 'name' => 'Exclude this post from the queue?', 'type' => 'checkbox'
        ),

		array( 'id' => 'tweet_text', 'name' => 'Custom Tweet Text', 'type' => 'textarea', 'desc' => 'If you want this post to have a custom tweet text, write it down here. Use {{URL}} for post\'s URL and {{TITLE}} for post\'s title.' ),
        
        array( 'id' => 'tweet_preview', 'name' => 'Tweet Preview', 'type' => 'custom', 'desc' => 'Preview of this post tweet', 'default' => TW()->tweet()->metabox_field_preview() )
            
	);


    // if new post and should be excluded by default
    if(
        wpsf_get_setting( 'tw_settings', 'global', 'queue_new_post' ) == 1 &&
        get_post_meta( $_GET['post'], 'post_exclude' ) == ''    
    ) :
        
        $fields[0]['id'] = '_post_exclude';
        $fields[0]['name'] = $fields[0]['name'] . ' (Excluded by global settings)';
        $fields[0]['default'] = 1;
        $fields[0]['disabled'] = true;
        
        $fields[] = array(
            'id' => 'post_exclude', 'name' => 'mama', 'type' => 'hidden', 'default' => 1
        );
        
    endif;
    

	$meta_boxes['tw_post'] = array(
		'title' => 'Custom Tweet Settings',
		'pages' => 'post',
		'fields' => $fields
	);

	return $meta_boxes;

}
add_filter( 'cmb_meta_boxes', 'tw_twitter_text_metabox' );
