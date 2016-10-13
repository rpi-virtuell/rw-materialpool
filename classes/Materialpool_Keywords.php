<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo labels vervollständigen
 */


class Materialpool_Keywords {

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_keywords_taxonomy_label
     * @filters materialpool_keyword_taxonomy_args
     */
    static public function register_taxonomy() {
        $labels = array(
            "name" => __( 'Keywords', Materialpool::$textdomain ),
            "singular_name" => __( 'Keywords', Materialpool::$textdomain ),
        );

        $args = array(
            "label" => __( 'Keywords', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_keywords_taxonomy_label', $labels ),
            "public" => true,
            "hierarchical" => false,
            "show_ui" => true,
            "show_in_menu" => true,
            "show_in_nav_menus" => true,
            "query_var" => true,
            "rewrite" => array( 'slug' => 'keywords', 'with_front' => true, ),
            "show_admin_column" => true,
            "show_in_rest" => false,
            "rest_base" => "",
            "show_in_quick_edit" => false,
            "meta_box_cb" => false,
        );
        register_taxonomy( "keywords", array( "material" ), apply_filters( 'materialpool_keywords_taxonomy_args', $args ) );

    }
}
