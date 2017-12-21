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
        $data = '';
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

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function remove_from_bulk_actions( $actions ) {
        unset( $actions[ 'edit' ] );
        return $actions;
    }


	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function material_list_post_where( $where ) {
		global $pagenow, $wpdb;

		if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='synonym' && isset( $_GET['s'] )  && $_GET['s'] != '') {
			$where = preg_replace(
				"/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
				"(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
		}
		return $where;
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function material_list_post_distinct( ) {
		global $pagenow;
		$back = '';

		if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='synonym' && isset( $_GET['s'] ) && $_GET['s'] != '') {
			$back = " DISTINCT ";
		}
		return $back;
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function material_list_post_join( $join ) {
		global $pagenow, $wpdb;

		if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='synonym' && isset( $_GET['s'] )  && $_GET['s'] != '') {
			$join .='LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
		}
		return $join;
	}


}


