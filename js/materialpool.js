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



