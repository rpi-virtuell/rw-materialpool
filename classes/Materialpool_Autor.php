<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */


class Materialpool_Autor {

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_autor_posttype_label
     * @filters materialpool_autor_posttype_args
     *
     */
    static public function register_post_type() {
        $labels = array(
            "name" => __( 'Autoren', Materialpool::$textdomain ),
            "singular_name" => __( 'Autor', 'twentyfourteen' ),
        );

        $args = array(
            "label" => __( 'Autoren', Materialpool::$textdomain ),
            "labels" => apply_filters( 'materialpool_autor_posttype_label', $labels ),
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
            "rewrite" => array( "slug" => "autor", "with_front" => true ),
            "query_var" => true,
            "supports" => false,
        );
        register_post_type( "autor", apply_filters( 'materialpool_autor_posttype_args', $args ) );

    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function register_meta_fields() {
        $cmb_author = new_cmb2_box( array(
            'id'            => 'cmb_autor',
            'title'         => __( 'Autor', Materialpool::get_textdomain() ),
            'object_types'  => array( 'autor' ),
            'context'       => 'normal',
            'priority'      => 'core',
            'show_names'    => true,
            'cmb_styles' => true,
            'closed'     => false,
        ) );


        $cmb_author->add_field( array(
            'name'    => _x( 'Firstname', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'firstname of author', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
            'default' => '',
            'id'      => 'autor_firstname',
            'type'    => 'text',
        ) );

        $cmb_author->add_field( array(
            'name'    => _x( 'lastname', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'lastname of author', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
            'default' => '',
            'id'      => 'autor_lastname',
            'type'    => 'text',
        ) );

        $cmb_author->add_field( array(
            'name'    => _x( 'URL', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'Website of author', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
            'default' => '',
            'id'      => 'autor_url',
            'type'    => 'text_url',
        ) );

        $cmb_author->add_field( array(
            'name'    => _x( 'Email', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'email of author (for gravatar)', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
            'default' => '',
            'id'      => 'autor_email',
            'type'    => 'text_email',
        ) );

        $cmb_author->add_field( array(
            'name' => _x( 'Picture', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Picture from author', 'Author Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'   => 'autor_picture',
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

        $cmb_author->add_field( array(
            'name' => 'Organisaion',
            'desc' => 'Hier fehlt die zuordnung zu Organisationen',
            'type' => 'title',
            'id'   => 'autor_orga'
        ) );

        $cmb_author->add_field( array(
            'name' => 'Material',
            'desc' => 'Hier fehlt die Auflistung von Materialien des Autors',
            'type' => 'title',
            'id'   => 'autor_material'
        ) );
    }

}
