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




/**
 *
 * Set Defauls for new Spezial
 *
 * @todo Zuordnungen im Backend Pflegbar
 */

jQuery(document).ready(function(){
    var url = document.URL;
    if ( url.indexOf( 'post-new.php?post_type=spezial') !== -1 ) {
        // set verfuegbarkeit
        jQuery("#pods-form-ui-pods-meta-spezial-verfuegbarkeit").val("51");

        // set sprache
        jQuery(".pods-form-ui-field-name-pods-meta-spezial-sprache").each( function() {
            if ( jQuery(this).val() == 4 ) {
                // checkbox deutsch
                jQuery(this).attr('checked', true);
            }
        })
    }
});


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
                img = "https://s0.wordpress.com/mshots/v1/" + url + "?w=400&h=300";
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
                    $obj = jQuery.parseJSON( html );
                    if ( jQuery("#pods-form-ui-pods-meta-material-titel").val() == '') {
                        jQuery("#pods-form-ui-pods-meta-material-titel").val( $obj.title );
                    }
                    if ( jQuery("#pods-form-ui-pods-meta-material-kurzbeschreibung").val() == '') {
                        jQuery("#pods-form-ui-pods-meta-material-kurzbeschreibung").val( $obj.description );
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



