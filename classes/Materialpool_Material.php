<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */


class Materialpool_Material {

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @filters materialpool_material_posttype_label
	 * @filters materialpool_material_posttype_args
	 *
	 */
	static public function register_post_type() {
		$labels = array(
			"name" => __( 'Material', Materialpool::$textdomain ),
			"singular_name" => __( 'Material', 'twentyfourteen' ),
		);

		$args = array(
			"label" => __( 'Material', Materialpool::$textdomain ),
			"labels" => apply_filters( 'materialpool_material_posttype_label', $labels ),
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => false,
			"show_in_menu" => true, //'materialpool',
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "material", "with_front" => true ),
			"query_var" => true,
			"supports" => array( "title" ),
		);
		register_post_type( "material", apply_filters( 'materialpool_material_posttype_args', $args ) );

	}



}
