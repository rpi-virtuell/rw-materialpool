<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
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
            "name" => __( 'Bildungsstufen', Materialpool::$textdomain ),
            "singular_name" => __( 'Bildungsstufe', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Bildungsstufe', Materialpool::$textdomain ),
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
