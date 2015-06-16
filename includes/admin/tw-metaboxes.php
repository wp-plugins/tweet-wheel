<?php

/**
 * Creates all metaboxes used within the plugin
 *
 * @since 0.4
 * @date 14/03/2015
 */

// ...

/**
 * Fire our meta box setup function on the post editor screen. 
 *
 * @since 0.4
 */

add_action( 'load-post.php', 'tw_post_meta_boxes_setup' );
add_action( 'load-post-new.php', 'tw_post_meta_boxes_setup' );

// ...

/**
 * Meta box setup function. 
 *
 * @since 0.4
 */

function tw_post_meta_boxes_setup() {

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'tw_add_tweet_templates_meta' );
  
  /* Save post meta on the 'save_post' hook. */
  add_action( 'save_post', 'tw_save_tweet_templates_meta', 10, 2 );
  add_action( 'save_post', 'tw_save_tweet_settings_meta', 10, 2 );
  
}

// ...

/**
 * Add all metaboxes
 *
 * @since 0.4
 * @updated 0.5
 */

function tw_add_tweet_templates_meta() {
	
	$post_types = tw_get_option( 'tw_settings', 'post_type' );
	
	if( empty( $post_types ) || ! is_array( $post_types ) )
		return;
	
	foreach( $post_types as $post_type ) :

		add_meta_box(
			'tw-tweet-settings',
			esc_html__( 'Tweet Settings', 'tweet-wheel' ),
			'tw_tweet_settings_meta_box',
			$post_type,
			'normal',
			'default'
		);

		add_meta_box(
			'tw-tweet-templates',
			esc_html__( 'Tweet Templates', 'tweet-wheel' ),
			'tw_tweet_templates_meta_box',
			$post_type,
			'normal',
			'default'
		);

	endforeach;
    
}

// ...

/*

Particular metaboxes down below

*/

// ...

/***************************************
 * Tweet Wheel Settings Metabox
 **************************************/

function tw_tweet_settings_meta_box( $object, $box ) {
    
    wp_nonce_field( basename( __FILE__ ), 'tweet_settings_nonce' ); 
    
    $tweet_order = get_post_meta( $object->ID, 'tw_templates_order', true);
    $tweet_order = $tweet_order == '' ? 'order' : $tweet_order;

    ?>

    <div class="tw-metabox tw-tweet-settings">
        
        <p>
            <span class="section-title">Post exclusion</span>
            <span class="section-note">If you don't want this post to be queued, you can exclude it permanently by checking the box below. If a post is currently in the queue, it will be dequeued.</span>
            
            <?php
    
                // if new post and should be excluded by default
                if( 
                    tw_get_option( 'tw_settings', 'queue_new_post' ) == 1 &&
                    get_post_meta( $_GET['post'], 'tw_post_exclude' ) == ''
                ) :
    
                ?>
                
                    <input type="checkbox" name="_tw_post_excluded" id="_tw_post_excluded" checked disabled>
                    <input type="hidden" name="tw_post_excluded" id="tw_post_excluded" value="1" checked>
            
                    <label for="_tw_post_excluded">Exclude this post from the queue <span style="color:red;font-size:11px;font-style:italic;">(excluded by default)</span></label>
            
                <?php   
    
                else :
    
                    $post_excluded = get_post_meta( $object->ID, 'tw_post_excluded', true);  
                    $post_excluded = $post_excluded == '' ? 0 : $post_excluded;
    
                    ?>
            
                    <input type="checkbox" name="tw_post_excluded" id="tw_post_excluded" value="1" <?php checked( $post_excluded, 1 ) ?>>
                    <label for="tw_post_excluded">Exclude this post from the queue</label>
            
                    <?php

                endif;

            ?>

        </p>
        
        <hr/>
        
        <p>
            <span class="section-title">Post tweeting</span>
            <span class="section-note">Ignore if you are using a default tweet template or a single custom one. Otherwise, please choose whether you would want your templates to be used in the order or randomly picked.</span>
            <input type="radio" name="tw_templates_order" id="tw_templates_order" value="order" <?php checked( $tweet_order, 'order' ) ?>>
            <label for="tw_templates_order">Follow the order</label><br/>
            <input type="radio" name="tw_templates_order" id="tw_templates_order_random" value="random" <?php checked( $tweet_order, 'random' ) ?>>
            <label for="tw_templates_order_random">Randomise selection</label>
        </p>
        
    </div>
    
<?php }

// ...

