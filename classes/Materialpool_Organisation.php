<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo cpt auflistung anpassen
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
			"singular_name" => __( 'Organisation', Materialpool::$textdomain ),
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
			"has_archive" => "organisation",
			"show_in_menu" => true,
			"exclude_from_search" => false,
			"capability_type" => "post",
			"map_meta_cap" => true,
			"hierarchical" => false,
			"rewrite" => array( "slug" => "organisation", "with_front" => true ),
			"query_var" => true,
			"supports" => array( "title" ),
		);
		register_post_type( "organisation", apply_filters( 'materialpool_organisation_posttype_args', $args ) );

	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
     * @filters materialpool_organisation_meta_field
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
            'name'    => _x( 'Logo URL', 'Organisation Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'URL of organisation logo', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
            'default' => '',
            'id'      => 'organisation_logo_url',
            'type'    => 'text_url',
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

        $cmb_organisation->add_field( array(
            'name' => 'Autor',
            'desc' => 'Hier fehlt die zuordnung zu Autoren',
            'type' => 'title',
            'id'   => 'organisation_autor'
        ) );

        $cmb_organisation->add_field( array(
            'name' => 'Material',
            'desc' => 'Hier fehlt die Auflistung von Materialien des Autors',
            'type' => 'title',
            'id'   => 'organisation_material'
        ) );

        $cmb_organisation = apply_filters( 'materialpool_organisation_meta_field', $cmb_organisation);
	}

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function load_template($template) {
        global $post;

        if ($post->post_type == "organisation"){
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-organisation.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-organisation.php';
                }
            }
            if ( is_archive() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/archive-organisation.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/archive-organisation.php';
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
        unset( $columns );
        $columns = array(
            'organisation-id' => _x( 'ID', 'Organisation list field',  Materialpool::$textdomain ),
            'title' => _x( 'Organisation', 'Organisation list field',  Materialpool::$textdomain ),
            'organisation_logo_url' => _x( 'Logo', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_url' => _x( 'URL', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_konfession' => _x( 'Konfession', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_alpika' => _x( 'ALPIKA', 'Organisation list field', Materialpool::$textdomain ),
            'date' => __('Date'),
            'organisation_autoren' => _x( '#Autoren', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_material' => _x( '#Material', 'Organisation list field', Materialpool::$textdomain ),
            'organisation_owner' => _x( 'Eintrager', 'Organisation list field', Materialpool::$textdomain ),
        );
        return $columns;
    }

    /**
     * Add content for the custom columns in list table
     *
     * @since   0.0.1
     * @access	public
     * @var     string  $column_name    name of the current column
     * @var     int     $post_id        ID of the current post
     * @filters materialpool-admin-organisation-pic-class
     */
    static public function cpt_list_column( $column_name, $post_id ) {
        $data = '';
        if ( $column_name == 'organisation-id' ) {
            $data = $post_id;
        }
        if ( $column_name == 'organisation_logo_url' ) {
            $url = get_metadata( 'post', $post_id, 'organisation_logo_url', true );
            if ( $url !== false ) {
                $data = "<img src='". $url ."' class='". apply_filters( 'materialpool-admin-organisation-pic-class', 'materialpool-admin-organisation-pic' ) ."'>";
            }
        }
        if ( $column_name == 'organisation_url' ) {
            $data = get_metadata( 'post', $post_id, 'organisation_url', true );
        }
        if ( $column_name == 'organisation_alpika' ) {
            $alpiika = get_metadata( 'post', $post_id, 'organisation_alpika', true );
            if ( $alpiika == '1' ) {
                $data = "<img src='". Materialpool::$plugin_url ."/assets/alpika.png'>";
            }
        }
        if ( $column_name == 'organisation_konfession' ) {
            $term = get_metadata( 'post', $post_id, 'organisation_konfession' );
            $data = $term[ 0 ][ 'name' ];
        }
        if ( $column_name == 'organisation_autoren' ) {
            $autors = get_metadata( 'post', $post_id, 'organisation_autor' );
            if ( sizeof( $autors ) == 1 ) {
                if ( $autors[ 0 ] !== false ) {
                    $data = "1";
                } else {
                    $data = "0";
                }
            } else {
                $data = sizeof( $autors );
            }
        }
        if ( $column_name == 'organisation_material' ) {
            $material = get_metadata( 'post', $post_id, 'organisation_material' );
            if ( sizeof( $material ) == 1 ) {
                if ( $material[ 0 ] !== false ) {
                    $data = "1";
                } else {
                    $data = "0";
                }
            } else {
                $data = sizeof( $material );
            }
        }
        if ( $column_name == 'organisation_owner' ) {
            $post = get_post( $post_id);
            $user = get_user_by( 'ID', $post->post_author );
            $data = $user->display_name;
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
            'organisation_url' => 'organisation_url',
            'organisation_alpika' => 'organisation_alpika',
            'organisation_konfession' => 'organisation_konfession',
        ) );
    }


	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function generate_title( $post_id ) {
        global $wpdb;
		$post_type = get_post_type($post_id);

		if ( "organisation" != $post_type ) return;

		$title = $_POST[ 'pods_meta_organisation_titel' ];

        $wpdb->update(
            $wpdb->posts,
            array(
                'post_title' => $title,
                'post_name' => sanitize_title( $title )
            ),
            array( 'ID' => $post_id ),
            array(
                '%s',
                '%s'
            ),
            array( '%d' )
        );
        $_POST[ 'post_title'] = $title;
	}


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function title() {
        echo Materialpool_Organisation::get_title();
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
    static public function url() {
        echo Materialpool_Organisation::get_url();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-organisation-url
     */
    static public function url_html() {
        $url = Materialpool_Organisation::get_url();
        echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-organisation-url', 'materialpool-template-organisation-url' ) .'">' . $url . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_url() {
        global $post;

        return get_metadata( 'post', $post->ID, 'organisation_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function logo() {
        echo Materialpool_Organisation::get_logo();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-organisation-logo
     *
     */
    static public function logo_html() {
        $url = Materialpool_Organisation::get_logo();
        echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-organisation-logo', 'materialpool-template-organisation-logo' ) .'"/>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_logo() {
        global $post;

        return get_metadata( 'post', $post->ID, 'organisation_logo_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function is_alpika() {
        global $post;

        $alpika = get_metadata( 'post', $post->ID, 'organisation_alpika', true );
        if ( $alpika == '1' ) {
            return true;
        }
        return false;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function konfession() {
        echo Materialpool_Organisation::get_konfession();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_konfession() {
        global $post;

        $konfession = get_metadata( 'post', $post->ID, 'organisation_konfession', true );
        if ( is_array( $konfession ) ) {
            return $konfession[ 'name'];
        }
    }


}
