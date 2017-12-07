/**
 * Materialpool Admin JS
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


/**
 *
 * Set Defauls for new Material
 *
 * @todo Zuordnungen im Backend Pflegbar
 */

jQuery(document).ready(function(){
    var url = document.URL;
    if ( url.indexOf( 'post-new.php?post_type=material') !== -1 ) {
        // set verfuegbarkeit
        jQuery("#pods-form-ui-pods-meta-material-verfuegbarkeit").val("51");

        // set sprache
        jQuery(".pods-form-ui-field-name-pods-meta-material-sprache").each( function() {
            if ( jQuery(this).val() == 4 ) {
                // checkbox deutsch
                jQuery(this).attr('checked', true);
            }
        })
    }
});

/**
 *
 * Set Alterstufen depends on Bildungsstufe
 *
 * @todo Zuordnungen im Backend Pflegbar
 */

jQuery(document).ready(function(){
    jQuery(".pods-form-ui-field-name-pods-meta-material-bildungsstufe").click( function() {
        switch ( jQuery(this).val() ) {
            case "7":    // Elementarbereich
                set_altersstufe( 37 ); // 1-5
                break;
            case "8":    // Erwachsenenbildung
                set_altersstufe( 41 ); //
                break;
            case "9":    // Arbeit mit Jugendlichen
                set_altersstufe( 39 ); // 13-15
                set_altersstufe( 40 ); // 15-19
                break;
            case "108":    // Arbeit mit Kindern
                set_altersstufe( 42 ); // 5-10
                set_altersstufe( 38 ); // 10-13
                break;
            case "10":    // Kindergottesdienst
                set_altersstufe( 37 ); // 1-5
                set_altersstufe( 42 ); // 5-10
                break;
            case "11":    // Konfirmandenarbeit
                set_altersstufe( 39 ); // 13-15
                break;
            case "13":    // Berufsshule
                set_altersstufe( 40 ); // 15-19
                break;
            case "14":    // Grundschule
                set_altersstufe( 42 ); // 5-10
                break;
            case "15":    // Oberstufe
                set_altersstufe( 40 ); // 15-19
                break;
            case "16":    // Sekundarstufe
                set_altersstufe( 38 ); // 10-13
                set_altersstufe( 39 ); // 13-15
                break;
        }
    })
});


function set_altersstufe( id ) {
    jQuery(".pods-form-ui-field-name-pods-meta-material-altersstufe").each( function() {
        if ( jQuery(this).val() == id ) {
            jQuery(this).attr('checked', true);
        }
    })
}





jQuery(document).ready(function(){
    jQuery(".pods-form-ui-field-name-pods-meta-spezial-bildungsstufe").click( function() {
        switch ( jQuery(this).val() ) {
            case "7":    // Elementarbereich
                set_spezial_altersstufe( 37 ); // 1-5
                break;
            case "8":    // Erwachsenenbildung
                set_spezial_altersstufe( 41 ); //
                break;
            case "9":    // Kinder und Jugendarbeit
                set_spezial_altersstufe( 42 ); // 5-10
                set_spezial_altersstufe( 38 ); // 10-13
                set_spezial_altersstufe( 39 ); // 13-15
                set_spezial_altersstufe( 40 ); // 15-19
                break;
            case "10":    // Kindergottesdienst
                set_spezial_altersstufe( 37 ); // 1-5
                set_spezial_altersstufe( 42 ); // 5-10
                break;
            case "11":    // Konfirmandenarbeit
                set_spezial_altersstufe( 39 ); // 13-15
                break;
            case "13":    // Berufsshule
                set_spezial_altersstufe( 40 ); // 15-19
                break;
            case "14":    // Grundschule
                set_spezial_altersstufe( 42 ); // 5-10
                break;
            case "15":    // Oberstufe
                set_spezial_altersstufe( 40 ); // 15-19
                break;
            case "16":    // Sekundarstufe
                set_spezial_altersstufe( 38 ); // 10-13
                set_spezial_altersstufe( 39 ); // 13-15
                break;
        }
    })
});


function set_spezial_altersstufe( id ) {
    jQuery(".pods-form-ui-field-name-pods-meta-spezial-altersstufe").each( function() {
        if ( jQuery(this).val() == id ) {
            jQuery(this).attr('checked', true);
        }
    })
}


