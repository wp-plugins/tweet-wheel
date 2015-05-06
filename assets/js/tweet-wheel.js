jQuery.noConflict();

// ...

jQuery(function(){
	
	/**
	 * Custom Tweet Metabox Counter and Parsing
	 * @since 0.1
	 * @updated 21.02.2015
	 */

    // Count characters and display on page load
    jQuery(window).load(function(){

        jQuery('textarea').autosize();

    });

    // Handle custom tweet text box input and update counter
    jQuery(document).on('keyup keydown','.tweet-template-textarea', function(e) {

        // ...
        var count = tw_character_counter( jQuery(this).val() );
        jQuery(this).parent().find('.counter').text( count );
    
        if( count > 140 ) {
            jQuery(this).parent().find('.counter').addClass( 'too-long' );   
        } else {
            jQuery(this).parent().find('.counter').removeClass( 'too-long' );
        }

        // ...

        jQuery('#tweet-preview').text( jQuery(this).val() ); 

    } );

	jQuery( "#tw-schedule label[for^=day]" ).click(function(){
		
		if( jQuery(this).find('input').is(':checked') ) {
			jQuery(this).addClass('active');
		} else {
			jQuery(this).removeClass('active');
		}
		
	});
    
    // ...
    
    jQuery('#add-new-time').click(function(e) {
        
        e.preventDefault();
       
        var template = jQuery('.time-template').html();
        var last_index = 0;
        
        if( jQuery('.times li').length != 0 ) { 
            last_index = jQuery('.times li').last().data('index');
            last_index++;
        }
        
        template = template.replace(/\[(\d+)\]/g,'['+last_index+']');
        
        console.log(template.match(/\[(\d+)\]/)[1]);
        
        jQuery('.times').append( '<li data-index="'+last_index+'">' + template + '</li>' );
        
    });
    
    // ...
    
    jQuery(document).on( 'click', '.remove-time', function(e) {
        
        e.preventDefault();
        
        jQuery(this).parent().remove();
        
    });
	
});

/*



*/

