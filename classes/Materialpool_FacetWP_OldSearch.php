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
        $search = $_REQUEST[ 'mp_search' ];
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
        ?>
        <script>
            (function($) {
                wp.hooks.addAction('facetwp/load/select2', function($this, obj) {
                    $this.find('.facet-source').val(obj.source);
                    $this.find('.facet-orderby').val(obj.orderby);
                    $this.find('.facet-count').val(obj.count);
                });

                wp.hooks.addFilter('facetwp/save/select2', function($this, obj) {
                    obj['source'] = $this.find('.facet-source').val();
                    obj['orderby'] = $this.find('.facet-orderby').val();
                    obj['count'] = $this.find('.facet-count').val();
                    return obj;
                });
            })(jQuery);
        </script>
        <?php
    }

    function front_scripts() {
        ?>
        <script>
            (function($) {
                wp.hooks.addAction('facetwp/refresh/select2', function($this, facet_name) {
                    FWP.facets[facet_name] = "";
                });
            })(jQuery);
        </script>
        <?php
    }

    function settings_html() {}
}


