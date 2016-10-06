<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo cpt auflistung anpassen
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

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_material_meta_field
     *
     */
    static public function register_meta_fields()
    {
        $cmb_material = new_cmb2_box(array(
            'id' => 'cmb_material',
            'title' => __('Materialdata', Materialpool::get_textdomain()),
            'object_types' => array('material'),
            'context' => 'normal',
            'priority' => 'core',
            'show_names' => true,
            'cmb_styles' => true,
            'closed' => false,
        ) );

        $cmb_material->add_field(array(
            'name' => _x('Shortdescription', 'Material Editpage Fieldname', Materialpool::get_textdomain()),
            'desc' => _x('Shortdescription of material', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            'default' => '',
            'id' => 'material_shortdescription',
            'type' => 'textarea',
        ) );

        $cmb_material->add_field(array(
            'name' => _x('Description', 'Material Editpage Fieldname', Materialpool::get_textdomain()),
            'desc' => _x('Description of material', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            'default' => '',
            'id' => 'material_description',
            'type' => 'textarea',
        ) );

        $cmb_material->add_field(array(
            'name' => _x('Release Date', 'Material Editpage Fieldname', Materialpool::get_textdomain()),
            'desc' => _x('Release Date of material', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            'default' => '',
            'id' => 'material_releasedate',
            'type' => 'text_date',
            'date_format' => 'd.m.Y'
        ) );

        $cmb_material->add_field(array(
            'name' => _x('Depublication Date', 'Material Editpage Fieldname', Materialpool::get_textdomain()),
            'desc' => _x('Depublicarion Date of material', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            'default' => '',
            'id' => 'material_depublicationdate',
            'type' => 'text_date',
            'date_format' => 'd.m.Y'
        ) );

        $cmb_material->add_field(array(
            'name' => _x('Review Date', 'Material Editpage Fieldname', Materialpool::get_textdomain()),
            'desc' => _x('Review Date of material', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            'default' => '',
            'id' => 'material_reviewdate',
            'type' => 'text_date',
            'date_format' => 'd.m.Y'
        ) );

        $cmb_material->add_field(array(
            'name' => _x('Create Date', 'Material Editpage Fieldname', Materialpool::get_textdomain()),
            'desc' => _x('Create Date of material', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            'default' => '',
            'id' => 'material_createdate',
            'type' => 'text_date',
            'date_format' => 'd.m.Y'
        ) );

        $cmb_material->add_field(array(
            'name' => _x('Material URL', 'Material Editpage Fieldname', Materialpool::get_textdomain()),
            'desc' => _x('URL of material', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            'default' => '',
            'id' => 'material_url',
            'type' => 'text_url',
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Picture', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Picture from material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'   => 'maerial_picture',
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

        $cmb_material->add_field( array(
            'name' => 'Autor',
            'desc' => 'Hier fehlt die zuordnung zu Autoren',
            'type' => 'title',
            'id'   => 'material_autor'
        ) );

        $cmb_material->add_field( array(
            'name' => 'Organisation',
            'desc' => 'Hier fehlt die zuordnung zu Organisationen',
            'type' => 'title',
            'id'   => 'material_organisation'
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Werk', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Werk from material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'   => 'material_werk',
            'type' => 'cpt_select',
            'cpt'  => 'material',
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Reference', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Reference to material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'   => 'material_reference',
            'type' => 'cpt_select',
            'cpt'  => 'material',
        ) );

        //@todo Hierachische Taxonomien sollen entsprechend eingerückt sein.
        $cmb_material->add_field( array(
            'name' => _x( 'Mediatyp', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Mediatyp of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_mediatyp',
            'taxonomy'  => 'medientyp',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Verfügbarkeit', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Verfügbarkeit of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_verfuegbarkeit',
            'taxonomy'  => 'verfuegbarkeit',
            'type'      => 'taxonomy_select',
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Bildungsstufe', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Bildungsstuge of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_bildungsstufe',
            'taxonomy'  => 'bildungsstufe',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Zugänglichkeit', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Zugänglichkeit of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_zugaenglichkeit',
            'taxonomy'  => 'zugaenglichkeit',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Inklusion', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Inklusion of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_inklusion',
            'taxonomy'  => 'inklusion',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Language', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Language of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_language',
            'taxonomy'  => 'sprache',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Lizenz', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Lizenz of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_lizenz',
            'taxonomy'  => 'lizenz',
            'type'      => 'taxonomy_select',
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Altersstufe', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Altersstufe of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_altersstufe',
            'taxonomy'  => 'altersstufe',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => 'Sorry, no terms could be found.' // Change default text. Default: "No terms"
            ),
        ) );

        $cmb_material = apply_filters( 'materialpool_material_meta_field', $cmb_material);
    }


}
