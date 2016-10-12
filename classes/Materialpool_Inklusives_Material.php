<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Inklusives_Material {
    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_inklusives_material_taxonomy_label
     * @filters materialpool_inklusives_material_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name"                          => __( 'Inclusion', Materialpool::$textdomain ),
            "singular_name"                 => __( 'Inclusion', Materialpool::$textdomain ),
            'search_items'                  => __( 'Search inclusion', Materialpool::$textdomain ),
            'popular_items'                 => __( 'Popular inclusion', Materialpool::$textdomain ),
            'all_items'                     => __( 'All inclusions', Materialpool::$textdomain ),
            'parent_item'                   => null,
            'parent_item_colon'             => null,
            'edit_item'                     => __( 'Edit inclusion', Materialpool::$textdomain ),
            'update_item'                   => __( 'Update inclusion', Materialpool::$textdomain ),
            'add_new_item'                  => __( 'Add New inclusion', Materialpool::$textdomain ),
            'new_item_name'                 => __( 'New inclusion Name', Materialpool::$textdomain ),
            'separate_items_with_commas'    => __( 'Separate inclusion with commas', Materialpool::$textdomain ),
            'add_or_remove_items'           => __( 'Add or remove inclusion', Materialpool::$textdomain ),
            'choose_from_most_used'         => __( 'Choose from the most used inclusion', Materialpool::$textdomain ),
            'not_found'                     => __( 'No inclusion found.', Materialpool::$textdomain ),
            'menu_name'                     => __( 'Inclusion', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Inclusion', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_inklusives_material_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'inklusion', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "inklusion", array( "material" ), apply_filters( 'materialpool_inklusives_material_taxonomy_args', $args ) );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_taxonomy_metadata() {
        $cmb = new_cmb2_box( array(
            'id'            => 'cmb_inklusion',
            'title'         => __( 'Inklusion', Materialpool::get_textdomain() ),
            'object_types'     => array( 'term' ),
            'taxonomies'       => array( 'inklusion' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ) );

        // Regular text field
        $cmb->add_field( array(
            'name'    => _x( 'Sortorder', 'Inklusion Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'sortorder for taxonomy', 'Inklusion Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'         =>  'inklusion_sort',
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
        $columns[ 'inklusion_sort' ] = __( 'Sort order', Materialpool::get_textdomain() );
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
            case 'inklusion_sort':
                $out .= get_term_meta(  $term_id, 'inklusion_sort', true );
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
        $sortable[ 'inklusion_sort' ] = 'inklusion_sort';
        return $sortable;

    }

}



