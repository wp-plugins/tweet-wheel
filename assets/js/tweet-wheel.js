$ = jQuery.noConflict();

$('#tweet-preview').focus(function(e){
    e.preventDefault();
})

maxCharacters = 140;

$(document).on('load keyup','#tweet_text-cmb-field-0', function(e) {
    
    var tweet_template = $(this).val();
    
    //list of functional/control keys that you want to allow always
    var keys = [8, 9, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 144, 145];
    if( $.inArray(e.keyCode, keys) == -1) {
        if (checkMaxLength (tweet_template, maxCharacters)) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }
    
    if( tweet_template.indexOf("{{URL}}") > -1 ) {
        maxCharacters = 147;
    } else {
        maxCharacters = 140;
    }
    
    var count = $('#count');
    var characters = $('#tweet-preview').val().length;

    count.text(maxCharacters - characters);

    tweet_template = tweet_template.replace("{{URL}}", post_url );
    tweet_template = tweet_template.replace("{{TITLE}}", post_title );
    $('#tweet-preview').val( tweet_template );
    $( '.autoresize' ).autosize();   
    
} );


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
        $.post( ajaxurl, { action: 'save_queue', queue_order : data }, function(response){
            if( response == 'ok' ) {
                $('#save-the-queue').removeClass('saving').addClass('disabled').text('All Saved');
            } else {
                alert( "Couldn't save changes. Not sure why... Restored original queue!" );
            }
        } ); 
    });

    $( ".post-header .title" ).click(function() {
        $(this).parent().parent().find( ".post-content" ).toggle();
    });
    
    $('#empty-queue-alert-hide').click(function(e){
        e.preventDefault();
        $('.tw-empty-queue-alert').slideUp();
        $.post( ajaxurl, { action: 'empty_queue_alert' } ); 
    });
});


$(function() {

    $('#change-queue-status').click(function(e){
        e.preventDefault();
        
        $('#change-queue-status').addClass('disabled').text('Working...');
        
        $.post( ajaxurl, { action: 'change_queue_status' }, function(response) {
            
            $('#change-queue-status').removeClass('disabled')
            
            if( response == 0 ) {
                $('#change-queue-status').text('Resume');
                $('#queue-status').text( 'Status: Paused' );
            } else if( response == 1 ) {
                $('#change-queue-status').text('Pause');
                $('#queue-status').text( 'Status: Running' );
            } else {
                $('#change-queue-status').text('Error :(');
            }
        
        } ); 
        
        
    });
    
    $('#tw-simple-view').click(function(e){
        
        e.preventDefault();
        
        $(this).toggleClass('active');
        $('#the-queue').find('> ul').toggleClass('simple');
         
    });
    
});

$(function() {
    
    $('.tweet-now').click(function(e){
       
        e.preventDefault();
        
        var el = $(this);
        
        el.text('Tweeting...');
        
        $.post( ajaxurl, { action: 'tweet', post_id : el.data('post-id') }, function( response ) {
            
            var data = $.parseJSON( response );
            
            if( data.response == "error" ) {
                
                $('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);
                
                el.text('Tweet Now');
                
                alert( 'Twitter did not accept your tweet. In most cases it\'s because it\'s a duplicate. We suggest moving the post down the queue and re-tweeting it again later.' );
                
            } else {
                
                $('#'+el.data('post-id')).css( 'background', '#00AB2B' ).slideUp();
                
            }
            
        } );
        
    });
    
} );

$(function() {
    
    $('.tw-dequeue').click(function(e){
       
        e.preventDefault();
        
        var el = $(this);
        
        el.text('Removing...');
        
        $.post( ajaxurl, { action: 'remove_from_queue', post_id : el.data('post-id') }, function( response ) {
            
            var data = $.parseJSON( response );
            
            if( data.response == "error" ) {
                
                $('#'+el.data('post-id')).animate({backgroundColor:'red'}, 300).animate({backgroundColor:'#fff'}, 300);
                
                el.text('Remove');
                
                alert( 'We couldn\'t remove your tweet... Not sure why. Try excluding it in the post edit screen.' );
                
            } else {
                
                $('#'+el.data('post-id')).css( 'background', '#00AB2B' ).slideUp();
                
            }
            
        } );
        
    });
    
} );

function checkMaxLength (text, max) {
    return (text.length >= max);
}
