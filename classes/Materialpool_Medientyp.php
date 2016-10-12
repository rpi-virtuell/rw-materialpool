<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Medientyp {
    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_medientyp_taxonomy_label
     * @filters materialpool_medientyp_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name" => __( 'Mediatype', Materialpool::$textdomain ),
            "singular_name" => __( 'Mediatype', Materialpool::$textdomain ),
            'search_items'                  => __( 'Search mediatype', Materialpool::$textdomain ),
            'popular_items'                 => __( 'Popular mediatype', Materialpool::$textdomain ),
            'all_items'                     => __( 'All mediatypes', Materialpool::$textdomain ),
            'parent_item'                   => null,
            'parent_item_colon'             => null,
            'edit_item'                     => __( 'Edit mediatype', Materialpool::$textdomain ),
            'update_item'                   => __( 'Update mediatype', Materialpool::$textdomain ),
            'add_new_item'                  => __( 'Add New mediatype', Materialpool::$textdomain ),
            'new_item_name'                 => __( 'New mediatype Name', Materialpool::$textdomain ),
            'separate_items_with_commas'    => __( 'Separat emediatypes with commas', Materialpool::$textdomain ),
            'add_or_remove_items'           => __( 'Add or remove mediatype', Materialpool::$textdomain ),
            'choose_from_most_used'         => __( 'Choose from the most used mediatypes', Materialpool::$textdomain ),
            'not_found'                     => __( 'No mediatype found.', Materialpool::$textdomain ),
            'menu_name'                     => __( 'Mediatypes', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Mediatype', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_medientyp_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => true,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'medientyp', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "medientyp", array( "material" ), apply_filters( 'materialpool_medientyp_taxonomy_args', $args ) );

    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_taxonomy_metadata() {
        $cmb = new_cmb2_box( array(
            'id'            => 'cmb_medientyp',
            'title'         => __( 'Medientyp', Materialpool::get_textdomain() ),
            'object_types'     => array( 'term' ),
            'taxonomies'       => array( 'medientyp' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ) );

        // Regular text field
        $cmb->add_field( array(
            'name'    => _x( 'Sortorder', 'Medientyp Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'sortorder for taxonomy', 'Medientyp Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'         =>  'medientyp_sort',
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
        $columns[ 'medientyp_sort' ] = __( 'Sort order', Materialpool::get_textdomain() );
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
            case 'medientyp_sort':
                $out .= get_term_meta(  $term_id, 'medientyp_sort', true );
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
        $sortable[ 'medientyp_sort' ] = 'medientyp_sort';
        return $sortable;

    }
}
