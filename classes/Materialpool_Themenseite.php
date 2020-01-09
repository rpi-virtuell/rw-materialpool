<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class Materialpool_Themenseite {


    /**
     *
     * @since 0.0.1
     * @access	public
     */
    static public function load_template($template) {
        global $post;

        if ($post->post_type == "themenseite" && !is_embed() ){
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-themenseite.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-themenseite.php';
                }
            }
            if ( is_archive() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/archive-themenseite.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/archive-themenseite.php';
                }
            }
            Materialpool_Statistic::log( $post->ID, $post->post_type );
            return $template_path;
        }
        return $template;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_template_check_external_files ( $checkArray ) {
        $checkArray[ 'materialpool/single-themenseite.php' ] = Materialpool::$plugin_base_dir . 'templates/single-themenseite.php';
        $checkArray[ 'materialpool/archive-themenseite.php'] = Materialpool::$plugin_base_dir . 'templates/archive-themenseite.php';
        return $checkArray;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function generate_taxonomy( $post_id ) {
        $post_type = get_post_type($post_id);

        if ( "themenseite" != $post_type ) return;

	    // Transients für Frontendcache löschen
	    delete_transient( 'facet_serach2_entry-'.$post_id );

        // Schlagwort det Themenseite in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'schlagwort' );
        $cats = explode( ',', $_POST[ 'pods_meta_thema_schlagworte' ] ) ;
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'schlagwort', true );

	    // Vorauswahl des Materials in term_rel speichern
	    wp_delete_object_term_relationships( $post_id, 'vorauswahl' );
	    $cats = $_POST[ 'pods_meta_vorauswahl' ];
	    if ( is_array( $cats ) ) {
		    foreach ( $cats as $key => $val ) {
			    $cat_ids[] = (int) $val;
		    }
	    }
	    if ( $cats!== null  ) {
		    $cat_ids[] = (int) $cats;
	    }
	    wp_set_object_terms( $post_id, $cat_ids, 'vorauswahl', true );


    }
    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_gruppen( $id = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;
        global $wpdb;

		$items_arr = array();
		$anzahl = count( get_field( 'themengruppen') );

		for ( $i = 0;  $i < $anzahl ;$i++ ) {
			$items_arr[ $i ] = array(
				'gruppe' => get_field( 'themengruppen_' . $i . '_gruppe_von_materialien'),
				'gruppenbeschreibung' => get_field( 'themengruppen_' . $i . '_infos' ),
				'auswahl' => get_field('themengruppen_' . $i . '_material_in_dieser_gruppe' )
			);
		}

        return $items_arr;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_gruppen_by_groupid( $id = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;
        global $wpdb;
        $query_str 		= $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 WHERE id = %s order by pandarf_order ', $id );
        $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr;
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
	 * Change the columns for list table
	 *
	 * @since   0.0.1
	 * @access	public
	 * @var     array    $columns    Array with columns
	 * @return  array
	 */
	static public function cpt_list_head( $columns ) {
		$columns[ 'themenseite-schlagworte' ] = _x( 'Schlagworte', 'Material list field',  Materialpool::$textdomain );
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
		global $wpdb;
		$data = '';

		if ( $column_name == 'themenseite-schlagworte' ) {
			$schlagworte = get_metadata( 'post', $post_id, 'thema_schlagworte' );
			if ( is_array( $schlagworte[0] )) {
				foreach ( $schlagworte[0] as $schlagwort ) {
					$term = get_term( $schlagwort, "schlagwort" );
					$data .=  $term->name. '<br>';
				}
			}
		}

		echo $data;
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function remove_post_custom_fields() {
		remove_meta_box( 'vorauswahldiv' , 'themenseite' , 'normal' );
	}

}
