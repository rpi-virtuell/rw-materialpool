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
	            $anzahl = get_term( $term_id, 'bildungsstufe' );
	            $url =  admin_url( 'edit.php?post_type=material&bildungsstufe=' . $anzahl->slug );
	            $out .=  '<a href="' . $url . '">'.$anzahl->count . '</a>';
                break;

            default:
                break;
        }
        return $out;
    }

}
