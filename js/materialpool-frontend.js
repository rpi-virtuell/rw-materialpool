
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
    var element = jQuery("#autor-subscription");
    var autor  = jQuery(element).data("autor");
    var user  = jQuery(element).data("user");

    var data = {
        'action': 'mp_check_subscription',
        'autor': autor,
        'user' : user,
    };
    jQuery.post(ajaxurl, data, function(response ) {
        ret = response;
        jQuery(element).html('');
        jQuery(element).append(ret);
    });

    jQuery("#autor-subscription").click( function() {
        var autor  = jQuery(element).data("autor");
        var user  = jQuery(element).data("user");
        var data = {
            'action': 'mp_add_subscription',
            'autor': autor,
            'user' : user,
        };
        jQuery.post(ajaxurl, data, function(response ) {
            ret = response;
            jQuery(element).html('');
            jQuery(element).append(ret);
        });
    });
});



jQuery(document).ready(function(){
    jQuery(".materialpool-vorschlag-send").click( function() {
        jQuery('.materialpool-vorschlag-hinweis').html('');
        var url = jQuery("#vorschlag-url").val();
        var description = jQuery("#vorschlag-beschreibung").val();
        var user = jQuery("#vorschlag-name").val();
        var email = jQuery("#vorschlag-email").val();
        if ( url == '' ) {
            jQuery('.materialpool-vorschlag-hinweis').append('Eine URL muss angegeben werden.');
            return;
        }
        var data = {
            'action': 'mp_add_proposal',
            'url': url,
            'user' : user,
            'email' : email,
            'description': description
        };
        jQuery.post(ajaxurl, data, function(response ) {
            ret = response;
            jQuery('.materialpool-vorschlag-hinweis').append(ret);
            jQuery("#vorschlag-url").val('');
            jQuery("#vorschlag-beschreibung").val('');
        })
    })
});



jQuery(document).ready(function(){
    jQuery(".materialpoolautorregister").click( function() {
        jQuery('.materialpoolautorhinweis').html('');
        var vorname = jQuery("#materialpoolautorvorname").val();
        var name = jQuery("#materialpoolautorname").val();
        var user = jQuery("#materialpoolautorid").val();
        var email = jQuery("#materialpoolautoremail").val();
        if ( vorname == '' || name == '' || user == '' || email == ''  ) {
            jQuery('.materialpoolautorhinweis').append('Bitte alle Felder ausfüllen!');
            return;
        }
        var data = {
            'action': 'mp_check_autor_request',
            'vorname': vorname,
            'name' : name,
            'user' : user,
            'email' : email
        };
        jQuery.post(ajaxurl, data, function(response ) {
            ret = response;
            jQuery('.materialpoolautorhinweis').append(ret);


        })
    })
    jQuery(".materialpoolautorhinweis").on("click",".materialpoolautorregister2", ( function() {
        jQuery('.materialpoolautorhinweis').html('');
        var vorname = jQuery("#materialpoolautorvorname").val();
        var name = jQuery("#materialpoolautorname").val();
        var user = jQuery("#materialpoolautorid").val();
        var email = jQuery("#materialpoolautoremail").val();
        if ( vorname == '' || name == '' || user == '' || email == ''  ) {
            jQuery('.materialpoolautorhinweis').append('Bitte alle Felder ausfüllen!');
            return;
        }
        var data = {
            'action': 'mp_check_autor_request2',
            'vorname': vorname,
            'name' : name,
            'user' : user,
            'email' : email
        };
        jQuery.post(ajaxurl, data, function(response ) {
            ret = response;
            jQuery('.materialpoolautorhinweis').append(ret);

        })
    }))

});


jQuery(document).ready(function(){
    jQuery("#vorschlag-url").focusout( function() {
        jQuery.showLoading({name: 'jump-pulse', allowHide: true });
        var url = jQuery("#vorschlag-url").val();
        var postid = jQuery("#post_ID").val();
        if ( url == '' ) return;
        var ret;

        // url exists?
        var data = {
            'action': 'mp_check_url',
            'site': url,
            'post-id': postid
        };
        jQuery.post(ajaxurl, data, function(response) {

            ret = response;
            if ( ret != ''  ) {
                var obj = jQuery.parseJSON( ret );
                if ( obj.status == 'exists' ) {

                    jQuery("#vorschlag-url").val('');
                    jQuery("#vorschlag-url").focus();

                    jQuery("body").append("<div id='" + obj.status + "' title='Hinweis'>" +
                        "<p align='center'>Diese URL wurde schon erfasst unter diesem <a  target='_blank' href='" + obj.material_url + "'>Material</a>.</p>" +
                        "</div>");

                    jQuery( "#" + obj.status ).dialog({
                        dialogClass: "no-close",
                        buttons: [
                            {
                                text: "OK",
                                click: function() {
                                    jQuery( this ).dialog( "close" );
                                }
                            }
                        ],
                        width: 450,
                        height: 280,
                        show: {
                            effect: "blind",
                            duration: 1000
                        },
                        hide: {
                            effect: "blind",
                            duration: 800
                        }
                    });
                    jQuery.hideLoading();
                    return;
                }

            }
        });



        var html;
        var data = {
            'action': 'mp_get_description',
            'site': url,
            'post-id': postid
        };

        jQuery.post(ajaxurl, data, function(response) {
            var text;
            html = response;
            if ( html != ''  ) {
                var $obj = jQuery.parseJSON( html );
                text = $obj.description + "\n\n" + jQuery("#vorschlag-beschreibung").val();
                jQuery("#vorschlag-beschreibung").val( text );
            }
            jQuery.hideLoading();
        });

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
