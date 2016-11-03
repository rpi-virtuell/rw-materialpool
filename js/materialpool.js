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
 */

jQuery(document).ready(function(){
    var url = document.URL;
    if ( url.indexOf( 'post-new.php?post_type=material') !== -1 ) {
        // set verfuegbarkeit
        jQuery("#pods-form-ui-pods-meta-material-verfuegbarkeit").val("49");

        // set sprache
        jQuery(".pods-form-ui-field-name-pods-meta-material-sprache").each( function() {
            if ( jQuery(this).val() == 21 ) {
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
 */

jQuery(document).ready(function(){
    jQuery(".pods-form-ui-field-name-pods-meta-material-bildungsstufe").click( function() {
        switch ( jQuery(this).val() ) {
            case "11":    // Elementarbereich
                set_altersstufe( 4 ); // 1-5
                break;
            case "12":    // Erwachsenenbildung
                set_altersstufe( 9 ); // 1-5
                break;
            case "13":    // Kinder und Jugendarbeit
                set_altersstufe( 5 ); // 5-10
                set_altersstufe( 6 ); // 10-13
                set_altersstufe( 7 ); // 13-15
                set_altersstufe( 8 ); // 15-19
                break;
            case "14":    // Kindergottesdienst
                set_altersstufe( 4 ); // 1-5
                set_altersstufe( 5 ); // 5-10
                break;
            case "15":    // Konfirmandenarbeit
                set_altersstufe( 7 ); // 13-15
                break;
            case "17":    // Berufsshule
                set_altersstufe( 8 ); // 15-19
                break;
            case "18":    // Grundschule
                set_altersstufe( 5 ); // 5-10
                break;
            case "19":    // Oberstufe
                set_altersstufe( 8 ); // 15-19
                break;
            case "20":    // Sekundarstufe
                set_altersstufe( 6 ); // 10-13
                set_altersstufe( 7 ); // 13-15
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
 * Screeshotbuttons
 *
 */

jQuery(document).ready(function(){
    jQuery("#generate-screenshot").click( function() {
        var url = jQuery("#pods-form-ui-pods-meta-material-url").val();
        img = "https://s0.wordpress.com/mshots/v1/" + url + "?w=400&h=300";


        jQuery('#material-screenshot').html('');
        jQuery('#material-screenshot').prepend('<img id="theImg" src="' + img + '" />')
        jQuery('#pods-form-ui-pods-meta-material-screenshot').val( img );

    })

    jQuery("#delete-screenshot").click( function() {
        jQuery('#material-screenshot').html('');
        jQuery('#pods-form-ui-pods-meta-material-screenshot').val('');
    })

});


