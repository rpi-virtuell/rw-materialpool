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
        $query_str 		= $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 WHERE pandarf_parent_post_id = %s order by pandarf_order ', $id );
        $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
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



}
