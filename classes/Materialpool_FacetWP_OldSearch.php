<?php

/**
 * Created by PhpStorm.
 * User: frank
 * Date: 09.03.17
 * Time: 16:47
 */
class Materialpool_FacetWP_OldSearch
{
    function __construct() {
        $this->label = __( 'Treffer im alten Materialpool', Materialpool::$textdomain );
    }


    function render( $params ) {
	    $output = '';
	    if (defined('REST_REQUEST') && REST_REQUEST) {
	        $search = $_REQUEST['mp_search'];
        } else {
	        $search = $_REQUEST[ 'fwp_suche' ];
	    }
        if ( $search != '' ) {
            $swp_query = new SWP_Query(
                array(
                    's' => $search,
                    'post_type' => array('post'),
                    'nopaging' => true,
                )
            );
            $anzahl = count($swp_query->posts);
            if ( $anzahl > 0 ) {
                $output =  "Zu dem Suchbegriff gibt es im alten <a href='/archiv/?fwp_suche=". urlencode( $search ) ."'>Materialpool</a> " . $anzahl . " Treffer ";
            }
        }
        return $output;
    }

    function filter_posts( $params ) {
        return;
    }

    function load_values( $params ) {

        return $params;
    }

    function admin_scripts() {

    }

    function front_scripts() {
 
    }

    function settings_html() {}
}


