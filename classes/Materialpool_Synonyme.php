<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class Materialpool_Synonyme {

    /**
     * Change the columns for list table
     *
     * @since   0.0.1
     * @access	public
     * @var     array    $columns    Array with columns
     * @return  array
     */
    static public function cpt_list_head( $columns ) {
        unset ( $columns[ 'date'] );
        $columns[ 'normwort' ] = _x( 'Normwort', 'Synonym list field',  Materialpool::$textdomain );
        $columns[ 'date' ] = __( 'Date' );
        return $columns;
    }


    /**
     * Add content for the custom columns in list table
     *
     * @since   0.0.1
     * @access	public
     * @var     string  $column_name    name of the current column
     * @var     int     $post_id        ID of the current post
     */
    static public function cpt_list_column( $column_name, $post_id ) {

        if ( $column_name == 'normwort' ) {
            $data = get_metadata( 'post', $post_id, 'normwort' , true );
        }
        echo $data;
    }

    /**
     * Set the sortable columns
     *
     * @since   0.0.1
     * @access	public
     * @param   array   $columns    array with the default sortable columns
     * @return  array   Array with sortable columns
     */
    static public function cpt_sort_column( $columns ) {
        return array_merge( $columns, array(
            'normwort' => 'normwort',
        ) );
    }

}


