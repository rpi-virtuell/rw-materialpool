<?php
/**
 *
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 *
 */


class Materialpool_Spezial {

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function load_template($template) {
        global $post;

        if ($post->post_type == "spezial"){
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-spezial.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-spezial.php';
                }
            }
            if ( is_archive() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/archive-spezial.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/archive-spezial.php';
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
        $columns[ 'spezial-bildungsstufe' ] = _x( 'Bildungsstufe', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'spezial-owner' ] = _x( 'Eintrager', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'spezial-schlagworte' ] = _x( 'Schlagworte', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'spezial-medientyp' ] = _x( 'Medientyp', 'Material list field',  Materialpool::$textdomain );
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
        $data = '';
        if ( $column_name == 'spezial-medientyp' ) {
            $medientyp = get_metadata( 'post', $post_id, 'spezial_medientyp' );
            if ( sizeof( $medientyp ) == 1 ) {
                if ( $medientyp[ 0 ] !== false ) {
                    $data .= $medientyp[ 0 ][ 'name' ] .'<br>';
                } else {
                    $data = "";
                }
            } else {
                foreach ( $medientyp as $medien ) {
                    $data .= $medien[ 'name' ] .'<br>';
                }
            }
        }
        if ( $column_name == 'spezial-bildungsstufe' ) {
            $bildungsstufe = get_metadata( 'post', $post_id, 'spezial_bildungsstufe' );
            if ( sizeof( $bildungsstufe ) == 1 ) {
                if ( $bildungsstufe[ 0 ] !== false ) {
                    $data .= $bildungsstufe[ 0 ][ 'name' ] .'<br>';
                } else {
                    $data = "";
                }
            } else {
                foreach ( $bildungsstufe as $bildung ) {
                    $data .= $bildung[ 'name' ] .'<br>';
                }
            }
        }
        if ( $column_name == 'spezial-owner' ) {
            $post = get_post( $post_id);
            $user = get_user_by( 'ID', $post->post_author );
            $data = $user->display_name;
        }
        if ( $column_name == 'spezial-schlagworte' ) {
            $schlagworte = get_metadata( 'post', $post_id, 'spezial_schlagworte' );
            if ( sizeof( $schlagworte ) == 1 ) {
                if ( $schlagworte[ 0 ] !== false ) {
                    $data .= $schlagworte[ 0 ][ 'name' ] .'<br>';
                } else {
                    $data = "";
                }
            } else {
                foreach ( $schlagworte as $schlagwort ) {
                    $data .= $schlagwort[ 'name' ] .'<br>';
                }
            }
        }
        echo $data;
    }


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function title() {
        echo Materialpool_Spezial::get_title();
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
    static public function content() {
        echo Materialpool_Spezial::get_content();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_content() {
        global $post;

        return $post->post_content;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function releasedate() {
        echo Materialpool_Spezial::get_releasedate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_releasedate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'spezial_veroeffentlichungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function depublicationdate() {
        echo Materialpool_Spezial::get_depublicationdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_depublicationdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'spezial_depublizierungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function reviewdate() {
        echo Materialpool_Spezial::get_reviewdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_reviewdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'spezial_wiedervorlagedatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function createdate() {
        echo Materialpool_Spezial::get_createdate();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_createdate() {
        global $post;

        return get_metadata( 'post', $post->ID, 'spezial_erstellungsdatum', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function url() {
        echo Materialpool_Spezial::get_url();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-spezial-url
     */
    static public function url_html() {
        $url = Materialpool_Spezial::get_url();
        echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-spezial-url', 'materialpool-template-spezial-url' ) .'">' . $url . '</a>';
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_url() {
        global $post;

        return get_metadata( 'post', $post->ID, 'spezial_url', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function picture() {
        echo Materialpool_Spezial::get_picture();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool-template-spezial-picture
     *
     */
    static public function picture_html() {
        $pic  = Materialpool_Spezial::get_picture();
        if ( is_array( $pic ) ) {
            $url = wp_get_attachment_url( $pic[ 'ID' ] );
            echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-spezial-picture', 'materialpool-template-spezial-picture' ) .'"/>';
        }
        $url = Materialpool_Spezial::get_picture_url();
        if ( $url != '') {
            echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-spezial-picture', 'materialpool-template-spezial-picture' ) .'"/>';
        }

    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_picture() {
        global $post;

        return get_metadata( 'post', $post->ID, 'spezial_cover', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_picture_url() {
        global $post;

        return get_metadata( 'post', $post->ID, 'spezial_cover_url', true );
    }

}
