/**
 * Check/Uncheck Themenseite
 */

jQuery(document).ready(function(){
     jQuery(".check_themenseite").change( function() {
         // Data Read
         var thema = jQuery(this).data("thema");
         var gruppe = jQuery(this).data("gruppe");
         var post = jQuery(this).data("post");
        if( jQuery(this).is(':checked') ) {
            var data = {
                'action': 'mp_add_thema',
                'thema': thema,
                'gruppe': gruppe,
                'post': post
            };
            jQuery.post(ajaxurl, data, function(response) {

                ret = response;
                if ( ret != 'ok'  ) {

                }
            });
        } else {
            var data = {
                'action': 'mp_remove_thema',
                'thema': thema,
                'gruppe': gruppe,
                'post': post
            };
            jQuery.post(ajaxurl, data, function(response) {

                ret = response;
                if ( ret != 'ok'  ) {

                }
            });
        }

    })
});

