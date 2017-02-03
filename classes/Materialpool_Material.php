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
     *
     */
    static public function load_template($template) {
        global $post;

        if ($post->post_type == "material" && !is_embed() ){
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
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_template_check_external_files ( $checkArray ) {
        $checkArray[ 'materialpool/single-material.php' ] = Materialpool::$plugin_base_dir . 'templates/single-material.php';
        $checkArray[ 'materialpool/archive-material.php'] = Materialpool::$plugin_base_dir . 'templates/archive-material.php';
        return $checkArray;
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
        $columns[ 'material-bildungsstufe' ] = _x( 'Bildungsstufe', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-owner' ] = _x( 'Eintrager', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-schlagworte' ] = _x( 'Schlagworte', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-organisation' ] = _x( 'Organisation', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-autor' ] = _x( 'Autoren', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-medientyp' ] = _x( 'Medientyp', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-online' ] = _x( 'Online', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-status' ] = _x( 'Status', 'Material list field',  Materialpool::$textdomain );
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
        if ( $column_name == 'material-autor' ) {
            $autors = get_metadata( 'post', $post_id, 'material_autoren' );
            if ( sizeof( $autors ) == 1 ) {
                if ( $autors[ 0 ] !== false ) {
                    $vorname = get_post_meta($autors[ 0 ][ 'ID' ], 'autor_vorname', true );
                    $nachname = get_post_meta($autors[ 0 ][ 'ID' ], 'autor_nachname', true );
                    $data .= '<a href="' . get_edit_post_link( $autors[ 0 ][ 'ID' ] ) . '">' . $vorname. ' '. $nachname .'</a><br>';
                } else {
                    $data = "";
                }
            } else {
                foreach ( $autors as $autor ) {
                    $vorname = get_post_meta($autor[ 'ID' ], 'autor_vorname', true );
                    $nachname = get_post_meta($autor[ 'ID' ], 'autor_nachname', true );
                    $data .= '<a href="' . get_edit_post_link( $autor[ 'ID' ] ) . '">' . $vorname. ' '. $nachname .'</a><br>';
                }
            }
        }
        if ( $column_name == 'material-medientyp' ) {
            $medientyp = get_metadata( 'post', $post_id, 'material_medientyp' );
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
        if ( $column_name == 'material-bildungsstufe' ) {
            $bildungsstufe = get_metadata( 'post', $post_id, 'material_bildungsstufe' );
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
        if ( $column_name == 'material-owner' ) {
            $post = get_post( $post_id);
            $user = get_user_by( 'ID', $post->post_author );
            $data = $user->display_name;
        }
        if ( $column_name == 'material-schlagworte' ) {
            $schlagworte = get_metadata( 'post', $post_id, 'material_schlagworte' );
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
        if ( $column_name == 'material-organisation' ) {
            $autors = get_metadata( 'post', $post_id, 'material_organisation' );
            if ( sizeof( $autors ) == 1 ) {
                if ( $autors[ 0 ] !== false ) {
                    $post = get_post( $autors[ 0 ][ 'ID' ] );
                    $data .= '<a href="' . get_edit_post_link( $autors[ 0 ][ 'ID' ] ) . '">' . $post->post_title .'</a><br>';
                } else {
                    $data = "";
                }
            } else {
                foreach ( $autors as $autor ) {
                    $post = get_post( $autor[ 'ID' ] );
                    $data .= '<a href="' . get_edit_post_link( $autor[ 'ID' ] ) . '">' . $post->post_title .'</a><br>';
                }
            }
        }
        if ( $column_name == 'material-status' ) {
            $labels =  get_post_status_object( get_post_status( $post_id) );
            $data = $labels->label;

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
	static public function generate_title( $post_id ) {
        global $wpdb;
		$post_type = get_post_type($post_id);
		$post_status = get_post_status ($post_id);
		$post_parent = wp_get_post_parent_id( $post_id );

		if ( "material" != $post_type ) return;

		$title = $_POST[ 'pods_meta_material_titel' ];
        $post_name = wp_unique_post_slug( sanitize_title( $title ), $post_id, $post_status, $post_type, $post_parent );

        $wpdb->update(
            $wpdb->posts,
            array(
                'post_title' => stripslashes( $title ),
                'post_name' => $post_name,
            ),
            array( 'ID' => $post_id ),
            array(
                '%s',
                '%s'
            ),
            array( '%d' )
        );

        $_POST[ 'post_title'] = $title;

        // Altersstufen des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'altersstufe' );
        $cats = $_POST[ 'pods_meta_material_altersstufe' ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( is_int( $cats ) ) {
            $cat_ids[] = $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'altersstufe', true );

        // Bildungsstufen des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'bildungsstufe' );
        $cats = $_POST[ 'pods_meta_material_bildungsstufe' ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( is_int( $cats ) ) {
            $cat_ids[] = $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'bildungsstufe', true );


        // Inklusion des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'inklusion' );
        $cats = $_POST[ 'pods_meta_material_inklusion' ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'inklusion', true );


        // Lizenz des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'lizenz' );
        $cats = $_POST[ 'pods_meta_material_lizenz' ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'lizenz', true );

        // Medientyp des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'medientyp' );
        $cats = $_POST[ 'pods_meta_material_medientyp' ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'medientyp', true );

        // Schlagwort des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'schlagwort' );
        $cats = explode( ',', $_POST[ 'pods_meta_material_schlagworte' ] ) ;
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'schlagwort', true );

        // Sprachen des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'sprache' );
        $cats = $_POST[ 'pods_meta_material_sprache' ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'sprache', true );

        // Verfügbarkeit des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'verfuegbarkeit' );
        $cats = $_POST[ 'pods_meta_material_verfuegbarkeit' ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'verfuegbarkeit', true );

        // Zugänglichkeit des Materials in term_rel speichern
        wp_delete_object_term_relationships( $post_id, 'zugaenglichkeit' );
        $cats = $_POST[ 'pods_meta_material_zugaenglichkeit' ];
        if ( is_array( $cats ) ) {
            foreach ( $cats as $key => $val ) {
                $cat_ids[] = (int) $val;
            }
        }
        if ( $cats!== null  ) {
            $cat_ids[] = (int) $cats;
        }
        wp_set_object_terms( $post_id, $cat_ids, 'zugaenglichkeit', true );

        // Autoren für FacetWP spiechern
        delete_post_meta( $post_id, 'material_autor_facet' );
        $autoren = explode( ',', $_POST[ 'pods_meta_material_autoren' ] ) ;
        $autoren_ids = array();
        if ( is_array( $autoren ) ) {
            foreach ( $autoren as $key => $val ) {
                if ( $val != '' ) {
                    $autoren_ids[] = (int)$val;
                }
            }
        } else {
            $autorid = (int) $autoren;
            if ( 0 !=  $autorid ) {
                $autoren_ids[] = $autorid;
            }
        }
        foreach ( $autoren_ids as $autoren_id ) {
            $vorname = get_post_meta($autoren_id, 'autor_vorname', true );
            $nachname = get_post_meta($autoren_id, 'autor_nachname', true );
            add_post_meta( $post_id, 'material_autor_facet', $vorname . ' ' . $nachname );
        }

        // Organisationen für FacetWP speichern
        delete_post_meta( $post_id, 'material_organisation_facet' );
        delete_post_meta( $post_id, 'material_alpika_facet' );
        $organisationen = explode( ',', $_POST[ 'pods_meta_material_organisation' ] ) ;
        $organisationen_ids = array();
        if ( is_array( $organisationen ) ) {
            foreach ( $organisationen as $key => $val ) {
                if ( $val != '' ) {
                    $organisationen_ids[] = (int) $val;
                }
            }
        } else {
            $orgaid = (int) $organisationen;
            if ( 0 != $orgaid ) {
                $organisationen_ids[] = $orgaid;
            }
        }
        foreach ( $organisationen_ids as $organisationen_id ) {
            /*organisation title*/
            $organisationen_meta = get_post( $organisationen_id );
            add_post_meta( $post_id, 'material_organisation_facet', $organisationen_meta->post_title );

            /*organisation_alpika*/
            if(get_post_meta($organisationen_meta->ID,'organisation_alpika', true)){
                add_post_meta( $post_id, 'material_alpika_facet', 1 );
            }

            /*organisation_konfession zu material konfessionen hinzugügen*/
            $konfession = get_post_meta($organisationen_meta->ID,'organisation_konfession', true);
            if($konfession && isset($konfession['name'])){
                wp_set_post_tags( $post_id, $konfession['name'], true);
            }

        }
        // Wenn Special, dann MaterialURL auf das Material selbst zeigen lassen.
        if (  $_POST[ 'pods_meta_material_special' ] == 1  ) {
            clean_post_cache( $post_id );
            $p = get_post( $post_id );
            $url = get_permalink( $p );
            update_post_meta( $post_id, 'material_url', $url  );
            $_POST[ 'pods_meta_material_url' ] = $url;
        }
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function remove_post_custom_fields() {
        remove_meta_box( 'tagsdiv-altersstufe' , 'material' , 'normal' );
        remove_meta_box( 'bildungsstufediv' , 'material' , 'normal' );
        remove_meta_box( 'tagsdiv-inklusion' , 'material' , 'normal' );
        remove_meta_box( 'tagsdiv-konfession' , 'material' , 'normal' );
        remove_meta_box( 'tagsdiv-lizenz' , 'material' , 'normal' );
        remove_meta_box( 'medientypdiv' , 'material' , 'normal' );
        remove_meta_box( 'tagsdiv-schlagwort' , 'material' , 'normal' );
        remove_meta_box( 'tagsdiv-sprache' , 'material' , 'normal' );
        remove_meta_box( 'tagsdiv-verfuegbarkeit' , 'material' , 'normal' );
        remove_meta_box( 'tagsdiv-zugaenglichkeit' , 'material' , 'normal' );
    }


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function custom_post_status(){
        register_post_status( 'vorschlag', array(
            'label'                     => _x( 'Vorschlag', 'material' ),
            'public'                    => true,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Vorschlag <span class="count">(%s)</span>', 'Vorschläge <span class="count">(%s)</span>' )
        ) );
        register_post_status( 'check', array(
            'label'                     => _x( 'Überprüfen', 'material' ),
            'public'                    => true,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Überprüfen <span class="count">(%s)</span>', 'Überprüfen <span class="count">(%s)</span>' )
        ) );
    }


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function append_post_status_list(){
        global $post;
        $complete1 = '';
        $label1 = '';
        $complete2 = '';
        $label2 = '';
        if($post->post_type == 'material'){
            if($post->post_status == 'vorschlag'){
                $complete1 = ' selected=\"selected\"';
                $label1 = '<span id=\"post-status-display\">Vorschlag</span>';
            }
            if($post->post_status == 'check'){
                $complete2 = ' selected=\"selected\"';
                $label2 = '<span id=\"post-status-display\">Überprüfen</span>';
            }
            echo '
          <script>
          jQuery(document).ready(function($){
               $("select#post_status").append("<option value=\"vorschlag\" '.$complete1.'>Vorschlag</option>");
               $("select#post_status").append("<option value=\"check\" '.$complete2.'>Überprüfen</option>");';
            if($post->post_status == 'vorschlag'){
                echo '$(".misc-pub-section label").append("'.$label1.'");';
            }
            if($post->post_status == 'check') {
                echo '$(".misc-pub-section label").append("' . $label2 . '");';
            }
            echo '
          });
          </script>
          ';
        }
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function add_metaboxes() {
	    add_meta_box('material_bookmarklet', __( 'Bookmarklet', Materialpool::$textdomain ), array( 'Materialpool_Material', 'bookmarklet_metabox' ), 'material', 'side', 'default');
	}

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
	static public function bookmarklet_metabox() {
	    $js = file_get_contents( Materialpool::$plugin_base_dir . 'js/bookmarklet.js' );
        echo "<a href='". $js ."'>". __( 'Materialpool++', Materialpool::$textdomain ) ."</a><br>";
        _e( 'Zieh den Link in deine Lesezeichenliste', Materialpool::$textdomain );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function material_list_post_join( $join ) {
        global $pagenow, $wpdb;

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && $_GET['s'] != '') {
            $join .='LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
        }
        return $join;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function material_list_post_where( $where ) {
        global $pagenow, $wpdb;

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && $_GET['s'] != '') {
            $where = preg_replace(
                "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
        }
        return $where;
    }


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function material_list_post_distinct( ) {
        global $pagenow;
        $back = '';

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && $_GET['s'] != '') {
            $back = " DISTINCT ";
        }
        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function vorschlag_shortcode( $args ) {

        $back = <<<END

<div class="materialpool-vorschlag">
    Ich möchte folgendes Material vorschlagen zur Aufnahme in den Materialpool.<br>
    <div class="materialpool-vorschlag-url">
        URL: <input type="text" id="vorschlag-url" >
    </div>
    <div class="materialpool-vorschlag-text">
        Beschreibung<br>
        <textarea id="vorschlag-beschreibung" ></textarea>
    </div>
    <br>
    <button class="materialpool-vorschlag-send">Vorschlagen</button>
    <div class="materialpool-vorschlag-hinweis">
        
    </div>
</div
END;


        return $back;
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

        return get_metadata( 'post', $post->ID, 'material_titel', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function shortdescription() {
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

        return get_metadata( 'post', $post->ID, 'material_kurzbeschreibung', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function description() {
        echo Materialpool_Material::get_description();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @filters materialpool_material_description
     * @filters materialpool_material_description_interim_autor
     * @filters materialpool_material_description_interim_organisation
     * @filters materialpool_material_description_interim_start
     * @filters materialpool_material_description_interim_separator
     * @filters materialpool_material_description_interim_end
     *
     */
    static public function get_description() {
        global $post;
        $autor = '';
        $organisation = '';

        $description = get_metadata( 'post', $post->ID, 'material_beschreibung', true );
        $description = apply_filters( 'materialpool_material_description', $description, $post );
        if ( ! self::has_autor() ) {
            $autor = apply_filters( 'materialpool_material_description_interim_autor', get_metadata( 'post', $post->ID, 'material_autor_interim', true ) );
        }
        if ( ! self::has_organisation() ) {
            $organisation = apply_filters( 'materialpool_material_description_interim_organisation', get_metadata( 'post', $post->ID, 'material_organisation_interim', true ) );
        }
        $addon = apply_filters( 'materialpool_material_description_interim_start', '<div class="">' );
        $addon .= $autor;
        if ( $autor != '' && $organisation != '' ) {
            $addon .= apply_filters( 'materialpool_material_description_interim_separator', ', ' );
        }
        $addon .= $organisation;
        $addon .= apply_filters( 'materialpool_material_description_interim_end', '</div>' );
        return $description . $addon;

    }


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function description_footer() {
        global $post;

        $user = get_user_by( 'ID', $post->post_author );
        $ts = strtotime( $post->post_date );
        echo "Im Materialpool eingetragen: " . date( 'd.m.Y', $ts) ." von " . $user->display_name;
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

        return get_metadata( 'post', $post->ID, 'material_veroeffentlichungsdatum', true );
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

        return get_metadata( 'post', $post->ID, 'material_depublizierungsdatum', true );
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

        return get_metadata( 'post', $post->ID, 'material_wiedervorlagedatum', true );
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

        return get_metadata( 'post', $post->ID, 'material_erstellungsdatum', true );
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
     *
     */
    static public function url_shorten() {
        $url =  parse_url ( Materialpool_Material::get_url() );
        if ( $url !== false ) {
            echo $url[ 'host' ];
        }

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
    static public function picture_html( $id = null ) {
        global $post;
        if ( $id == null ) $id = $post->ID;
        $pic  = Materialpool_Material::get_picture( $id );
        $data = '';
	    if ( is_array( $pic ) ) {
		    $url = wp_get_attachment_url( $pic[ 'ID' ] );
		    $data =  '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
	    }
	    if ( $data == '' ) {
	        $url = Materialpool_Material::get_picture_url( $id );
	        if ( $url != '')  {
		        $data =  '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
	        }
        }
        if ( $data == '' ) {
            $url = Materialpool_Material::get_screenshot( $id );
            if ( $url != '')  {
                $data =  '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
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
    static public function get_picture( $id = null ) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        return get_metadata( 'post', $id, 'material_cover', true );
    }

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function get_picture_url( $id = null ) {
		global $post;
        if ( $id == null ) $id = $post->ID;

		return get_metadata( 'post', $id, 'material_cover_url', true );
	}


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_screenshot( $id = null ) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        return get_metadata( 'post', $id, 'material_screenshot', true );
    }



    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
	static public function cover_facet_html( $id = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;

	    $url = '';
        $data = '';
        // Prio 1: hochgeladenes Bild
        $pic  = Materialpool_Material::get_picture( $id );
        if ( is_array( $pic ) ) {
            $url = wp_get_attachment_url( $pic[ 'ID' ] );
        }
        // Prio 2, Cover URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_picture_url( $id );
        }
        // Prio 3, Screenshot URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_screenshot( $id );
        }
        if ( $url != '' && trim( $url)  != '' ) {
            $data = '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'alignleft materialpool-template-material-picture-facet' ) .'"/>';

        }
        return $data;
    }





    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function cover_facet_html_noallign( $id = null) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        $url = '';
        $data = '';
        // Prio 1: hochgeladenes Bild
        $pic  = Materialpool_Material::get_picture( $id );
        if ( is_array( $pic ) ) {
            $url = wp_get_attachment_url( $pic[ 'ID' ] );
        }
        // Prio 2, Cover URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_picture_url( $id );
        }
        // Prio 3, Screenshot URL
        if ( $url == '' ) {
            $url  = Materialpool_Material::get_screenshot( $id );
        }
        if ( $url != '' && trim( $url)  != '' ) {
            $data = '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture-facet' ) .'"/>';

        }
        return $data;
    }


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function werk() {
        $werkID =  Materialpool_Material::get_werk_id();
	    if ( false === $werkID ) {
	    	return;
	    }
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

	    $werk = get_metadata( 'post', $post->ID, 'material_werk', true );
		if ( is_array( $werk ) ) {
		    return ( $werk["ID"] );
		} else {
			return false;
		}
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

	/**
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 */
	static public function verweise () {
		$verweise = Materialpool_Material::get_verweise();
		foreach ( $verweise as $verweis ) {
			echo $verweis[ 'post_title' ] . '<br>';
		}
	}

    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function has_verweise () {
        $back = false;
        $verweise = Materialpool_Material::get_verweise();
        if ( is_array( $verweise ) && $verweise[0] ) {
            $back = true;
        }
        return $back;
    }

	/**
	 *
	 * @since 0.0.1
	 * @access public
 	 * @filters materialpool-template-material-verweise
	 */
	static public function verweise_html () {
		$verweise = Materialpool_Material::get_verweise();
		foreach ( $verweise as $verweis ) {
			$url = get_permalink( $verweis[ 'ID' ] );
			echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-verweise' ) .'">' . $verweis[ 'post_title' ] . '</a><br>';

		}
	}


    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function get_verweise_ids () {
        $back = array();
        $verweise = Materialpool_Material::get_verweise();
        foreach ( $verweise as $verweis ) {
            $back[] = $verweis[ 'ID' ];
        }
        return $back;
    }
    /**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function get_verweise() {
		global $post;

		return get_metadata( 'post', $post->ID, 'material_verweise', false );
	}

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function availability() {
        echo Materialpool_Material::get_availability();
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_availability() {
        global $post;

        $vid = get_metadata( 'post', $post->ID, 'material_verfuegbarkeit', true );
        if ( is_array( $vid ) ) {
            return $vid[ 'name'];
        }
    }


    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function organisation () {
        $verweise = Materialpool_Material::get_organisation();
        foreach ( $verweise as $verweis ) {
            echo $verweis[ 'post_title' ] . '<br>';
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function organisation_html () {
        $verweise = Materialpool_Material::get_organisation();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-verweise' ) .'">' . $verweis[ 'post_title' ] . '</a><br>';

        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function organisation_html_cover () {
        $verweise = Materialpool_Material::get_organisation();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            $logo = get_metadata( 'post', $verweis[ 'ID' ], 'organisation_logo_url', true );
            echo "<div class='materialpool-template-material-organisation'>";
            if ( $logo != '') {
                echo '<a href="' . $url . '" style="background-image:url(\'' . $logo . '\')" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-organisation-logo' ) .'"><img src="' . $logo . '"></a>';
            }
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-organisation' ) .'">' . $verweis[ 'post_title' ] . '</a>';
            echo "</div>";



        }
    }


    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function has_organisation () {
        $verweise = Materialpool_Material::get_organisation();
        $back = true;
        if ( $verweise === false) {
            $back = false;
        }
        if ( is_array( $verweise ) && $verweise[ 0 ] === false ) {
            $back = false;
        }

        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_organisation() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_organisation', false );
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-organisation
     */
    static public function organisation_facet_html () {
        $organisationen = Materialpool_Material::get_organisation();
        $data = '';
        foreach ( $organisationen as $organisation ) {
            if ( $organisation != '' )
                if ( $data != '') {
                    $data .= ', ';
                }
                $data .= $organisation[ 'post_title' ];
        }
        $data = "<span class='search-organisation'>" . $data . "</span>";
        return $data;
    }



    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function has_autor () {
        $verweise = Materialpool_Material::get_autor();
        $back = true;
        if ( $verweise === false) {
            $back = false;
        }
        if ( is_array( $verweise ) && $verweise[ 0 ] === false ) {
            $back = false;
        }

        return $back;
    }


    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function autor () {
        $verweise = Materialpool_Material::get_autor();
        foreach ( $verweise as $verweis ) {
            $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
            $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
            echo $vorname. ' ' . $nachname . '<br>';
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     *
     */
    static public function autor_list () {
        $count = 0;
        $verweise = Materialpool_Material::get_autor();
        foreach ( $verweise as $verweis ) {
            if ($count > 0 ) {
                echo ", ";
            }
            $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
            $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
            echo $vorname . ' ' . $nachname;
            $count++;
        }
    }

    /**
 *
 * @since 0.0.1
 * @access public
 * @filters materialpool-template-material-autor
 */
    static public function autor_html () {
        $verweise = Materialpool_Material::get_autor();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
            $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $vorname .' '. $nachname . '</a><br>';

        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function autor_html_picture () {
        $verweise = Materialpool_Material::get_autor();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            $logo = get_metadata( 'post', $verweis[ 'ID' ], 'autor_bild_url', true );
            $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
            $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
            if ( $logo != '') {
                //echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'"><img  class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'" src="' . $logo . '"></a>';
                echo '<a href="' . $url . '" style="background-image:url(\'' . $logo . '\')" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'"></a>';
            }
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $vorname . ' '. $nachname . '</a>';

        }
    }


    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function autor_facet_html () {
        $verweise = Materialpool_Material::get_autor();
        $url =  parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
        $data = '';
        foreach ( $verweise as $verweis ) {
            if ( $verweis != '' )
                if ( $data != '') {
                    $data .= ', ';
                }
            $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
            $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
            $data .=  $vorname .' '. $nachname;
        }
        $data = "<span class='search-autor'>" . $data . "</span>";
        return $data;
    }



    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function bildungsstufe_facet_html () {
        global $post;

        $url =  parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
        $data = '';
        $bildungsstufe = get_metadata( 'post', $post->ID, 'material_bildungsstufe' );
        if ( sizeof( $bildungsstufe ) == 1 ) {
            if ( $bildungsstufe[ 0 ] !== false ) {
                if ( $bildungsstufe[ 0 ][ 'parent'] != 0 ) {
                    $link = add_query_arg( 'fwp_bildungsstufe', $bildungsstufe[0][ 'slug' ], $url );
                    $data .= '<span class="facet-tag"><a href="' . $link . '">' . $bildungsstufe[0][ 'name' ] .'</a></span>';
                }
            }
        } else {
            foreach ( $bildungsstufe as $bildung ) {
                if ( $bildung[ 'parent'] != 0 ) {
                    $link = add_query_arg( 'fwp_bildungsstufe', $bildung[ 'slug' ], $url );
                    $data .= '<span class="facet-tag"><a href="' . $link . '">' . $bildung[ 'name' ] .'</a></span>';
                }
            }
        }
        return $data;
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @return  commaseparated bildungssufen
     */
    static public function get_bildungsstufen () {
        global $post;

        $url =  parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
        $data = '';
        $bildungsstufe = get_metadata( 'post', $post->ID, 'material_bildungsstufe' );
        foreach ( $bildungsstufe as $bildung ) {
            if ( $bildung[ 'parent'] != 0 ) {
                $data[] =  $bildung[ 'name' ] ;
            }
        }
        return implode(', ', $data);
    }

    static public function bildungsstufen () {
        return self::get_bildungsstufen();
    }



    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function inklusion_facet_html () {
        global $post;
        $url =  parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
        $data = '';
        $bildungsstufe = get_metadata( 'post', $post->ID, 'material_inklusion' );
        if ( sizeof( $bildungsstufe ) == 1 ) {
            if ( $bildungsstufe[ 0 ] !== false ) {
                $link = add_query_arg( 'fwp_bildungsstufe', $bildungsstufe[0][ 'slug' ], $url );
                $data .= '<span class="facet-tag"><a href="' . $link . '">' . $bildungsstufe[0][ 'name' ] .'</a></span>';
            } else {
                $data = "";
            }
        } else {
            foreach ( $bildungsstufe as $bildung ) {
                $link = add_query_arg( 'fwp_bildungsstufe', $bildung[ 'slug' ], $url );
                $data .= '<span class="facet-tag"><a href="' . $link . '">' . $bildung[ 'name' ] .'</a></span>';
            }
        }
        return $data;
    }



    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_autor() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_autoren', false );
    }

	/**
	 * @since 0.0.1
	 * @access public
	 * @return mixed
	 */
    static public function is_special() {
    	global $post;

    	$back = false;
	    $special =  get_metadata( 'post', $post->ID, 'material_special', true );

		if ( $special == '1' ) {
			$back = true;
		}
		return $back;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return bool
     */
    static public function is_alpika() {
        global $post;
        $alpika =  get_post_meta( $post->ID, 'material_alpika_facet', true );
        return $alpika?true:false;
    }


    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_viewer() {
        global $post;

        $back = false;
        $url =  get_metadata( 'post', $post->ID, 'material_url', true );
        if ( mb_endsWith($url, '.pdf' ) ) {
            $back = true;
        }

        return $back;
    }


    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_embed() {
        global $post;

        $back = true;
        $url =  wp_oembed_get( get_metadata( 'post', $post->ID, 'material_url', true )) ;
        if ($url === false ) {
            $back = false;
        }

        return $back;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_download() {
        global $post;

        $back = false;
        $url =  get_metadata( 'post', $post->ID, 'material_url', true );
        if ( mb_endsWith($url, '.pdf' ) ) {
            $back = true;
        }

        return $back;
    }



    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function get_schlagworte() {
        global $post;

        $data = '';
        $schlagworte = get_metadata( 'post', $post->ID, 'material_schlagworte' );
        if ( sizeof( $schlagworte ) == 1 ) {
            if ( $schlagworte[ 0 ] !== false ) {
                if ( $data != '') $data .= ', ';
                $data .= $schlagworte[ 0 ][ 'name' ];
            } else {
                $data = "";
            }
        } else {
            foreach ( $schlagworte as $schlagwort ) {
                if ( $data != '') $data .= ', ';
                $data .= $schlagwort[ 'name' ];
            }
        }
        return $data;
    }

    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function get_schlagworte_html() {
        global $post;
        $url =  parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
        $data = '';
        $schlagworte = get_metadata( 'post', $post->ID, 'material_schlagworte' );
        if ( sizeof( $schlagworte ) == 1 ) {
            if ( $schlagworte[ 0 ] !== false ) {
                $link = add_query_arg( 'fwp_schlagworte', $schlagworte[0][ 'slug' ], $url );
                if ( $data != '') $data .= ', ';
                $data .= '<a href="' . $link . '">' . $schlagworte[0][ 'name' ] .'</a>';
            } else {
                $data = "";
            }
        } else {
            foreach ( $schlagworte as $schlagwort ) {
                $link = add_query_arg( 'fwp_schlagworte', $schlagwort[ 'slug' ], $url );
                if ( $data != '') $data .= ', ';
                $data .= '<a href="' . $link . '">' . $schlagwort[ 'name' ] .'</a>';
            }
        }
        return $data;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function jahr_html() {
        $jahr = Materialpool_Material::get_jahr();
        $data = '';
        if ( $jahr != '' ) {
            $data = '<span class="facet-tag">' . $jahr . '</span>';
        }
        return $data;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_jahr() {
        global $post;

        return get_metadata( 'post', $post->ID, 'material_jahr', true );
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function get_mediatyps_root() {
        global $post;

        $arr = array();
        $typs = wp_get_object_terms( $post->ID, 'medientyp' );
        foreach ( $typs as $term ) {
            if ( $term->parent == 0 ) {
                $icon =   get_metadata( 'term', $term->term_id, 'medientyp_icon', true );
                $farbe =   get_metadata( 'term', $term->term_id, 'medientyp_farbe', true );
                $arr[] = array(
                  'name' => $term->name,
                  'icon' => $icon,
                  'farbe' => $farbe
                );
            }
        }
        return $arr;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function rating_facet_html() {
        global $post;
        if (function_exists( 'the_ratings_results' )) {
            return '<span class="facet-rating">' . the_ratings_results( $post->ID )  . '</span>';
        }

    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function cta_link() {
        global $post;

        if ( self::is_download() ) {
            $text = "Material herunterladen";
        } else {
            $text = "Zum Material";
        }
         return "<a class='cta-button' href='". self::get_url() ."'>".$text."</a>";
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function cta_url2clipboard() {
        global $post;

        $back = "<a class='cta-button copyurl'>URL in Zwischenablage</a> <script> jQuery(document).ready(function(){ var clipboard = new Clipboard('.copyurl', { text: function() { return '". self::get_url() ."'; } }); clipboard.on('success', function(e) { console.log(e);}); clipboard.on('error', function(e) { console.log(e); });}); </script>";
        return $back;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @param $id
     * @return string
     */
    static public function get_themengruppentitel( $id ) {
        global $wpdb;
        $query_str 		= $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 WHERE id = %s ', $id );
        $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr[ 0 ][ 'gruppe' ];
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @param $id
     * @return string
     */
    static public function get_themengruppenbeschreibung( $id ) {
        global $wpdb;
        $query_str 		= $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 WHERE id = %s ', $id );
        $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr[ 0 ][ 'titel_der_gruppe' ];
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @param $id
     * @return array
     */
    static public function get_themengruppe( $id ) {
        global $wpdb;
        $query_str 		= $wpdb->prepare('SELECT *  FROM `' . $wpdb->prefix . 'pods_themenseitengruppen`	 	  
										 WHERE id = %s ', $id );
        $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
        return $items_arr[ 0 ];
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @param $id
     * @return string
     */
    static public function get_thema( $id ) {
        $thema = get_post( $id);
        return $thema->post_title;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @param $id
     * @return string
     */
    static public function cb_themenseite_checked( $id ) {
        $thema = self::get_themengruppe( $id);
        $auswahlArr = explode( ',', $thema[ 'auswahl'] );
        $back ='';
        if ( in_array( get_the_ID(), $auswahlArr ) ) {
            $back = "checked='checked' class='uncheck_themenseite' ";
        } else {
            $back = " class='check_themenseite' ";
        }
        return $back;
    }



    /**
     *
     * @since 0.0.1
     * @access	public
     * @return string
     *
     */
    static public function cb_themenseite() {
        $thema = (int) $_GET[ 'thema'];
        $gruppe = (int) $_GET[ 'gruppe'];
        $back = '';
        if ( $thema == 0 || $gruppe == 0 ) {
            return $back;
        }
        $gruppenname = self::get_themengruppentitel ($gruppe);
        $themaname = self::get_thema( $thema );
        if ( is_user_logged_in() ) {  // @todo Rolle abfragen welche nötig ist dafür
            $back .= '<div class="material-themenseiten-auswahl">';
            $back .= "<input type='checkbox' " . self::cb_themenseite_checked( $gruppe ) ;
            $back .= " data-thema='". $thema ."' data-gruppe='". $gruppe ."' data-post='". get_the_ID() ."'";
            $back .= ">";
            $back .= "Material der Themenseite '". $themaname ."', Gruppe '" . $gruppenname . "' zuordnen.";
            $back .= '</div>';
        }

        return $back;
    }
}