jQuery(function() {
    
    jQuery(document).ready(function() {
    
        jQuery( "#the-queue ul" ).sortable({
            handle : '.drag-handler',
            update : function() {
                jQuery('#save-the-queue').removeClass('disabled').text('Save Changes');
            }
        });
        
    });
    
    jQuery('#save-the-queue').click(function(e){
        e.preventDefault();
        
        if(jQuery(this).hasClass('disabled')) {
            return;
        }
        
        jQuery('#save-the-queue').addClass('saving disabled').text('Saving...');
        var data = jQuery('#the-queue > ul').sortable('toArray');

        jQuery.post( 
            ajaxurl, 
            { 
                action: 'save_queue', 
                twnonce: TWAJAX.twNonce,
                queue_order : data
            }, 
            function(response){
                var data = jQuery.parseJSON(response);
                if( data.response == 'ok' ) {
                    jQuery('#save-the-queue').removeClass('saving').addClass('disabled').text('All Saved');
                } else {
                    alert( "Couldn't save changes. Not sure why... Restored original queue!" );
                }
            }
        ); 
    });

    jQuery( ".post-header .title" ).click(function() {
        jQuery(this).parent().parent().find( ".post-content" ).toggle();
    });
    
    jQuery('#empty-queue-alert-hide').click(function(e){
        e.preventDefault();
        jQuery('.tw-empty-queue-alert').slideUp();
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'empty_queue_alert', 
                twnonce: TWAJAX.twNonce 
            }
        ); 
    });
    
    // ...
    
    jQuery('#wp-cron-alert-hide').click(function(e){
        e.preventDefault();
        jQuery('.tw-wp-cron-alert').slideUp();
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'wp_cron_alert', 
                twnonce: TWAJAX.twNonce 
            }
        ); 
    });
    
    // ...

    jQuery('#change-queue-status').click(function(e){
        e.preventDefault();
        
        jQuery('#change-queue-status').addClass('disabled').text('Working...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'change_queue_status',
                twnonce: TWAJAX.twNonce
            }, 
            function(response) {
            
                var data = jQuery.parseJSON(response);
            
                jQuery('#change-queue-status').removeClass('disabled')
            
                if( data.response == 'paused' ) {
                    jQuery('#change-queue-status').text('Resume');
                    jQuery('#queue-status').text( 'Status: Paused' );
                } else if( data.response == 'running' ) {
                    jQuery('#change-queue-status').text('Pause');
                    jQuery('#queue-status').text( 'Status: Running' );
                } else {
                    jQuery('#change-queue-status').text('Error :(');
                }
        
            } 
        ); 
        
        
    });
    
    jQuery('#tw-simple-view').click(function(e){
        
        e.preventDefault();
        
        jQuery(this).toggleClass('active');
        jQuery('#the-queue').find('> ul').toggleClass('simple');
         
    });
    
    /**
     * Tweet Now available on the Queue screen
     */
    
    jQuery('.tweet-now').click(function(e){
       
        e.preventDefault();
        
        var el = jQuery(this);
        
        el.text('Tweeting...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'tweet', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {

                var data = jQuery.parseJSON( response );

                if( data.response == "error" ) {
                
                    jQuery('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);
                
                    el.text('Tweet Now');
                
                    alert( 'Twitter did not accept your tweet. In most cases it\'s because it\'s a duplicate. We suggest moving the post down the queue and re-tweeting it again later.' );
                
                } else {
                
                    jQuery('#'+el.data('post-id')).css( 'background', '#00AB2B' ).slideUp().remove();
                
                }
            
            } 
        );
        
    });
    
    // ...
    
    jQuery(document).on('click','.tw-dequeue-post',function(e){
       
        e.preventDefault();
        
        var el = jQuery(this);
        
        el.text('Dequeuing...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'remove_from_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {
            
                var data = jQuery.parseJSON( response );
            
                if( data.response == "error" ) {
                
                    el.replaceWith('<a href="#" style="color:#a00" class="tw-dequeue-post" data-post-id="'+el.data('post-id')+'">Dequeue</a>');
                
                    alert( 'We couldn\'t remove your tweet... Not sure why. Try excluding it in the post edit screen.' );
                
                } else {
                
                    el.replaceWith('<a href="#" class="tw-queue-post" data-post-id="'+el.data('post-id')+'">Queue</a>');
                
                }
            
            } 
        );
        
    });
    
    // ...
    
    jQuery(document).on('click','.tw-queue-post',function(e){
       
        e.preventDefault();
        
        var el = jQuery(this);
        
        el.text('Queuing...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'add_to_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {
            
                var data = jQuery.parseJSON( response );
            
                if( data.response == "error" ) {
                
                    el.replaceWith('<a href="#" class="tw-queue-post" data-post-id="'+el.data('post-id')+'">Queue</a>');
                
                    alert( 'We couldn\'t queue your tweet... Not sure why. Try excluding it in the post edit screen.' );
                
                } else {
                
                    el.replaceWith('<a href="#" style="color:#a00" class="tw-dequeue-post" data-post-id="'+el.data('post-id')+'">Dequeue</a>');
                
                }
            
            } 
        );
        
    });

    // ...

    jQuery('.tw-dequeue').click(function(e){
       
        e.preventDefault();
        
        var el = jQuery(this);
        
        el.text('Removing...');
        
        jQuery.post( 
            ajaxurl, 
            { 
                action: 'remove_from_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            },
            function( response ) {
            
                var data = jQuery.parseJSON( response );
            
                if( data.response == "error" ) {
                
                    jQuery('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);
                
                    el.text('Remove');
                
                    alert( 'We couldn\'t remove your tweet... Not sure why. Try excluding it in the post edit screen.' );
                
                } else {
                
                    jQuery('#'+el.data('post-id')).css( 'background', '#00AB2B' ).slideUp().remove();
					
					if( jQuery('#tw-queue .the-queue-item').length == 0 ) {
						
						location.reload();
						
					}
                
                }
            
            } 
        );
        
    });
    
    // ...
    
    jQuery('.show-all-templates').click(function(e) {
        
        e.preventDefault();

        jQuery(this).parent().find('li').not(':first-child').toggleClass('visible');
        
    });

});

function tw_character_counter( raw ) {
    
    // Max characters accepted for a single tweet
    maxCharacters = 140;
    
    // Load custom tweet text to a variable
    var tweet_template = raw;
    
    // ...
    
    if( tw_template_tags.length != 0 || typeof tw_template_tags != undefined ) {
     
        jQuery.each( tw_template_tags, function(k,v) {
            
            var regex = new RegExp( '{{'+k+'}}', 'g' );
            tweet_template = tweet_template.replace( regex, v );
            
        });
        
    }
    
    /**
     * Calculate a whole string length
     */
    var current_length = 0;
    current_length = tweet_template.length;

    // ...
    
    /**
     * Amend character limit if URL is detected (22 characters per url)
     */
    
    var url_chars = 22;

    // urls will be an array of URL matches
    var urls = tweet_template.match(/(?:(?:https?|ftp):\/\/)?(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/?[^\s]*)?/g);
    
    // If urls were found, play the max character value accordingly
    if( urls != null ) {
        
        for (var i = 0, il = urls.length; i < il; i++) {
            
            // get url length difference
            var diff = url_chars - urls[i].length;
            
            // apply difference
            current_length += diff;
            
        }
        
    }
    
    // return actually tweet length
    return current_length;
    
}
