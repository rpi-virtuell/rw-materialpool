<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Bildungsstufe {
    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_bildungsstufe_taxonomy_label
     * @filters materialpool_bildungsstufe_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name"                          => __( 'Educational levels', Materialpool::$textdomain ),
            "singular_name"                 => __( 'Educational level', Materialpool::$textdomain ),
            'search_items'                  => __( 'Search educational level', Materialpool::$textdomain ),
            'popular_items'                 => __( 'Popular educational level', Materialpool::$textdomain ),
            'all_items'                     => __( 'All educational levels', Materialpool::$textdomain ),
            'parent_item'                   => null,
            'parent_item_colon'             => null,
            'edit_item'                     => __( 'Edit educational level', Materialpool::$textdomain ),
            'update_item'                   => __( 'Update educational level', Materialpool::$textdomain ),
            'add_new_item'                  => __( 'Add New educational level', Materialpool::$textdomain ),
            'new_item_name'                 => __( 'New language Name', Materialpool::$textdomain ),
            'separate_items_with_commas'    => __( 'Separate educational level with commas', Materialpool::$textdomain ),
            'add_or_remove_items'           => __( 'Add or remove educational level', Materialpool::$textdomain ),
            'choose_from_most_used'         => __( 'Choose from the most used educational levels', Materialpool::$textdomain ),
            'not_found'                     => __( 'No educational level found.', Materialpool::$textdomain ),
            'menu_name'                     => __( 'Educational levels', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Educational level', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_bildungsstufe_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => true,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'bildungsstufe', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "bildungsstufe", array( "material" ), apply_filters( 'materialpool_bildungsstufe_taxonomy_args', $args ) );

    }
}
