<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 * @todo post_title beim Speichern aus VornameNachname generieren
 * @todo cpt auflistung anpassen
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
            "has_archive" => 'autor',
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
     * @filters materialpool_autor_meta_field
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
            'name'    => _x( 'RPI BuddyPress Username', 'Author Editpage Fieldname', Materialpool::get_textdomain() ),
            'desc'    => _x( 'username of author on rpi buddypress', 'Organisation Editpage Fielddescription', Materialpool::get_textdomain() ),
            'default' => '',
            'id'      => 'autor_buddypress',
            'type'    => 'text',
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

        $cmb_author = apply_filters( 'materialpool_autor_meta_field', $cmb_author);
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     */
    static public function load_template($template) {
        global $post;

        if ($post->post_type == "autor"){
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-autor.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-autor.php';
                }
            }
            if ( is_archive() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/archive-autor.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/archive-autor.php';
                }
            }
            return $template_path;
        }
        return $template;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function firstname() {
        echo Materialpool_Autor::get_firstname();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_firstname() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_firstname', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function lastname() {
        echo Materialpool_Autor::get_lastname();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_lastname() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_lastname', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function url() {
        echo Materialpool_Autor::get_url();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-autor-url
     */
    static public function url_html() {
        $url = Materialpool_Autor::get_url();
        echo '<a href="' . $url . '" class="'. apply_filters( '', 'materialpool-template-autor-url' ) .'">' . $url . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_url() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function buddypress() {
        echo Materialpool_Autor::get_buddypress();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-buddypress-member-url
     * @filters materialpool-template-autor-buddypress-url
     */
    static public function buddypress_html() {
        $name = Materialpool_Autor::get_buddypress();
        echo '<a href="' . apply_filters( 'materialpool-buddypress-member-url', Materialpool::$buddypress_member_url ) . $name  . '" class="'. apply_filters( 'materialpool-template-autor-buddypress-url', 'materialpool-autor-buddypress-url' ) .'">' . $name . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_buddypress() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_buddypress', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function email() {
        echo Materialpool_Autor::get_email();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-autor-email
     */
    static public function email_html() {
        $email = Materialpool_Autor::get_email();
        echo '<a href="mailto:' . $email . '" class="'. apply_filters( 'materialpool-template-autor-email', 'materialpool-autor-email' ) .'">' . $email . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_email() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_email', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function picture() {
        echo Materialpool_Autor::get_picture();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-autor-pic
     *
     */
    static public function picture_html() {
        $url = Materialpool_Autor::get_picture();
        echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-autor-pic', 'materialpool-autor-pic' ) .'"/>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_picture() {
        global $post;

        return get_metadata( 'post', $post->ID, 'autor_picture', true );
    }



}
