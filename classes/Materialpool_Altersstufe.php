<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Altersstufe {
    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_altersstufe_taxonomy_label
     * @filters materialpool_altersstufe_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name"                          => __( 'Age brackets', Materialpool::$textdomain ),
            "singular_name"                 => __( 'Age bracket', Materialpool::$textdomain ),
            'search_items'                  => __( 'Search age brackets', Materialpool::$textdomain ),
            'popular_items'                 => __( 'Popular age brackets', Materialpool::$textdomain ),
            'all_items'                     => __( 'All age brackets', Materialpool::$textdomain ),
            'parent_item'                   => null,
            'parent_item_colon'             => null,
            'edit_item'                     => __( 'Edit age bracket', Materialpool::$textdomain ),
            'update_item'                   => __( 'Update age bracket', Materialpool::$textdomain ),
            'add_new_item'                  => __( 'Add New age bracket', Materialpool::$textdomain ),
            'new_item_name'                 => __( 'New age bracket Name', Materialpool::$textdomain ),
            'separate_items_with_commas'    => __( 'Separate age bracket with commas', Materialpool::$textdomain ),
            'add_or_remove_items'           => __( 'Add or remove age bracket', Materialpool::$textdomain ),
            'choose_from_most_used'         => __( 'Choose from the most used age brackets', Materialpool::$textdomain ),
            'not_found'                     => __( 'No age brackets found.', Materialpool::$textdomain ),
            'menu_name'                     => __( 'Age brackets', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Age bracket', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_altersstufe_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'altersstufe', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "altersstufe", array( "material" ), apply_filters( 'materialpool_altersstufe_taxonomy_args', $args ) );

    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_taxonomy_metadata() {
        $cmb = new_cmb2_box( array(
            'id'            => 'cmb_alterstufe',
            'title'         => __( 'Altersstufe', Materialpool::get_textdomain() ),
            'object_types'     => array( 'term' ),
            'taxonomies'       => array( 'altersstufe' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ) );

        // Regular text field
        $cmb->add_field( array(
            'name'    => _x( 'Sortorder', 'Altersstufe Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'sortorder for taxonomy', 'Altersstufe Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'         =>  'altersstufe_sort',
            'type'       => 'text',
            'show_on_cb' => 'cmb2_hide_if_no_cats',
        ) );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function taxonomy_column( $columns ) {
        $columns[ 'altersstufe_sort' ] = __( 'Sort order', Materialpool::get_textdomain() );
        return $columns;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function taxonomy_column_data( $out, $column_name, $term_id ) {
        switch ($column_name) {
            case 'altersstufe_sort':
                $out .= get_term_meta(  $term_id, 'altersstufe_sort', true );
                break;

            default:
                break;
        }
        return $out;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function taxonomy_sort_column( $sortable ) {
        $sortable[ 'altersstufe_sort' ] = 'altersstufe_sort';
        return $sortable;

    }
}
