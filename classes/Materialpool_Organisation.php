<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */


class Materialpool_Organisation {

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @filters materialpool_organisation_posttype_label
	 * @filters materialpool_organisation_posttype_args
	 *
	 */
	static public function register_post_type() {
		$labels = array(
			"name" => __( 'Organisationen', Materialpool::$textdomain ),
			"singular_name" => __( 'Organisation', 'twentyfourteen' ),
		);

		$args = array(
			"label" => __( 'Organisationen', Materialpool::$textdomain ),
			"labels" => apply_filters( 'materialpool_organisation_posttype_label', $labels ),
			"description" => "",
			"public" => true,
			"publicly_queryable" => true,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => false,
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "organisation", "with_front" => true ),
			"query_var" => true,

			"supports" => array( "title" ),					);
		register_post_type( "organisation", apply_filters( 'materialpool_organisation_posttype_args', $args ) );

	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function register_meta_fields() {
		$cmb_organisation = new_cmb2_box( array(
			'id'            => 'cmb_organisation',
			'title'         => __( 'Organisationdata', Materialpool::get_textdomain() ),
			'object_types'  => array( 'organisation' ),
			'context'       => 'normal',
			'priority'      => 'core',
			'show_names'    => true,
			'cmb_styles' => true,
			'closed'     => false,
		) );

		$cmb_organisation->add_field( array(
			'name'    => _x( 'URL', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'Website of organisation', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'organisation_url',
			'type'    => 'text_url',
		) );

		$cmb_organisation->add_field( array(
			'name' => _x( 'Logo', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc' => _x( 'Logo from organisation', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'id'   => 'organisation_logo',
			'type' => 'file',
			'preview_size' => array( 350, 550 ),
			'options' => array(
				'add_upload_files_text' => 'Replacement',
				'remove_image_text' => 'Replacement',
				'file_text' => 'Replacement',
				'file_download_text' => 'Replacement',
				'remove_text' => 'Replacement',
			),
		) );

		$cmb_organisation->add_field( array(
			'name'    => _x( 'ALPIKA', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'    => _x( 'Organisation is ALPIKA', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'default' => '',
			'id'      => 'organisation_alpika',
			'type'    => 'checkbox',
		) );

		$cmb_organisation->add_field( array(
			'name'     => _x( 'Konfession', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
			'desc'     => _x( 'Konfession of organisation', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
			'id'       => 'organisation_konfession_select',
			'taxonomy' => 'konfession',
			'type'     => 'taxonomy_select',
		) );
	}

}
