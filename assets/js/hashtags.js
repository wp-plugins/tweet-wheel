(function($) {
    
    // This function saves any used hashtags for later use.
    // We are aiming to provide hashtag suggestions and popularity score
    // However first we need to keep collecting anonymous data before it happens
    $.fn.grab = function() {
         
        $(this).blur(function() {
            var tagslistarr = $(this).val().split(' ');
            var arr=[];
            $.each(tagslistarr,function(i,val){
                if(tagslistarr[i].indexOf('#') == 0){
                  arr.push({name:tagslistarr[i]});  
                }
            });
            
            if (arr.length !== 0) {
            
                $.post(
                    'http://tweet-wheel.com/data/hashtag.php',
                    {
                        hashtags: arr,
                        token: TWAJAX.twNonce
                    }
                );
                
            }
            
        });
        
    }
    
})(jQuery);