/**
 *
 * Screeshotbuttons
 *
 */

jQuery(document).ready(function(){
    jQuery("#generate-screenshot").click( function() {
        var url = jQuery("#pods-form-ui-pods-meta-material-url").val();
        var html;
        var data = {
            'action': 'mp_get_html',
            'site': url
        };

        jQuery.post(ajaxurl, data, function(response) {

            html = response;
            if ( html != ''  ) {
                img = html;
            } else {
                img = "https://s.wordpress.com/mshots/v1/" + encodeURIComponent( url ) + "?w=400&h=300";
            }

            jQuery('#material-screenshot').html('');
            jQuery('#material-screenshot').prepend('<img style="max-width: 400px;" id="theImg" src="' + img + '" />')
            jQuery('#pods-form-ui-pods-meta-material-screenshot').val( img );

        });


    })

    jQuery("#delete-screenshot").click( function() {
        jQuery('#material-screenshot').html('');
        jQuery('#pods-form-ui-pods-meta-material-screenshot').val('');
    })

});

/**
 *
 * MaterialEdit Schlagworte
 */

jQuery(document).ready(function(){
    jQuery('#pods-form-ui-pods-meta-material-schlagworte').on('change', function(){
        var keystring = jQuery('#pods-form-ui-pods-meta-material-schlagworte').val();
        var keys = keystring.split(',');
        var html;
        var jo;
        keys.forEach( function( s, i, o ) {
            if ( isNaN( s ) ) {

                // Ein neues Schlagwort. Prüfen gegen SynonymDB
                var data = {
                    'action': 'mp_synonym_check_tag',
                    'tag': s
                };
                jQuery.post(ajaxurl, data, function(response ) {
                    html = response;
                    if ( html != ''  ) {
                        jo = JSON.parse( html );
                        switch  ( jo.status ) {
                            case 'replace-new':
                                    keys.forEach( function( s2, i2, o2 ) {
                                        if ( s2 ==  jo.orig) {
                                            keys[ i2 ] = jo.id;

                                        }
                                    })
                                    jQuery('#pods-form-ui-pods-meta-material-schlagworte').val( keys.toString()  );
                                    jQuery('#s2id_pods-form-ui-pods-meta-material-schlagworte > ul > li').each( function () {
                                        old = jQuery(this).find('div').html();
                                        if ( old == jo.orig ) {
                                            jQuery(this).find('div').html( jo.name );
                                        }
                                    })
                                break;
                            case 'replace-exist':
                                    keys.forEach( function( s2, i2, o2 ) {
                                        if ( s2 ==  jo.orig) {
                                            keys[ i2 ] = jo.id;

                                        }
                                    })
                                    jQuery('#pods-form-ui-pods-meta-material-schlagworte').val( keys.toString()  );
                                    jQuery('#s2id_pods-form-ui-pods-meta-material-schlagworte > ul > li').each( function () {
                                        old = jQuery(this).find('div').html();
                                        if ( old == jo.orig ) {
                                            jQuery(this).find('div').html( jo.name );
                                        }
                                    })

                                break;
                            case 'error' :
                                jQuery('#s2id_pods-form-ui-pods-meta-material-schlagworte > ul > li').each( function () {
                                    old = jQuery(this).find('div').html();
                                    if ( old == jo.orig ) {
                                        jQuery(this).css({"border-color" : jo.tagcolor});
                                        jQuery("body").append("<div id='" + jo.orig + "' title='Hinweis'>" +
                                            "<p align='center'>Das Normwort konnte nicht ermittlet werden.</p>" +
                                            "<p align='center'>Bitte recherchiere <a  target='_blank' href='" + jo.url + "'>selbst</a>, ob das Schlagwort korrekt ist.</p>" +
                                            "</div>");

                                        jQuery( "#" + jo.orig ).dialog({
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
                                    }
                                })
                                break;
                        }
                    }
                })
            }
        })

    });





    /**
     *
     * get title&description
     *
     */

    jQuery(document).ready(function(){
        jQuery("#pods-form-ui-pods-meta-material-url").focusout( function() {

            var url = jQuery("#pods-form-ui-pods-meta-material-url").val();
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
                    obj = jQuery.parseJSON( ret );
                    if ( obj.status == 'exists' ) {

                        jQuery("#pods-form-ui-pods-meta-material-url").val('');
                        jQuery("#pods-form-ui-pods-meta-material-url").focus();

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
                    $obj = jQuery.parseJSON( html );
                    if ( jQuery("#pods-form-ui-pods-meta-material-titel").val() == '') {
                        jQuery("#pods-form-ui-pods-meta-material-titel").val( $obj.title );
                    }
                    if ( (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden() ) {
                        text = $obj.description + "<br><br>" + tinyMCE.activeEditor.getContent();
                        tinyMCE.activeEditor.setContent( text );
                    } else {
                        text = $obj.description + "\n\n" + jQuery("#pods-form-ui-pods-meta-material-beschreibung").val();
                        jQuery("#pods-form-ui-pods-meta-material-beschreibung").val( text );
                    }
                    if ( jQuery("#pods-form-ui-pods-meta-material-schlagworte-interim").val() == '') {
                        jQuery("#pods-form-ui-pods-meta-material-schlagworte-interim").val( $obj.keywords );
                    }
                    if ( jQuery("#pods-form-ui-pods-meta-material-cover-url").val() == '') {
                        jQuery("#pods-form-ui-pods-meta-material-cover-url").val( $obj.image );
                    }

                }
            });
        })

    });
});


var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? '' : sParameterName[1];
        }
    }
};


