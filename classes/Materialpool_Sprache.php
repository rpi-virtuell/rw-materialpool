<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
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
            "name" => __( 'Sprachen', Materialpool::$textdomain ),
            "singular_name" => __( 'Sprache', Materialpool::$textdomain ),
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
