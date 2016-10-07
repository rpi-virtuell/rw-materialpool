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
}
