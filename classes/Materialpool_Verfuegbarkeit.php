<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Verfuegbarkeit {
    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_verfuegbarkeit_taxonomy_label
     * @filters materialpool_verfuegbarkeit_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name"                          => __( 'Disposability', Materialpool::$textdomain ),
            "singular_name"                 => __( 'Disposability', Materialpool::$textdomain ),
            'search_items'                  => __( 'Search disposabilitys', Materialpool::$textdomain ),
            'popular_items'                 => __( 'Popular disposabilitys', Materialpool::$textdomain ),
            'all_items'                     => __( 'All disposabilitys', Materialpool::$textdomain ),
            'parent_item'                   => null,
            'parent_item_colon'             => null,
            'edit_item'                     => __( 'Edit disposability', Materialpool::$textdomain ),
            'update_item'                   => __( 'Update disposability', Materialpool::$textdomain ),
            'add_new_item'                  => __( 'Add New disposability', Materialpool::$textdomain ),
            'new_item_name'                 => __( 'New disposability Name', Materialpool::$textdomain ),
            'separate_items_with_commas'    => __( 'Separate disposability with commas', Materialpool::$textdomain ),
            'add_or_remove_items'           => __( 'Add or remove disposability', Materialpool::$textdomain ),
            'choose_from_most_used'         => __( 'Choose from the most used disposabilitys', Materialpool::$textdomain ),
            'not_found'                     => __( 'No disposabilitys found.', Materialpool::$textdomain ),
            'menu_name'                     => __( 'Disposabilitys', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Disposability', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_verfuegbarkeit_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'verfuegbarkeit', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "verfuegbarkeit", array( "material" ), apply_filters( 'materialpool_verfuegbarkeit_taxonomy_args', $args ) );

    }
}