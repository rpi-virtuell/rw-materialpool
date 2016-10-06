<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
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
            "name" => __( 'Zugänglichkeit', Materialpool::$textdomain ),
            "singular_name" => __( 'Zugänglichkeit', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Zugänglichkeit', Materialpool::$textdomain ),
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
}
