<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */

class Materialpool_Ratings {

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function page_column( $columns ) {
        $columns['ratings'] =  __( 'Bewertung', Materialpool::get_textdomain() );
        return $columns;
    }
}
