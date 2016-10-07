<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Lizenz {
    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_lizenz_taxonomy_label
     * @filters materialpool_lizenz_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name" => __( 'License', Materialpool::$textdomain ),
            "singular_name" => __( 'License', Materialpool::$textdomain ),
            'search_items'                  => __( 'Search license', Materialpool::$textdomain ),
            'popular_items'                 => __( 'Popular license', Materialpool::$textdomain ),
            'all_items'                     => __( 'All license', Materialpool::$textdomain ),
            'parent_item'                   => null,
            'parent_item_colon'             => null,
            'edit_item'                     => __( 'Edit license', Materialpool::$textdomain ),
            'update_item'                   => __( 'Update license', Materialpool::$textdomain ),
            'add_new_item'                  => __( 'Add New license', Materialpool::$textdomain ),
            'new_item_name'                 => __( 'New license Name', Materialpool::$textdomain ),
            'separate_items_with_commas'    => __( 'Separate license with commas', Materialpool::$textdomain ),
            'add_or_remove_items'           => __( 'Add or remove license', Materialpool::$textdomain ),
            'choose_from_most_used'         => __( 'Choose from the most used license', Materialpool::$textdomain ),
            'not_found'                     => __( 'No license found.', Materialpool::$textdomain ),
            'menu_name'                     => __( 'License', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'License', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_lizenz_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'lizenz', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "lizenz", array( "material" ), apply_filters( 'materialpool_lizenz_taxonomy_args', $args ) );

    }
}
