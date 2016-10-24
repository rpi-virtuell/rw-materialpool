<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class Materialpool_Bildungsstufen {

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function taxonomy_column( $columns ) {
        unset( $columns[ 'posts' ] );
        $columns[ 'uses' ] = __( 'Anzahl', Materialpool::get_textdomain() );
        return $columns;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function taxonomy_column_data( $out, $column_name, $term_id ) {
        global $wpdb;

        switch ($column_name) {
            case 'uses':
                $anzahl = $wpdb->get_col( $wpdb->prepare( "SELECT count( meta_id ) as anzahl  FROM  $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_bildungsstufe', $term_id) );
                $out .= $anzahl[ 0 ];
                break;

            default:
                break;
        }
        return $out;
    }

}
