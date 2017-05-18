
/**
 * Check/Uncheck Themenseite
 */

jQuery(document).ready(function(){
         jQuery('.material-results').on('change', 'input[type=checkbox]', function(){
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


// Fix for daterage facet in IE
(function($) {
    $(document).on('facetwp-loaded', function() {

        // do something, starting *after* pageload.
        var isIE11 = /Trident.*rv[ :]*11\./.test(navigator.userAgent);
        var isIE10 = /Trident.*rv[ :]*6\./.test(navigator.userAgent);

        if ( isIE10 || isIE11 ) {
            jQuery('input, select, textarea, :input').removeAttr('placeholder');
        }

    });
})(jQuery);

var QueryString = function () {
    // This function is anonymous, is executed immediately and
    // the return value is assigned to QueryString!
    var query_string = {};
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
        var pair = vars[i].split("=");
        // If first entry with this name
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = decodeURIComponent(pair[1]);
            // If second entry with this name
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
            query_string[pair[0]] = arr;
            // If third or later entry with this name
        } else {
            query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
    }
    return query_string;
}();



jQuery.ajaxSetup({
    data: {
        "mp_url": window.location.search,
        "mp_thema": QueryString.thema,
        "mp_gruppe": QueryString.gruppe,
        "mp_search": QueryString.fwp_suche,
    }
});

jQuery(document).ready(function(){
     jQuery('body').keyup(function( e ){
        jQuery.ajaxSetup({
            data: {
                "mp_url": window.location.search,
                "mp_thema": QueryString.thema,
                "mp_gruppe": QueryString.gruppe,
                "mp_search": jQuery(".facetwp-search").val(),
            }
        });
    })
});