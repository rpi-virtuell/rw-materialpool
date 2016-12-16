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
        $columns[ 'material-bildungsstufe' ] = _x( 'Bildungsstufe', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-owner' ] = _x( 'Eintrager', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-schlagworte' ] = _x( 'Schlagworte', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-organisation' ] = _x( 'Organisation', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-autor' ] = _x( 'Autoren', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-medientyp' ] = _x( 'Medientyp', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-online' ] = _x( 'Online', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-stauts' ] = _x( 'Status', 'Material list field',  Materialpool::$textdomain );
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

        if ( $column_name == 'material-autor' ) {
            $autors = get_metadata( 'post', $post_id, 'material_autoren' );
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

        $wpdb->update(
            $wpdb->posts,
            array(
                'post_title' => stripslashes( $title ),
                'post_name' => wp_unique_post_slug( sanitize_title( $title ), $post_id, $post_status, $post_type, $post_parent ),
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
        $autoren = self::get_autor();
        foreach ( $autoren as $autor ) {
            add_post_meta( $post_id, 'material_autor_facet', $autor[ 'post_title' ] );
        }

        // Organisationen für FacetWP speichern
         delete_post_meta( $post_id, 'material_organisation_facet' );
        $organisationen = self::get_organisation();
        foreach ( $organisationen as $organisation ) {
            add_post_meta( $post_id, 'material_organisation_facet', $organisation[ 'post_title' ] );
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

        return get_metadata( 'post', $post->ID, 'material_kurzbeschreibung', true );
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

        return get_metadata( 'post', $post->ID, 'material_beschreibung', true );
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
        $pic  = Materialpool_Material::get_picture();
	    if ( is_array( $pic ) ) {
		    $url = wp_get_attachment_url( $pic[ 'ID' ] );
		    echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
	    }
	    $url = Materialpool_Material::get_picture_url();
	    if ( $url != '') {
		    echo '<img  src="' . $url . '" class="'. apply_filters( 'materialpool-template-material-picture', 'materialpool-template-material-picture' ) .'"/>';
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

        return get_metadata( 'post', $post->ID, 'material_cover', true );
    }

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function get_picture_url() {
		global $post;

		return get_metadata( 'post', $post->ID, 'material_cover_url', true );
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
     *
     */
    static public function autor () {
        $verweise = Materialpool_Material::get_autor();
        foreach ( $verweise as $verweis ) {
            echo $verweis[ 'post_title' ] . '<br>';
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
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $verweis[ 'post_title' ] . '</a><br>';

        }
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
}
