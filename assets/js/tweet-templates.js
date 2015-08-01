jQuery.noConflict();

jQuery(document).ready(function() {

    jQuery( document ).on('click','.tw-remove-tweet-template',function(e){

        e.preventDefault();

        jQuery( this ).parent().remove();

    });

    // Check how many templates are there
    var no_of_templates = jQuery('.tweet-template-item').length;

    // Adjust index
    var i = no_of_templates != null ? no_of_templates : 0;

    jQuery( '#add-tweet-template' ).click( function(e) {

        e.preventDefault();

        // Append a tweet template
        jQuery('.tw-tweet-templates').append(tweet_template);

        // Fix name indexing for jQuery validator plugin. It doesn't like array names with no specified index e.g. name[]
        jQuery('.tw-tweet-templates > div:last-of-type textarea').attr('name','tw_post_templates['+i+']');

        i++;                                                          
        // reinitialise autosize for textareas
        jQuery('.tweet-template-textarea').autosize();

    } );

    jQuery('.tw-template-learn-more').click(function(e){

        e.preventDefault();

        var el = jQuery('.tw-template-learn-more-content');

        el.slideToggle();

    });

    // Is 140 chars
    jQuery.validator.addMethod(
        "tweetFit", 
        function(value, element) {
            return tw_character_counter( value ) > 140 ? false : true;
        }, 
        "Sorry, amigo. Maximum 140 characters."
    );

    // Has post url
    jQuery.validator.addMethod(
        "tweetURL", 
        function(value, element) {
            if( /{{URL}}/i.test(value) ) {
                return true;
            }

            return false;
        }, 
        "Please add {{URL}} tag to your template."
    );
    
    // ...

	// Hook the script only to post types that are used by the plugin
	if( jQuery.inArray( typenow, TWAJAX.post_types ) !== -1 ) {
    
	    // Some WP hacking to skip the bug with posts not being published (just saved as drafts)
	    // more: http://wordpress.stackexchange.com/questions/119814/validating-custom-meta-boxes-with-jquery-results-in-posts-being-saved-as-draft-i

	    var form = jQuery("#post");
	    var send = form.find("#publish");
    
	    send.addClass('tw-submit');

	    jQuery('.tw-submit').click(function(e){

	        form.validate();

	        jQuery('.tweet-template-textarea').each(function(){

	            jQuery(this).rules("add",{
	                required : true,
	                tweetFit : true,
	                tweetURL : true
	            });

	        });

	        if(jQuery(form).valid()) {
	            jQuery("#publishing-action .spinner").show();
	            return true;
	        } else {
	            jQuery("#publishing-action .spinner").hide();
	            jQuery('html, body').animate({
	                scrollTop: jQuery(".tweet-template-textarea.error").offset().top
	            }, 2000);
	        }

	        return false;

	    });
	
	}

});