jQuery(document).ready(function(){
    var url = unescape(getUrlParameter('url'));
    var text = unescape(getUrlParameter('text'));
    if ( url != 'undefined' ) {
        jQuery("#pods-form-ui-pods-meta-material-url").click();
        jQuery("#pods-form-ui-pods-meta-material-url").val( url );
        jQuery("#pods-form-ui-pods-meta-material-beschreibung").val( unescape(text ));

        if ( url == '' ) return;
        var ret;

        // url exists?
        var data = {
            'action': 'mp_check_url',
            'site': url
        };
        jQuery.post(ajaxurl, data, function(response) {

            ret = response;
            if ( ret != ''  ) {
                obj = jQuery.parseJSON( ret );
                if ( obj.status == 'exists' ) {

                    jQuery("#pods-form-ui-pods-meta-material-url").val('');
                    jQuery("#pods-form-ui-pods-meta-material-url").focus();

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

                    return;
                }

            }
        });



        var html;
        var data = {
            'action': 'mp_get_description',
            'site': url
        };

        jQuery.post(ajaxurl, data, function(response) {

            html = response;
            if ( html != ''  ) {
                obj = jQuery.parseJSON( html );
                if ( jQuery("#pods-form-ui-pods-meta-material-titel").val() == '') {
                    jQuery("#pods-form-ui-pods-meta-material-titel").val( obj.title );
                }
                if ( jQuery("#pods-form-ui-pods-meta-material-kurzbeschreibung").val() == '') {
                    jQuery("#pods-form-ui-pods-meta-material-kurzbeschreibung").val( obj.description );
                }
                if ( jQuery("#pods-form-ui-pods-meta-material-schlagworte-interim").val() == '') {
                    jQuery("#pods-form-ui-pods-meta-material-schlagworte-interim").val( obj.keywords );
                }
                if ( jQuery("#pods-form-ui-pods-meta-material-cover-url").val() == '') {
                    jQuery("#pods-form-ui-pods-meta-material-cover-url").val( obj.image );
                }
            }
        });
    }
});

/**
 *
 * specials handling
 *
 */

jQuery(document).ready(function(){
    // Hide URL afer loading
    if ( jQuery( '#pods-form-ui-pods-meta-material-special1' ).is( ":checked" ) ) {
        jQuery(".pods-form-ui-row-name-material-url").hide();
    }

    // Hide URL
    jQuery('#pods-form-ui-pods-meta-material-special1').click(function(){
        jQuery("#gruppe1").attr("checked","checked");
        jQuery(".pods-form-ui-row-name-material-url").hide();

        // Check Medientyp "special"
        jQuery("#pods-form-ui-pods-meta-material-medientyp22").attr('checked', true);
    });

    // Show URL
    jQuery('#pods-form-ui-pods-meta-material-special2').click(function(){
        jQuery("#gruppe1").attr("checked","checked");
        jQuery(".pods-form-ui-row-name-material-url").show();
    });

    // Set URL on Specials, after title is focus lost
    jQuery("#pods-form-ui-pods-meta-material-titel").focusout( function() {
        if ( jQuery( '#pods-form-ui-pods-meta-material-special1' ).is( ":checked" ) ) {
            if ( jQuery("#pods-form-ui-pods-meta-material-url").val() == '') {
                jQuery("#pods-form-ui-pods-meta-material-url").val( 'http://localhost/random'  +  Math.floor((Math.random() * 10000000) + 1)  );
            }
        }
    })
});

