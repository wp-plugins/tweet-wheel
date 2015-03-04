$ = jQuery.noConflict();

/**
 * Custom Tweet Metabox Counter and Parsing
 * @since 0.1
 * @updated 21.02.2015
 */

if( pagenow == 'post' ) {

    // Count characters and display on page load
    $(window).load(function(){
    
        $('#count').text( tw_character_counter( $('#tweet_text-cmb-field-0').val() ) );
    
    });

    // Handle custom tweet text box input and update counter
    $(document).on('keyup keydown','#tweet_text-cmb-field-0', function(e) {
    
        // ...
    
        $('#count').text( tw_character_counter( $(this).val() ) );
    
        // ...
    
        $(this).val( $(this).val().replace("{{URL}}", post_url ) );
        $(this).val( $(this).val().replace("{{TITLE}}", post_title ) );
    
        // ...
    
        $('#tweet-preview').text( $(this).val() ); 

    } );

}

/*



*/

$(function() {
    $( "#the-queue ul" ).sortable({
        handle : '.drag-handler',
        update : function() {
            $('#save-the-queue').removeClass('disabled').text('Save Changes');
        }
    });
    
    $('#save-the-queue').click(function(e){
        e.preventDefault();
        $('#save-the-queue').addClass('saving disabled').text('Saving...');
        var data = $('#the-queue ul').sortable('toArray');
        $.post( 
            ajaxurl, 
            { 
                action: 'save_queue', 
                twnonce: TWAJAX.twNonce,
                queue_order : data
            }, 
            function(response){
                var data = $.parseJSON(response);
                if( data.response == 'ok' ) {
                    $('#save-the-queue').removeClass('saving').addClass('disabled').text('All Saved');
                } else {
                    alert( "Couldn't save changes. Not sure why... Restored original queue!" );
                }
            }
        ); 
    });

    $( ".post-header .title" ).click(function() {
        $(this).parent().parent().find( ".post-content" ).toggle();
    });
    
    $('#empty-queue-alert-hide').click(function(e){
        e.preventDefault();
        $('.tw-empty-queue-alert').slideUp();
        $.post( 
            ajaxurl, 
            { 
                action: 'empty_queue_alert', 
                twnonce: TWAJAX.twNonce 
            }
        ); 
    });
    
    // ...

    $('#change-queue-status').click(function(e){
        e.preventDefault();
        
        $('#change-queue-status').addClass('disabled').text('Working...');
        
        $.post( 
            ajaxurl, 
            { 
                action: 'change_queue_status',
                twnonce: TWAJAX.twNonce
            }, 
            function(response) {
            
                var data = $.parseJSON(response);
            
                $('#change-queue-status').removeClass('disabled')
            
                if( data.response == 'paused' ) {
                    $('#change-queue-status').text('Resume');
                    $('#queue-status').text( 'Status: Paused' );
                } else if( data.response == 'running' ) {
                    $('#change-queue-status').text('Pause');
                    $('#queue-status').text( 'Status: Running' );
                } else {
                    $('#change-queue-status').text('Error :(');
                }
        
            } 
        ); 
        
        
    });
    
    $('#tw-simple-view').click(function(e){
        
        e.preventDefault();
        
        $(this).toggleClass('active');
        $('#the-queue').find('> ul').toggleClass('simple');
         
    });
    
    /**
     * Tweet Now available on the Queue screen
     */
    
    $('.tweet-now').click(function(e){
       
        e.preventDefault();
        
        var el = $(this);
        
        el.text('Tweeting...');
        
        $.post( 
            ajaxurl, 
            { 
                action: 'tweet', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {

                var data = $.parseJSON( response );

                if( data.response == "error" ) {
                
                    $('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);
                
                    el.text('Tweet Now');
                
                    alert( 'Twitter did not accept your tweet. In most cases it\'s because it\'s a duplicate. We suggest moving the post down the queue and re-tweeting it again later.' );
                
                } else {
                
                    $('#'+el.data('post-id')).css( 'background', '#00AB2B' ).slideUp().remove();
                
                }
            
            } 
        );
        
    });
    
    // ...
    
    $(document).on('click','.tw-dequeue-post',function(e){
       
        e.preventDefault();
        
        var el = $(this);
        
        el.text('Dequeuing...');
        
        $.post( 
            ajaxurl, 
            { 
                action: 'remove_from_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {
            
                var data = $.parseJSON( response );
            
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
    
    $(document).on('click','.tw-queue-post',function(e){
       
        e.preventDefault();
        
        var el = $(this);
        
        el.text('Queuing...');
        
        $.post( 
            ajaxurl, 
            { 
                action: 'add_to_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            }, 
            function( response ) {
            
                var data = $.parseJSON( response );
            
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

    $('.tw-dequeue').click(function(e){
       
        e.preventDefault();
        
        var el = $(this);
        
        el.text('Removing...');
        
        $.post( 
            ajaxurl, 
            { 
                action: 'remove_from_queue', 
                post_id : el.data('post-id'),
                twnonce: TWAJAX.twNonce
            },
            function( response ) {
            
                var data = $.parseJSON( response );
            
                if( data.response == "error" ) {
                
                    $('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);
                
                    el.text('Remove');
                
                    alert( 'We couldn\'t remove your tweet... Not sure why. Try excluding it in the post edit screen.' );
                
                } else {
                
                    $('#'+el.data('post-id')).css( 'background', '#00AB2B' ).slideUp().remove();
                
                }
            
            } 
        );
        
    });
    
} );

function tw_character_counter( raw ) {
    
    // Max characters accepted for a single tweet
    maxCharacters = 140;
    
    // Load custom tweet text to a variable
    var tweet_template = raw;
    
    // ...

    tweet_template = tweet_template.replace("{{URL}}", post_url );
    tweet_template = tweet_template.replace("{{TITLE}}", post_title );
    
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
