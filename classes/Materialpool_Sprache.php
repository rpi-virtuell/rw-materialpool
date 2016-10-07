<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Sprache {
    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_sprache_taxonomy_label
     * @filters materialpool_sprache_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name"                          => __( 'Languages', Materialpool::$textdomain ),
            "singular_name"                 => __( 'Language', Materialpool::$textdomain ),
            'search_items'                  => __( 'Search languages', Materialpool::$textdomain ),
            'popular_items'                 => __( 'Popular languages', Materialpool::$textdomain ),
            'all_items'                     => __( 'All languages', Materialpool::$textdomain ),
            'parent_item'                   => null,
            'parent_item_colon'             => null,
            'edit_item'                     => __( 'Edit language', Materialpool::$textdomain ),
            'update_item'                   => __( 'Update language', Materialpool::$textdomain ),
            'add_new_item'                  => __( 'Add New language', Materialpool::$textdomain ),
            'new_item_name'                 => __( 'New language Name', Materialpool::$textdomain ),
            'separate_items_with_commas'    => __( 'Separate language with commas', Materialpool::$textdomain ),
            'add_or_remove_items'           => __( 'Add or remove language', Materialpool::$textdomain ),
            'choose_from_most_used'         => __( 'Choose from the most used languages', Materialpool::$textdomain ),
            'not_found'                     => __( 'No languages found.', Materialpool::$textdomain ),
            'menu_name'                     => __( 'Languages', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Sprachen', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_sprache_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'sprache', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "sprache", array( "material" ), apply_filters( 'materialpool_sprache_taxonomy_args', $args ) );

    }
}
