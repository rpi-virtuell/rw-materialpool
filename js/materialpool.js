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
        // set sprache
        jQuery("input[name='acf[field_5dbc8c18f8d06][]']").each( function() {
            if ( jQuery(this).val() == 4 ) {
                // checkbox deutsch
                jQuery(this).attr('checked', true);
            }
        })
        jQuery("input[name='acf[field_5dbc8c50855bb][]']").each( function() {
            if ( jQuery(this).val() == 2206 ) {
                // checkbox Handverlesen
                jQuery(this).attr('checked', true);
            }
        })
        // Verfügbarkeit
        jQuery("input[name='acf[field_5dbc8eedaf43e]']").each( function() {
            if ( jQuery(this).val() == 182 ) {
                // checkbox Handverlesen
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
    jQuery("input[name='acf[field_5dbc8a128988b][]']").click( function() {
        switch ( jQuery(this).val() ) {
            case "7":    // Elementarbereich
                set_altersstufe( 37 ); // 1-5
                break;
            case "8":    // Erwachsenenbildung
                set_altersstufe( 41 ); //
                set_materialbildungsstufe( 6 );
                break;
            case "9":    // Arbeit mit Jugendlichen
                set_altersstufe( 39 ); // 13-15
                set_altersstufe( 40 ); // 15-19
                set_materialbildungsstufe( 6 );
                break;
            case "108":    // Arbeit mit Kindern
                set_altersstufe( 42 ); // 5-10
                set_altersstufe( 38 ); // 10-13
                set_materialbildungsstufe( 6 );
                break;
            case "10":    // Kindergottesdienst
                set_materialbildungsstufe( 6 );
                set_altersstufe( 37 ); // 1-5
                set_altersstufe( 42 ); // 5-10

                break;
            case "11":    // Konfirmandenarbeit
                set_altersstufe( 39 ); // 13-15
                set_materialbildungsstufe( 6 );
                break;
            case "13":    // Berufsshule
                set_altersstufe( 40 ); // 15-19
                set_materialbildungsstufe( 12 );
                break;
            case "14":    // Grundschule
                set_altersstufe( 42 ); // 5-10
                set_materialbildungsstufe( 12 );
                break;
            case "15":    // Oberstufe
                set_materialbildungsstufe( 12 );
                set_altersstufe( 40 ); // 15-19
                break;
            case "16":    // Sekundarstufe
                set_altersstufe( 38 ); // 10-13
                set_altersstufe( 39 ); // 13-15
                set_materialbildungsstufe( 12 );
                break;
            case "3273":    // AusBildung
                set_materialbildungsstufe( 306 );
                break;
        }
    })

});

function set_altersstufe( id ) {
    jQuery("input[name='acf[field_5dbc8a9ea8d52][]']").each( function() {
        if ( jQuery(this).val() == id ) {
            jQuery(this).attr('checked', true);
        }
    })
}

function set_materialbildungsstufe( id ) {
    jQuery("input[name='acf[field_5dbc8a128988b][]']").each( function() {
        if ( jQuery(this).val() == id ) {
            jQuery(this).attr('checked', true);
        }
    })
}



jQuery(document).ready(function(){
    jQuery("input[name='acf[field_5dbc8bed9f213][]']").click( function() {
        switch ( jQuery(this).val() ) {
            case "18":    // Gamification
                set_materialmedientyp( 17 ); // 1-5
                break;
            case "20":    // Online Lesson
                set_materialmedientyp( 17 ); // 1-5
                break;
            case "22":    // Lokale Einrichtung
                set_materialmedientyp( 21 ); // 1-5
                break;
            case "23":    // Virtueller Lernort
                set_materialmedientyp( 21 ); // 1-5
                break;
            case "69":    // Audio
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "25":    // Bild
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "4165":    // Filme im Verleih
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "3207":    // Gebet/Lied
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "27":    // Internetportal
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "589":    // Präsentation
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "28":    // Text/Ausatz
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "29":    // Video
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "26":    // Zeitschrift/Buch
                set_materialmedientyp( 24 ); // 1-5
                break;
            case "34":    // Anforderungssituation
                set_materialmedientyp( 30 ); // 1-5
                break;
            case "32":    // Arbeitsblatt
                set_materialmedientyp( 30 ); // 1-5
                break;
            case "33":    // Aufgabenstellung
                set_materialmedientyp( 30 ); // 1-5
                break;
            case "35":    // Erählung
                set_materialmedientyp( 30 ); // 1-5
                break;
            case "133":    // Lernstation
                set_materialmedientyp( 30 ); // 1-5
                break;
            case "53":    // Gottesdienstentwurf
                set_materialmedientyp( 36 ); // 1-5
                break;
            case "54":    // Projektplanung
                set_materialmedientyp( 36 ); // 1-5
                break;
            case "55":    // Unterrichtsentwurf
                set_materialmedientyp( 36 ); // 1-5
                break;
        }
    })

});

function set_materialmedientyp( id ) {
    jQuery("input[name='acf[field_5dbc8bed9f213][]']").each( function() {
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
            'action': 'mp_get_screenshot',
            'site': url
        };

        jQuery.post(ajaxurl, data, function(response) {
            html = response;
            if ( html != ''  ) {
                img = html;
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
 * get title&description
 *
 */

jQuery(document).ready(function(){
    jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').focusout( function() {

        var url = jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').val();
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

                    jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').val('');
                    jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').focus();

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
                if ( jQuery('input[name="acf[field_5dbc825df7494]"]').val() == '') {
                    jQuery('input[name="acf[field_5dbc825df7494]"]').val( $obj.title );
                }
                if ( (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden() ) {
                    text = $obj.description + "<br><br>" + tinyMCE.activeEditor.getContent();
                    tinyMCE.activeEditor.setContent( text );
                } else {
                    text = $obj.description + "\n\n" + jQuery('input[name="acf[field_5dbc82ca3e84f]"]').val();
                    jQuery('input[name="acf[field_5dbc82ca3e84f]"]').val( text );
                }
                if ( jQuery('input[name="acf[field_5dbc898b69985]"]').val() == '') {
                    jQuery('input[name="acf[field_5dbc898b69985]"]').val( $obj.keywords );
                }
                if ( jQuery('input[name="acf[field_5dc13b57f2a74]"]').val() == '') {
                    jQuery('input[name="acf[field_5dc13b57f2a74]"]').val( $obj.image );
                }

            }
        });
    })

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
        jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').click();
        jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').val( url );
        jQuery('input[name="acf[field_5dbc82ca3e84f]"]').val( unescape(text ));

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

                    jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').val('');
                    jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').focus();

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
                if ( jQuery('input[name="acf[field_5dbc825df7494]"]').val() == '') {
                    jQuery('input[name="acf[field_5dbc825df7494]"]').val( obj.title );
                }
                if ( jQuery('input[name="acf[field_5dbc82995b741]"]').val() == '') {
                    jQuery('input[name="acf[field_5dbc82995b741]"]').val( obj.description );
                }
                if ( jQuery('input[name="acf[field_5dbc898b69985]"]').val() == '') {
                    jQuery('input[name="acf[field_5dbc898b69985]"]').val( obj.keywords );
                }
                if ( jQuery('input[name="acf[field_5dc13b57f2a74]"]').val() == '') {
                    jQuery('input[name="acf[field_5dc13b57f2a74]"]').val( obj.image );
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
    if ( jQuery( 'input[name="acf[field_5dbc823aa108e]"]' ).is( ":checked" ) ) {
        jQuery(".acf-field-5dbc6c2e9e6d5").hide();
    }

    // Hide URL
    jQuery('input[name="acf[field_5dbc823aa108e]"]').click(function(){
        if ( jQuery( 'input[name="acf[field_5dbc823aa108e]"]' ).is( ":checked" ) ) {
            jQuery(".acf-field-5dbc6c2e9e6d5").hide();
        } else {
            jQuery(".acf-field-5dbc6c2e9e6d5").show();
        }
    });


    // Set URL on Specials, after title is focus lost
    jQuery('input[name="acf[field_5dbc825df7494]"]').focusout( function() {
        if ( jQuery( 'input[name="acf[field_5dbc823aa108e]"]' ).is( ":checked" ) ) {
            if ( jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').val() == '') {
                jQuery('input[name="acf[field_5dbc6c2e9e6d5]"]').val( 'http://localhost/random'  +  Math.floor((Math.random() * 10000000) + 1)  );
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
    jQuery('input[name="acf[field_5dbc825df7494]"]').focusout( function() {
        var title = jQuery('input[name="acf[field_5dbc825df7494]"]').val();
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
    jQuery('input[name="acf[field_5dcd8482bb0ec]"]').focusout( function() {
        var title = jQuery('input[name="acf[field_5dcd8482bb0ec]"]').val();
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
    if (typeof(acf) !== 'undefined') {
        var datefield = acf.getField('field_5dbc91e3d3a3c');
        datefield.on('change', function () {
            var date = datefield.val().toString()
            var year = date.substring(0, 4);

            if (!isNaN(year)) {
                var jahr = acf.getField('field_5dbc925636320');
                jahr.val(year);
            }

        });
    }

});


function check_material() {
    if (typeof(acf) !== 'undefined') {
        jQuery("#mppubinfo").empty();
        var text;

        var field = acf.getField('field_5dbc82995b741');
        if (!field.val()) {
            field.showError('Kurzbeschreibung nicht angegeben.');
            jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Kurzbeschreibung nicht angegeben.</div>");

        }

        var field2 = acf.getField('field_5dbc6c2e9e6d5');
        if (!field2.val()) {
            field2.showError('Material URL nicht angegeben.');
            jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Material URL nicht angegeben.</div>");

        }

        var field3 = acf.getField('field_5dbc82ca3e84f');
        if (!field3.val()) {
            field3.showError('Beschreibung nicht angegeben.');
            jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Beschreibung nicht angegeben.</div>");

        }

        var field4 = acf.getField('field_5dbc8a128988b');
        if (!field4.val()) {
            field4.showError('Keine Bildungsstufe ausgewählt.');
            jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Keine Bildungsstufe gewählt.</div>");

        }

        var field5 = acf.getField('field_5dbc8bed9f213');
        if (!field5.val()) {
            field5.showError('Kein Medientyp ausgewählt.');
            jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Kein Medientyp gewählt.</div>");

        }

        var field6 = acf.getField('field_5dbc888798a2f');
        if (!field6.val()) {
            field6.showError('Keine Schlagworte ausgewählt.');
            jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Keine Schlagworte vergeben.</div>");

        }

        var field7 = acf.getField('field_5dce78682efe3');
        if (!field7.val()) {
            var str = field5.val().toString();
            if (str != false) {
                var myarray = str.split(',');
                var out = 0;
                for (var i = 0; i < myarray.length; i++) {
                    if (myarray[i] == 17) out = 1;
                    if (myarray[i] == 30) out = 1;
                    if (myarray[i] == 55) out = 1;
                }
                if (out == 1) {
                    field7.showError('Keine Kompetenzen ausgewählt.');
                    jQuery("#mppubinfo").append("<div class='materialpool-notice-error'>Keine Kompetenz gewählt.</div>");
                }
            }
        }
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

jQuery(window).ready(function() {
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

    jQuery(".einverstaendnis_autor").click( function() {
        var id = jQuery(this).data("id");
        var data = {
            'action': 'mp_change_autor_einverstaendnis',
            'id': id
        };
        jQuery.post(ajaxurl, data, function(response) {
            ret = response;
        });
    })

    jQuery(".einverstaendnis_organisation").click( function() {
        var id = jQuery(this).data("id");
        var data = {
            'action': 'mp_change_organisation_einverstaendnis',
            'id': id
        };
        jQuery.post(ajaxurl, data, function(response) {
            ret = response;
        });
    })

    jQuery(".contribute").click( function() {

        var autor  = jQuery(this).data("autor");
        var user  = jQuery(this).data("user");
        var action = jQuery(this).data("action");
        var data = {
            'action': 'mp_edit_subscription',
            'autor': autor,
            'user' : user,
            'cmd'  : action,
        };
        jQuery.post(ajaxurl, data, function(response ) {
            location.reload();
        });
    });


});