/**
 *
 * check material title
 *
 */

jQuery(document).ready(function(){
    jQuery("#pods-form-ui-pods-meta-material-titel").focusout( function() {
        var title = jQuery("#pods-form-ui-pods-meta-material-titel").val();
        var postid = jQuery("#post_ID").val();
        if ( title == '' ) return;
        var ret;
        var data = {
            'action': 'mp_check_material_title',
            'title': title,
            'post-id': postid
        };
        jQuery.post(ajaxurl, data, function(response) {

            ret = response;
            if ( ret != ''  ) {
                obj = jQuery.parseJSON( ret );
                if ( obj.status == 'exists' ) {

                    jQuery("body").append("<div id='" + obj.status + "' title='Hinweis'>" +
                        "<p align='center'>Dieser Titel wirde schon verwendet bei diesem <a  target='_blank' href='" + obj.material_url + "'>Material</a>.</p>" +
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

                    return;
                }

            }
        });
    });
});

/**
 *
 * check organisation title
 *
 */

jQuery(document).ready(function(){
    jQuery("#pods-form-ui-pods-meta-organisation-titel").focusout( function() {
        var title = jQuery("#pods-form-ui-pods-meta-organisation-titel").val();
        if ( title == '' ) return;
        var ret;
        var data = {
            'action': 'mp_check_organisation_title',
            'title': title
        };
        jQuery.post(ajaxurl, data, function(response) {

            ret = response;
            if ( ret != ''  ) {
                obj = jQuery.parseJSON( ret );
                if ( obj.status == 'exists' ) {

                    jQuery("body").append("<div id='" + obj.status + "' title='Hinweis'>" +
                        "<p align='center'>Dieser Titel wirde schon verwendet bei dieser <a  target='_blank' href='" + obj.material_url + "'>Organisation</a>.</p>" +
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

                    return;
                }

            }
        });
    });
});




/**
 * Uncheck Themenseite Backend
 */

jQuery(document).ready(function(){
        jQuery('body').on('change', '.uncheck_themenseite', function() {
        // Data Read
        var gruppe = jQuery(this).data("gruppe");
        var post = jQuery(this).data("post");
        var data = {
            'action': 'mp_remove_thema_backend',
            'gruppe': gruppe,
            'post': post
        };
        jQuery.post(ajaxurl, data, function(response) {

            ret = response;
            jQuery('#material-' + gruppe ).html('');
            jQuery('#material-' + gruppe ).prepend( ret );
        });

    })
});


/**
 * Refresh Themenseite Backend Materialliste
 */

jQuery(document).ready(function(){
    jQuery(".themenseite-cb-backend-update").click( function() {
        // Data Read
        var gruppe = jQuery(this).data("gruppe");
        var data = {
            'action': 'mp_list_thema_backend',
            'gruppe': gruppe
        };
        jQuery.post(ajaxurl, data, function(response) {

            ret = response;
            jQuery('#material-' + gruppe ).html('');
            jQuery('#material-' + gruppe ).prepend( ret );
        });

    })
});


/**
 * Set jahr after change from veroeffentlichungsdatum
 */
jQuery(document).ready(function(){
    jQuery("#pods-form-ui-pods-meta-material-veroeffentlichungsdatum").on("change",function() {
        var date = jQuery("#pods-form-ui-pods-meta-material-veroeffentlichungsdatum").val();
        var split = date.split(".");
        var year = parseInt(split[2] || 0, 10);

        if ( ! isNaN( year)) {
            jQuery("#pods-form-ui-pods-meta-material-jahr").val(year );
        }
    })
});


function check_material() {
    jQuery("#mppubinfo").empty();
    var text;
    // URL prüfen
    if ( jQuery("#pods-form-ui-pods-meta-material-url").val() == '' ) {
        jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Material URL nicht angegeben.</div>");
    }
    // Kurzbeschreibung prüfen
    if ( jQuery("#pods-form-ui-pods-meta-material-kurzbeschreibung").val() == '' ) {
        jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Kurzbeschreibung nicht angegeben.</div>");
    }
    // Beschreibung prüfen
    if ( (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden() ) {
        text = tinyMCE.activeEditor.getContent();
    } else {
        text = jQuery("#pods-form-ui-pods-meta-material-beschreibung").val();
    }
    if ( text == '' ) {
        jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Beschreibung nicht angegeben.</div>");
    }
    // Schlagworte prüfen
    if ( jQuery("#pods-form-ui-pods-meta-material-schlagworte").val() == ''  ) {
        jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Keine Schlagworte vergeben.</div>");
    }
    // Bildungsstufen prüfen
    if ( jQuery(".pods-form-ui-field-name-pods-meta-material-bildungsstufe:checked").length == 0 ) {
        jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Keine Bildungsstufe gewählt.</div>");
    }
    // Medientyp prüfen
    if ( jQuery(".pods-form-ui-field-name-pods-meta-material-medientyp:checked").length == 0 ) {
        jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Kein Medientyp gewählt.</div>");
    }

}
/**
 * Hnweise bei Material auf Pseudo Pflichtfelder
 */
jQuery(document).ready(function(){
    if ( jQuery(".post-type-material").length ) {
        // Wenn auf der CPT Materialseite
        if (! jQuery("#mppubinfo").length ) {
            // Initial DIV Container hinzufügen
            jQuery("#publishing-action").append("<div id='mppubinfo'></div>");
            check_material();
        }

        jQuery('body').click(function(){
            check_material();
        });
    }
});

jQuery(window).load(function() {
    if ( jQuery(".post-type-material").length ) {
        check_material();
    }
});

/*
 * Konvert Post to Material
 */
jQuery(document).ready(function(){
    jQuery("#convert2material").click( function() {
        var id = jQuery(this).data("id");
        var data = {
            'action': 'convert2material',
            'post': id
        };
        jQuery.post(ajaxurl, data, function(response) {

            ret = jQuery.parseJSON( response ) ;
            if ( ret != 0 ) {
                window.location.href = ret;

            }
        });

    });
});

/*
 * Quickedit Elemente ausblenden
 */

jQuery(document).ready( function($) {
     jQuery("#inline-edit-col-center").each(function (i) {

        $(this).parent().remove();
    });

    $('span:contains("Altersstufen")').each(function (i) {
        $(this).parent().remove();
    });
    $('span:contains("Inklusion")').each(function (i) {
        $(this).parent().remove();
    });
    $('span:contains("Konfession")').each(function (i) {
        $(this).parent().remove();
    });
    $('span:contains("Lizenzen")').each(function (i) {
        $(this).parent().remove();
    });
    $('span:contains("Schlagworte")').each(function (i) {
        $(this).parent().remove();
    });
    $('span:contains("Sprachen")').each(function (i) {
        $(this).parent().remove();
    });
    $('span:contains("Verfügbarkeiten")').each(function (i) {
        $(this).parent().remove();
    });
    $('span:contains("Zugänglichkeiten")').each(function (i) {
        $(this).parent().remove();
    });
/*
    jQuery('#the-list span:contains("Bildungsstufen")').each(function (i) {
        $(this).parent().parent().remove();
    });
*/
    $('.inline-edit-date').each(function (i) {
        $(this).remove();
    });
});

jQuery(document).ready(function(){
    jQuery(".mail_autor_send").click( function() {

        var id = jQuery(this).data("id");
        var data = {
            'action': 'mp_send_autor_mail',
            'id': id
        };
        jQuery.post(ajaxurl, data, function(response) {
            ret = response;
            jQuery('#autor_nachricht-' + id ).html( ret );
        });
    })
});

jQuery(document).ready(function(){
    jQuery(".mail_organisation_send").click( function() {

        var id = jQuery(this).data("id");
        var data = {
            'action': 'mp_send_organisation_mail',
            'id': id
        };
        jQuery.post(ajaxurl, data, function(response) {
            ret = response;
            jQuery('#organisation_nachricht-' + id ).html( ret );
        });
    })
});

