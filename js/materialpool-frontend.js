
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
            });
        } else {
            var data = {
                'action': 'mp_remove_thema',
                'thema': thema,
                'gruppe': gruppe,
                'post': post
            };
            jQuery.post(mp_ajaxurl, data, function(response) {

                ret = response;
                if ( ret != 'ok'  ) {

                }
            });
        }

    })
});


jQuery(document).ready(function(){
    jQuery(".materialpool-vorschlag-send").click( function() {
        jQuery('.materialpool-vorschlag-hinweis').html('');
        var url = jQuery("#vorschlag-url").val();
        var description = jQuery("#vorschlag-beschreibung").val();
        if ( url == '' ) {
            jQuery('.materialpool-vorschlag-hinweis').append('Eine URL muss angegeben werden.');
        }
        var data = {
            'action': 'mp_add_proposal',
            'url': url,
            'description': description
        };
        jQuery.post(ajaxurl, data, function(response ) {
            ret = response;
            jQuery('.materialpool-vorschlag-hinweis').append(ret);
        })
    })
});