/* Save the meta box's post metadata. */
function tw_save_tweet_settings_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['tweet_settings_nonce'] ) || !wp_verify_nonce( $_POST['tweet_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $post_excluded = $_POST['tw_post_excluded'];
    $tweet_order = $_POST['tw_templates_order'];

    update_post_meta( $post_id, 'tw_post_excluded', $post_excluded );
    update_post_meta( $post_id, 'tw_templates_order', $tweet_order );
    
}


// ...

/***************************************
 * Tweet Templates Metabox
 **************************************/

function tw_tweet_template_default() {
    
    return apply_filters( 'tw_tweet_template_default', '<div class="tweet-template-item"><span class="tw-remove-tweet-template dashicons dashicons-no control" title="Delete this template"></span><div><textarea class="widefat tweet-template-textarea" name="tw_post_templates[%d]" placeholder="Enter your custom tweet text" required>%s</textarea><span class="counter">%d</span></div></div>' ); 
    
}

function tw_tweet_templates_meta_box( $object, $box ) { 
    
    wp_nonce_field( basename( __FILE__ ), 'tweet_templates_nonce' ); 
    
    $tweet_templates = get_post_meta( $object->ID, 'tw_post_templates', true );
    
    $template = tw_tweet_template_default();
    
    ?>

    <div class="tw-metabox tw-tweet-templates">
        <a href="#add-tweet-template" id="add-tweet-template" class="button">
            Add a Tweet Template
        </a>
        <a href="#how-to" class="tw-template-learn-more">Learn more<span class="dashicons dashicons-arrow-down"></span></a>
        
        <div class="tw-template-learn-more-content">
            <p>Create as many tweet templates as you like by clicking "Add a Tweet Template" button above. Below you can find tags that you can use within tweet templates.</p>
            <ul>
                <li>
                    <strong>{{URL}}</strong> - (mandatory) displays link to this post
                </li>
                <li>
                    <strong>{{TITLE}}</strong> - (optional) display this post title
                </li>
            </ul>
        </div>
        <?php

        /**
         * Backward compatibility for users of 0.3
         * We changed handling of metaboxes, but we don't want them to lose
        * their custom tweet texts on the plugin update.
        */
        
        if( '' == $tweet_templates ) :
            
            $tweet_text = get_post_meta( $object->ID, 'tweet_text', true );
        
            if( $tweet_text != '' )
                echo sprintf( $template, '', $tweet_text, tw_character_counter( $tweet_text ) );
        
        endif;
        
        // ... now load any others
        
        if( '' != $tweet_templates ) :
    
            $j = 0;
        
            foreach( $tweet_templates as $t ) :

                echo sprintf( $template, $j, $t, tw_character_counter( $t ) );
    
                $j++;
            
            endforeach;
        
        endif;
            
        ?>
        
    </div>
    
<?php }

// ...

/* Save the meta box's post metadata. */
function tw_save_tweet_templates_meta( $post_id, $post ) {

    /* Verify the nonce before proceeding. */
    if ( !isset( $_POST['tweet_templates_nonce'] ) || !wp_verify_nonce( $_POST['tweet_templates_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    /* Get the posted data and sanitize it for use as an HTML class. */
    $new_meta_value = $_POST['tw_post_templates'];
    
    $sorted = array();
    
    // Reset keys
    if( ! empty( $new_meta_value ) ) : 

        foreach( $new_meta_value as $m ):

            $sorted[] = $m;

        endforeach;
    
    endif;
    
    /* Get the meta key. */
    $meta_key = 'tw_post_templates';

    /* Get the meta value of the custom field key. */
    $meta_value = get_post_meta( $post_id, $meta_key, true );

    /* If a new meta value was added and there was no previous value, add it. */
    if ( $new_meta_value && '' == $meta_value )
        add_post_meta( $post_id, $meta_key, $new_meta_value, true );

    /* If the new meta value does not match the old value, update it. */
    elseif ( $new_meta_value && $new_meta_value != $meta_value )
        update_post_meta( $post_id, $meta_key, $new_meta_value );

    /* If there is no new meta value but an old value exists, delete it. */
    elseif ( '' == $new_meta_value && $meta_value )
        delete_post_meta( $post_id, $meta_key, $meta_value );

    // Remove old template post meta - backward compatiblity to <= 0.3.2
    delete_post_meta( $post_id, 'tweet_text' );

}

// ...

// ...

/**
 * Tweet Card - Checkbox in the Featured Image metabox
 **/

function tw_add_tweet_image( $content, $post_id ) {
    
    $populate = get_post_meta( $post_id, 'tweet_image', true );
    
    $content .= '<div class="upgrade-frame"><h4>Upgrade To Pro</h4><label><input readonly disabled type="checkbox"  value="1" ' . checked( $populate, 1, false ) . '> ' . __( 'Use as a tweet\'s image', 'tweetwheel' ) . '</label><span style="margin-top:5px;" class="section-note">Please note twitter requires a tweet image to be at least 280px by 150px in order for it to be used as a photo card.</span><a href="http://nrdd.co/upgrade_to_twp" target="_blank" class="upgrade-button">Upgrade Now</a></div>';
    
    return $content;
    
}

add_filter( 'admin_post_thumbnail_html', 'tw_add_tweet_image', 10, 2 );

