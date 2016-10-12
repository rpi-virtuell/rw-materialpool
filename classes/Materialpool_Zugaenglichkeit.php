<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Zugaenglichkeit {
    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_zugaenglichkeit_taxonomy_label
     * @filters materialpool_zugaenglichkeit_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name"                          => __( 'Accessibility', Materialpool::$textdomain ),
            "singular_name"                 => __( 'Accessibility', Materialpool::$textdomain ),
            'search_items'                  => __( 'Search accessibility', Materialpool::$textdomain ),
            'popular_items'                 => __( 'Popular accessibility', Materialpool::$textdomain ),
            'all_items'                     => __( 'All accessibility', Materialpool::$textdomain ),
            'parent_item'                   => null,
            'parent_item_colon'             => null,
            'edit_item'                     => __( 'Edit accessibility', Materialpool::$textdomain ),
            'update_item'                   => __( 'Update accessibility', Materialpool::$textdomain ),
            'add_new_item'                  => __( 'Add New accessibility', Materialpool::$textdomain ),
            'new_item_name'                 => __( 'New accessibility Name', Materialpool::$textdomain ),
            'separate_items_with_commas'    => __( 'Separate accessibility with commas', Materialpool::$textdomain ),
            'add_or_remove_items'           => __( 'Add or remove accessibility', Materialpool::$textdomain ),
            'choose_from_most_used'         => __( 'Choose from the most used accessibility', Materialpool::$textdomain ),
            'not_found'                     => __( 'No accessibility found.', Materialpool::$textdomain ),
            'menu_name'                     => __( 'Accessibility', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Accessibility', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_zugaenglichkeit_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'zugaenglichkeit', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "zugaenglichkeit", array( "material" ), apply_filters( 'materialpool_zugaenglichkeit_taxonomy_args', $args ) );

    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_taxonomy_metadata() {
        $cmb = new_cmb2_box( array(
            'id'            => 'cmb_bzugaenglichkeit',
            'title'         => __( 'Zugaenglichkeit', Materialpool::get_textdomain() ),
            'object_types'     => array( 'term' ),
            'taxonomies'       => array( 'zugaenglichkeit' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_names'    => true,
        ) );

        // Regular text field
        $cmb->add_field( array(
            'name'    => _x( 'Sortorder', 'Zugänglichkeit Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'sortorder for taxonomy', 'Zugänglichkeit Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'         =>  'zugaenglichkeit_sort',
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
        $columns[ 'zugaenglichkeit_sort' ] = __( 'Sort order', Materialpool::get_textdomain() );
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
            case 'zugaenglichkeit_sort':
                $out .= get_term_meta(  $term_id, 'zugaenglichkeit_sort', true );
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
        $sortable[ 'zugaenglichkeit_sort' ] = 'zugaenglichkeit_sort';
        return $sortable;

    }
}
