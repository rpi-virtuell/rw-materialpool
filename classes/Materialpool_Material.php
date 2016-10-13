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
			"name"                  => __( 'Material', Materialpool::$textdomain ),
			"singular_name"         => __( 'Material', Materialpool::$textdomain ),
            'menu_name'             => _x( 'Material', 'admin menu', Materialpool::$textdomain ),
            'name_admin_bar'        => _x( 'Material', 'add new on admin bar', Materialpool::$textdomain ),
            'add_new'               => _x( 'Add New', 'Material', Materialpool::$textdomain ),
            'add_new_item'          => __( 'Add New Material', Materialpool::$textdomain ),
            'new_item'              => __( 'New Material', Materialpool::$textdomain ),
            'edit_item'             => __( 'Edit Material', Materialpool::$textdomain ),
            'view_item'             => __( 'View Material', Materialpool::$textdomain ),
            'all_items'             => __( 'All Material', Materialpool::$textdomain ),
            'search_items'          => __( 'Search Material', Materialpool::$textdomain ),
            'parent_item_colon'     => __( 'Parent Material:', Materialpool::$textdomain ),
            'not_found'             => __( 'No Material found.', Materialpool::$textdomain ),
            'not_found_in_trash'    => __( 'No Material found in Trash.', Materialpool::$textdomain )
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
			"has_archive" => 'material',
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
            'type' => 'wysiwyg',
        ) );

        $cmb_material->add_field( array(
            'name' => _x('Keywords', 'Material Editpage Fieldname', Materialpool::get_textdomain()),
            'desc' => _x('Keywords of material', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            'id' => 'material_keywords',
            'taxonomy'  => 'keywords',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => _x('Sorry, no terms could be found.', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => 'Schlagwort Vorschläge',
            'desc' => 'Die Schlagwortdialog oben muss noch umgebaut werden, liste der zugeordneten Keywords und texteingabe mit vorschlägen beim tippen. Hier ercheinen die Schlagwortvorschläge und mann kann sie als Schlagwort übernehmen oder in die Synonymliste aufnehmen.',
            'type' => 'title',
            'id'   => 'wiki_test_title'
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
            'id'   => 'material_picture',
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
                'no_terms_text' => _x('Sorry, no terms could be found.', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
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
                'no_terms_text' => _x('Sorry, no terms could be found.', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Zugänglichkeit', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Zugänglichkeit of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_zugaenglichkeit',
            'taxonomy'  => 'zugaenglichkeit',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => _x('Sorry, no terms could be found.', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Inklusion', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Inklusion of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_inklusion',
            'taxonomy'  => 'inklusion',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => _x('Sorry, no terms could be found.', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            ),
        ) );

        $cmb_material->add_field( array(
            'name' => _x( 'Language', 'Material Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc' => _x( 'Language of material', 'Material Editpage Fielddescription', Materialpool::get_textdomain() ),
            'id'        => 'material_language',
            'taxonomy'  => 'sprache',
            'type'      => 'taxonomy_multicheck',
            'text'      => array(
                'no_terms_text' => _x('Sorry, no terms could be found.', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
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
                'no_terms_text' => _x('Sorry, no terms could be found.', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain()),
            ),
        ) );

        $cmb_material = apply_filters( 'materialpool_material_meta_field', $cmb_material);
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function load_template($template) {
        global $post;

        if ($post->post_type == "material"){
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-material.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-material.php';
                }
            }
            if ( is_archive() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/archive-material.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/archive-material.php';
                }
            }
            return $template_path;
        }
        return $template;
    }

    /**
     * Change the columns for list table
     *
     * @since   0.0.1
     * @access	public
     * @var     array    $columns    Array with columns
     * @return  array
     */
    static public function cpt_list_head( $columns ) {
        $columns[ 'organisation-name' ] = _x( 'Organisation', 'Organisation list field',  Materialpool::$textdomain );
        $columns[ 'autor-name' ] = _x( 'Autoren', 'Organisation list field',  Materialpool::$textdomain );
        return $columns;
    }

    /**
     * Add content for the custom columns in list table
     *
     * @since   0.0.1
     * @access	public
     * @var     string  $column_name    name of the current column
     * @var     int     $post_id        ID of the current post
     */
    static public function cpt_list_column( $column_name, $post_id ) {
        if ( $column_name == 'organisation-name' ) {
            $data = "name der zugeordneten Orgas";
        }
        if ( $column_name == 'autor-name' ) {
            $data = "name der zugeordneten Autoren";
        }
        echo $data;
    }

    /**
     * Set the sortable columns
     *
     * @since   0.0.1
     * @access	public
     * @param   array   $columns    array with the default sortable columns
     * @return  array   Array with sortable columns
     */
    static public function cpt_sort_column( $columns ) {
        return array_merge( $columns, array(
            'taxonomy-lizenz' => 'taxonomy-lizenz',
            'taxonomy-verfuegbarkeit' => 'taxonomy-verfuegbarkeit',
        ) );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function title() {
        echo Materialpool_Material::get_title();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_title() {
        global $post;

        return $post->post_title;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function shortdecription() {
        echo Materialpool_Material::get_shortdescription();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_shortdescription() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_shortdescription', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function decription() {
        echo Materialpool_Material::get_description();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_description() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_description', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function releasedate() {
        echo Materialpool_Material::get_releasedate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_releasedate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_releasedate', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function depublicationdate() {
        echo Materialpool_Material::get_depublicationdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_depublicationdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_depublicationdate', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function reviewdate() {
        echo Materialpool_Material::get_reviewdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_reviewdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_reviewdate', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function createdate() {
        echo Materialpool_Material::get_createdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_createdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_createdate', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function url() {
        echo Materialpool_Material::get_url();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-material-url
     */
    static public function url_html() {
        $url = Materialpool_Material::get_url();
        echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-url', 'materialpool-template-material-url' ) .'">' . $url . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_url() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function picture() {
        echo Materialpool_Material::get_picture();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-material-picture
     *
     */
    static public function picture_html() {
        $url = Materialpool_Material::get_picture();
        echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_picture() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_picture', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function werk() {
        $werkID =  Materialpool_Material::get_werk_id();
        $werk = get_post( $werkID );
        echo $werk->post_title;
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-werk
     *
     */
    static public function werk_html() {
        $werkID =  Materialpool_Material::get_werk_id();
        if ( $werkID != '' ) {
            $werk = get_post( $werkID );
            $url = get_permalink( $werkID );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-werk', 'materialpool-template-material-werk' ) .'">' . $werk->post_title . '</a>';
        }
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_werk_id() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_werk', true );
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     * @todo
     */
    static public function is_werk() {
        global $post;
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare("SELECT count(post_id) as count FROM $wpdb->postmeta WHERE meta_key = %s and meta_value = %s", 'material_werk', $post->ID ) );
        if ( is_object( $result)  && $result->count == 0 ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     * @todo
     */
    static public function is_part_of_werk() {
        global $post;
        global $wpdb;
        $result = $wpdb->get_row( $wpdb->prepare("SELECT count(post_id) as count FROM $wpdb->postmeta WHERE meta_key = %s and post_id = %s", 'material_werk', $post->ID ) );
        if ( is_object( $result)  && $result->count == 0 ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     * @todo
     */
    static public function volumes() {
        global $wpdb;
        global $post;

        if ( self::is_werk() ) {
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s order by post_title asc" , 'material_werk', $post->ID ) );
            foreach ( $result as $material ) {
                echo $material->post_title . '<br>';
            }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-volumes
     *
     * @todo
     */
    static public function volumes_html() {
        global $wpdb;
        global $post;

        if ( self::is_werk() ) {
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s order by post_title asc" , 'material_werk', $post->ID ) );
            foreach ( $result as $material ) {
                $url = get_permalink( $material->ID );
                echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-volumes', 'materialpool-template-material-volumes' ) .'">' . $material->post_title . '</a><br>';
            }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function sibling_volumes ( $selfinclude = false ) {
        global $wpdb;
        global $post;

        if ( self::is_part_of_werk() ) {
            $werk = self::get_werk_id();
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s order by post_title asc" , 'material_werk', $werk ) );
            foreach ( $result as $material ) {
                if ( ! $selfinclude ) {
                    if ( $material->ID == $post->ID ) {
                        break;
                    }
                }
                echo $material->post_title . '<br>';
            }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-sibling
     *
     */
    static public function sibling_volumes_html ( $selfinclude = false ) {
        global $wpdb;
        global $post;

        if ( self::is_part_of_werk() ) {
            $werk = self::get_werk_id();
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s order by post_title asc" , 'material_werk', $werk ) );
            foreach ( $result as $material ) {
                if ( ! $selfinclude ) {
                    if ( $material->ID == $post->ID ) {
                        break;
                    }
                }
                $url = get_permalink( $material->ID );
                echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-sibling', 'materialpool-template-material-sibling' ) .'">' . $material->post_title . '</a><br>';
            }
        }
    }


}
