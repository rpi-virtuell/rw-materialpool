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

        if (is_tax() ) {
	        return $template;
        }

	    $template_path = $template;
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
            $autoren = get_metadata( 'post', $post->ID, 'material_autoren', false );
            foreach( $autoren  as $autor ) {
                if ( is_array( $autor ) ) {
                    Materialpool_Statistic::log_autor( $autor[ 'ID' ] );
                }
            }
            $orgas = get_metadata( 'post', $post->ID, 'material_organisation', false );
            foreach( $orgas  as $orga ) {
                if ( is_array( $orga ) ) {
                    Materialpool_Statistic::log_organisation( $orga[ 'ID' ] );
                }
            }
            Materialpool_Statistic::log( $post->ID, $post->post_type );

            return $template_path;
        }
        if ( $post->post_type == "post" && Materialpool_Material::is_old_material() ) {
            if ( is_single() ) {
                if ( $theme_file = locate_template( array ( 'materialpool/single-material-old.php' ) ) ) {
                    $template_path = $theme_file;
                } else {
                    $template_path = Materialpool::$plugin_base_dir . 'templates/single-material-old.php';
                }
                return $template_path;
            }
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
        $columns[ 'material_views' ] = _x( 'Views', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-bildungsstufe' ] = _x( 'Bildungsstufe', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-schlagworte' ] = _x( 'Schlagworte', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-medientyp' ] = _x( 'Medientyp', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-organisation' ] = _x( 'Organisation', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-autor' ] = _x( 'Autoren', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-online' ] = _x( 'Online', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-status' ] = _x( 'Status', 'Material list field',  Materialpool::$textdomain );
        $columns[ 'material-owner' ] = _x( 'Redakteur', 'Material list field',  Materialpool::$textdomain );
	    $columns[ 'material-vorschlag' ] = _x( 'Eintrager', 'Material list field',  Materialpool::$textdomain );
        return $columns;
    }

    /**
     * Add content for the custom columns in list table
     *
     * @since   0.0.1
     * @access	public
     */
    static public function add_taxonomy_filters() {
        global $typenow;

        // an array of all the taxonomyies you want to display. Use the taxonomy name or slug
        $taxonomies = array('bildungsstufe', 'schlagwort', 'medientyp');

        // must set this to the post type you want the filter(s) displayed on
        if( $typenow == 'material' ){

            foreach ($taxonomies as $tax_slug) {
                $tax_obj = get_taxonomy($tax_slug);
                $tax_name = $tax_obj->labels->name;
                $terms = get_terms($tax_slug);
                if(count($terms) > 0) {
                    echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
                    echo "<option value=''>$tax_name</option>";
                    foreach ($terms as $term) {
                        $selected = '';
                        if ( isset(  $_GET[$tax_slug] )  && $_GET[$tax_slug] == $term->slug ) {
	                        $selected = 'selected="selected"';
                        }
                        echo '<option value='. $term->slug . ' '. $selected . '>' . $term->name .' (' . $term->count .')</option>';
                    }
                    echo "</select>";
                }
            }
        }
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
        global $wpdb;

        $data = '';
        if ( $column_name == 'material-autor' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-autor-2-'.$post_id ) ) ) {
                $autors = get_metadata('post', $post_id, 'material_autoren');
                if (sizeof($autors) == 1) {
                    if ($autors[0] !== false) {
                        $vorname = get_post_meta($autors[0]['ID'], 'autor_vorname', true);
                        $nachname = get_post_meta($autors[0]['ID'], 'autor_nachname', true);
                        $data .= '<a href="/wp-admin/edit.php?post_type=material&all_posts=1&AUTOR_FILTER_FIELD_NAME=' .  $autors[ 0 ][ 'ID' ]  . '">' . $vorname . ' '.$nachname  .'</a><br>';
                    } else {
                        $data = "";
                    }
                } else {
                    foreach ($autors as $autor) {
                        $vorname = get_post_meta($autor['ID'], 'autor_vorname', true);
                        $nachname = get_post_meta($autor['ID'], 'autor_nachname', true);
                        $data .= '<a href="/wp-admin/edit.php?post_type=material&all_posts=1&AUTOR_FILTER_FIELD_NAME=' .  $autor ['ID' ]  . '">' .  $vorname . ' '.$nachname  .'</a><br>';
                    }
                }
                set_transient( 'mp-cpt-list-material-autor-2-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }
        }
        if ( $column_name == 'material-medientyp' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-medientyp2-'.$post_id ) ) ) {
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
                set_transient( 'mp-cpt-list-material-medientyp2-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }
        }
        if ( $column_name == 'material-bildungsstufe' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-bildungsstufe-'.$post_id ) ) ) {
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
                set_transient( 'mp-cpt-list-material-bildungsstufe-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }

        }
        if ( $column_name == 'material-owner' ) {
            $post = get_post( $post_id);
            $user = get_user_by( 'ID', $post->post_author );
            $data = $user->display_name;
        }
	    if ( $column_name == 'material-vorschlag' ) {
		    $data = get_metadata( 'post', $post_id, 'material_von_name', true );
	    }

        if ( $column_name == 'material-schlagworte' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-schlagworte-'.$post_id ) ) ) {
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
                set_transient( 'mp-cpt-list-material-schlagworte-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }
        }
        if ( $column_name == 'material-organisation' ) {
            if ( false === ( $transient = get_transient( 'mp-cpt-list-material-organisation-2-'.$post_id ) ) ) {
                $autors = get_metadata( 'post', $post_id, 'material_organisation' );
                if ( sizeof( $autors ) == 1 ) {
                    if ( $autors[ 0 ] !== false ) {
                        $post = get_post( $autors[ 0 ][ 'ID' ] );
                        $data .= '<a href="/wp-admin/edit.php?post_type=material&all_posts=1&ORGA_FILTER_FIELD_NAME=' .  $autors[ 0 ][ 'ID' ]  . '">' . $post->post_title .'</a><br>';
                    } else {
                        $data = "";
                    }
                } else {
                    foreach ( $autors as $autor ) {
                        $post = get_post( $autor[ 'ID' ] );
                        $data .= '<a href="/wp-admin/edit.php?post_type=material&all_posts=1&ORGA_FILTER_FIELD_NAME=' .  $autor[ 'ID' ]  . '">' . $post->post_title .'</a><br>';
                    }
                }
                set_transient( 'mp-cpt-list-material-organisation-2-'.$post_id, $data, 60*60*24*7 ); // Eine Woche zwischenspeichern
            } else {
                $data .= $transient;
            }
        }
        if ( $column_name == 'material-status' ) {
            $labels =  get_post_status_object( get_post_status( $post_id) );
            $data = $labels->label;

        }
        if ( $column_name == 'material_views' ) {
            $wpdb->mp_stats = $wpdb->prefix . 'mp_stats';
            $query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->mp_stats} WHERE object = %d",
                $post_id
            );
            $results = $wpdb->get_var(  $query );

            $data = $results;
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
        if ( "trash" == $post_status ) return;

        if ( isset( $_POST[ 'pods_meta_material_titel' ] ) ) {
	        $title = $_POST[ 'pods_meta_material_titel' ];
        } else {
            $title =  get_metadata('post', $post_id, 'material_titel', true );
        }
        $post_name = wp_unique_post_slug( sanitize_title( $title ), $post_id, 'publish', $post_type, $post_parent );
        $post_content = '';
        $url = '';
		// Prio 1: hochgeladenes Bild
		$pic  = $_POST[ 'pods_meta_material_cover' ];
		if ( is_array( $pic ) ) {
		    foreach ( $pic as $picArray ) {
		        $id = (int) $picArray[ 'id' ];
            }
        }
		if ( is_int( $id ) ) {
			$urlA = wp_get_attachment_image_src( $id  );
			$url = $urlA[ 0 ];
		}
		// Prio 2, Cover URL
		if ( $url == '' ) {
			$url  = $_POST[ 'pods_meta_material_cover_url' ];
		}
		// Prio 3, Screenshot URL
		if ( $url == '' ) {
			$url  = trim( $_POST[ 'pods_meta_material_screenshot' ] );
		}
		if ( $url != '' ) {
			$post_content ='<img class="size-medium  alignleft" src="'. trim( $url ) .'" alt="" sizes="(max-width: 300px) 100vw, 300px">';
        }

        $post_content .= '<strong>' . wp_unslash( apply_filters( 'content_save_pre', $_POST[ 'pods_meta_material_kurzbeschreibung' ] ) ) . '</strong>';
        $post_content .= "\n\n<p>";
        $text = wp_unslash( $_POST[ 'pods_meta_material_beschreibung' ] );
        $text = strip_shortcodes( $text );
        $text = apply_filters( 'the_content', $text );
        $text = str_replace(']]>', ']]&gt;', $text);
        $excerpt_length = apply_filters( 'excerpt_length', 55 );
        $excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
        $text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
        $post_content .= $text . '&hellip;';

        $wpdb->update(
            $wpdb->posts,
            array(
                'post_title' => stripslashes( $title ),
                'post_name' => $post_name,
                'post_content' => $post_content,
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


		// Werkzeug des Materials in term_rel speichern
		wp_delete_object_term_relationships( $post_id, 'werkzeug' );
		$cats = $_POST[ 'pods_meta_material_werkzeug' ];
		if ( is_array( $cats ) ) {
			foreach ( $cats as $key => $val ) {
				$cat_ids[] = (int) $val;
			}
		}
		if ( $cats!== null  ) {
			$cat_ids[] = (int) $cats;
		}
		wp_set_object_terms( $post_id, $cat_ids, 'werkzeug', true );


		// Vorauswahl des Materials in term_rel speichern
		wp_delete_object_term_relationships( $post_id, 'vorauswahl' );
		$cats = $_POST[ 'pods_meta_material_vorauswahl' ];
		if ( is_array( $cats ) ) {
			foreach ( $cats as $key => $val ) {
				$cat_ids[] = (int) $val;
			}
		}
		if ( $cats!== null  ) {
			$cat_ids[] = (int) $cats;
		}
		wp_set_object_terms( $post_id, $cat_ids, 'vorauswahl', true );


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
            $konfession = get_post_meta( $organisationen_meta->ID,'organisation_konfession', true);
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

        // Transients für Backendliste löschen
        delete_transient( 'mp-cpt-list-material-autor-'.$post_id );
        delete_transient( 'mp-cpt-list-material-medientyp-'.$post_id );
        delete_transient( 'mp-cpt-list-material-medientyp-'.$post_id );
        delete_transient( 'mp-cpt-list-material-schlagworte-'.$post_id );
        delete_transient( 'mp-cpt-list-material-organisation-'.$post_id );

        // Transients für Frontendcache löschen
        delete_transient( 'facet_serach2_entry-'.$post_id );
		delete_transient( 'rss_material_entry-'.$post_id );
        delete_transient( 'facet_autor_entry-'.$post_id );
        delete_transient( 'facet_themenseite_entry-'.$post_id );
        delete_transient( 'facet_organisation_entry-'.$post_id );

        Materialpool_Material::set_createdate( $post_id );
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
	    remove_meta_box( 'vorauswahldiv' , 'material' , 'normal' );
	    remove_meta_box( 'tagsdiv-werkzeug' , 'material' , 'normal' );
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
            'public'                    => false,
            'show_in_admin_all_list'    => false,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Vorschlag <span class="count">(%s)</span>', 'Vorschläge <span class="count">(%s)</span>' )
        ) );
        register_post_status( 'check', array(
            'label'                     => _x( 'Überprüfen', 'material' ),
            'public'                    => false,
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
	    add_meta_box('material_url', __( 'Material', Materialpool::$textdomain ), array( 'Materialpool_Material', 'material_metabox' ), 'material', 'side', 'default');
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
	static public function material_metabox() {
	    $url = Materialpool_Material::get_url();
 		echo "<a target='_new' href='". $url ."' class='preview button' >". __( 'zum Material', Materialpool::$textdomain ) ."</a><br><br>";
	}

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function material_list_post_join( $join ) {
        global $pagenow, $wpdb;

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && isset( $_GET['s'] )  && $_GET['s'] != '') {
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

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && isset( $_GET['s'] )  && $_GET['s'] != '') {
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

        if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='material' && isset( $_GET['s'] ) && $_GET['s'] != '') {
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
    static public function check_404_old_material() {
        global $wp_query;
        global $wpdb;

        if ( is_404() ) {
            $uri = $_SERVER[ 'REQUEST_URI' ];
            if ( strpos( $uri, '/material/') !== false ) {
                // old_slug im Material suchen und ggf Umleiten
                $query = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'old_slug' and meta_value = %s",
                    $uri
                );
                $results = $wpdb->get_var(  $query );
                if ( $results !== null ) {
                    status_header( 301 );
                    $wp_query->is_404=false;
                    if ( wp_redirect( get_permalink( $results ) ) ) {
                        exit;
                    }
                }
            }
            if ( strpos( $uri, '/tagpage/') !== false ) {
                // old_slug im Material suchen und ggf Umleiten
                $query = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'old_slug' and meta_value = %s",
                    $uri
                );
                $results = $wpdb->get_var(  $query );
                if ( $results !== null ) {
                    status_header( 301 );
                    $wp_query->is_404=false;
                    if ( wp_redirect( get_permalink( $results ) ) ) {
                        exit;
                    }
                }
            }
	        if ( strpos( $uri, '/check_autor/') !== false ) {
                $hash = substr( $uri, 13 );

		        $result = $wpdb->get_var( $wpdb->prepare( "SELECT $wpdb->postmeta.post_id   FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_value = %s " , $hash ) );
                if ( ! is_wp_error( $result) && $result !== false ) {
		            add_metadata( 'post', $result, 'autor_email_read', time() );
		            wp_redirect( get_permalink( $result) );
		            exit;
                }
	        }
	        if ( strpos( $uri, '/check_organisation/') !== false ) {
		        $hash = substr( $uri, 20 );
		        $result = $wpdb->get_var( $wpdb->prepare( "SELECT $wpdb->postmeta.post_id   FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_value = %s " , $hash ) );
		        if ( ! is_wp_error( $result) && $result !== false ) {
			        add_metadata( 'post', $result, 'organisation_email_read', time() );
			        wp_redirect( get_permalink( $result) );
		        }
	        }
        }
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function remove_from_bulk_actions( $actions ) {
        unset( $actions[ 'edit' ] );
        return $actions;
    }


    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function row_actions( $actions, $post ) {
        if ( $post->post_type == "material" ) {
            unset($actions['inline hide-if-no-js']);
        }
        return $actions;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function vorschlag_shortcode( $args ) {
        $user = "";
        $email = "";
        if ( is_user_logged_in() ) {
	        $current_user = wp_get_current_user();
            $user = $current_user->display_name;
            if ($user == '') $user = $current_user->user_login;
            $email = $current_user->user_email;
        }
        $back = <<<END

<div class="materialpool-vorschlag">
    Ich möchte folgendes Material vorschlagen zur Aufnahme in den Materialpool.<br>
    <div class="materialpool-vorschlag-url">
        URL: <input type="text" id="vorschlag-url" >
    </div>
    <div class="materialpool-vorschlag-namne">
        Dein Name: <input type="text" id="vorschlag-name" value="$user">
    </div>    
    <div class="materialpool-vorschlag-email">
        Deine E-Mail: <input type="text" id="vorschlag-email"  value="$email">
    </div>    
    <div class="materialpool-vorschlag-text">
        Beschreibung der Seite<br>
        <textarea id="vorschlag-beschreibung" ></textarea>
    </div>
    <br>
    <button class="materialpool-vorschlag-send">Vorschlagen</button>
    <div class="materialpool-vorschlag-hinweis">
        
    </div>
</div>
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
     *
     */
    static public function get_description() {
        global $post;
        $autor = '';
        $organisation = '';

        $description = get_metadata( 'post', $post->ID, 'material_beschreibung', true );
        $description = apply_filters( 'materialpool_material_description', $description, $post );
        return $description ;

    }


    /**
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
    static public function get_picture_source( $id = null ) {
        global $post;
        if ( $id == null ) $id = $post->ID;

        return get_metadata( 'post', $id, 'material_cover_quelle', true );
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
	static public function get_cover( $id = null) {
		global $post;
		if ( $id == null ) $id = $post->ID;

		$url = '';
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

		return trim($url);
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
        $result = $wpdb->get_row( $wpdb->prepare("SELECT count(post_id) as count FROM $wpdb->postmeta WHERE meta_key = %s and meta_value = %s ", 'material_werk', $post->ID ) );
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
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s  and $wpdb->posts.post_status = 'publish'  order by post_title asc" , 'material_werk', $post->ID ) );
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
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s  and $wpdb->posts.post_status = 'publish'  order by post_title asc" , 'material_werk', $post->ID ) );
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
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s and $wpdb->posts.post_status = 'publish'   order by post_title asc" , 'material_werk', $werk ) );
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
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT $wpdb->posts.*  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value = %s  and $wpdb->posts.post_status = 'publish'  order by post_title asc" , 'material_werk', $werk ) );
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
	 * @access	public
	 *
	 */
	static public function get_werkzeuge() {
		global $post;

		$vid = get_metadata( 'post', $post->ID, 'material_werkzeug', true );
		if ( is_array( $vid ) ) {
			return $vid[ 'name'];
		}
	}

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
	static public function werkzeuge_html() {
		global $post;

		$vid = get_metadata( 'post', $post->ID, 'material_werkzeug', true );
		if ( is_array( $vid ) ) {
			echo "<a href='/" . $vid['taxonomy'] . "/" . $vid['slug'] . "'>" . $vid['name'] . "</a>";
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
     * @filters materialpool-template-material-organisation
     */
    static public function organisation_html () {
        global $post;
        $verweise = Materialpool_Material::get_organisation();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-verweise' ) .'">' . $verweis[ 'post_title' ] . '</a><br>';

        }
        $organisation = apply_filters( 'materialpool_material_description_interim_organisation', get_metadata( 'post', $post->ID, 'material_organisation_interim', true ) );
        if ( $organisation != '' ) {
            echo '<a class="'. apply_filters( 'materialpool-template-material-organisation', 'materialpool-template-material-organisation' ) .'">' . $organisation. '</a>';
        }

    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-verweise
     */
    static public function organisation_html_cover () {
        global $post;
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
        $organisation = apply_filters( 'materialpool_material_description_interim_organisation', get_metadata( 'post', $post->ID, 'material_organisation_interim', true ) );
        if ( $organisation != '' ) {
            echo "<div class='materialpool-template-material-organisation'>";
            echo $organisation ;
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
        global $post;

        $verweise = Materialpool_Material::get_organisation();
        $back = true;
        if ( $verweise === false) {
            $back = false;
        }
        if ( is_array( $verweise ) && $verweise[ 0 ] === false ) {
            $back = false;
        }
        $interim = get_metadata( 'post', $post->ID, 'material_organisation_interim', true );
        if ( $interim != '' ) {
            $back = true;
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
        global $post;
        $verweise = Materialpool_Material::get_autor();
        $back = true;
        if ( $verweise === false) {
            $back = false;
        }
        if ( is_array( $verweise ) && $verweise[ 0 ] === false ) {
            $back = false;
        }
        $interim = get_metadata( 'post', $post->ID, 'material_autor_interim', true );
        if ( $interim != '' ) {
            $back = true;
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
        global $post;
        $verweise = Materialpool_Material::get_autor();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
            $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $vorname .' '. $nachname . '</a><br>';

        }
        // Output INterim Autor
        $autor = apply_filters( 'materialpool_material_description_interim_autor', get_metadata( 'post', $post->ID, 'material_autor_interim', true ) );
        if ( $autor != '' ) {
            echo   $autor ;
        }
    }

    /**
     *
     * @since 0.0.1
     * @access public
     * @filters materialpool-template-material-autor
     */
    static public function autor_html_picture () {
        global $post;
        $verweise = Materialpool_Material::get_autor();
        foreach ( $verweise as $verweis ) {
            $url = get_permalink( $verweis[ 'ID' ] );
            $logo = get_metadata( 'post', $verweis[ 'ID' ], 'autor_bild_url', true );
            $vorname = get_post_meta($verweis[ 'ID' ], 'autor_vorname', true );
            $nachname = get_post_meta($verweis[ 'ID' ], 'autor_nachname', true );
            if ( $logo != '') {
                //echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'"><img  class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'" src="' . $logo . '"></a>';
                echo '<a href="' . $url . '" style="background-image:url(\'' . $logo . '\')" class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'"><img  class="'. apply_filters( 'materialpool-template-material-verweise', 'materialpool-template-material-autor-logo' ) .'" src="' . $logo . '"></a>';
            }
            echo '<a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-autor', 'materialpool-template-material-autor' ) .'">' . $vorname . ' '. $nachname . '</a>';

        }

        // Output INterim Autor
        $autor = apply_filters( 'materialpool_material_description_interim_autor', get_metadata( 'post', $post->ID, 'material_autor_interim', true ) );
        if ( $autor != '' ) {
            echo $autor;
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

        if (defined('REST_REQUEST') && REST_REQUEST) {
            $url = esc_url_raw( $_POST[ 'mp_url'] );
        } else {
            $url =  parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
        }
        $data = '';
        $bildungsstufe = get_metadata( 'post', $post->ID, 'material_bildungsstufe' );
        if ( ! Materialpool_Material::is_old() ) {
            if ( sizeof( $bildungsstufe ) == 1 ) {
                if ( $bildungsstufe[ 0 ] !== false ) {
                    if ( $bildungsstufe[ 0 ][ 'parent'] != 0 ) {
	                    $link = "/facettierte-suche/?fwp_bildungsstufe=". $bildungsstufe[0][ 'slug' ];
                        $data .= '<span class="facet-tag"><a href="' . $link . '">' . $bildungsstufe[0][ 'name' ] .'</a></span>';
                    }
                }
            } else {
                foreach ( $bildungsstufe as $bildung ) {
                    if ( $bildung[ 'parent'] != 0 ) {
	                    $link = "/facettierte-suche/?fwp_bildungsstufe=". $bildung[ 'slug' ];
                        $data .= '<span class="facet-tag"><a href="' . $link . '">' . $bildung[ 'name' ] .'</a></span>';
                    }
                }
            }
        } else {
            if ( sizeof( $bildungsstufe ) == 1 ) {
                $term = get_term( $bildungsstufe, "bildungsstufe" );
                if ( $term->parent != 0 ) {
	                $link = "/facettierte-suche/?fwp_bildungsstufe=". $term->slug;
                    $data .= '<span class="facet-tag"><a href="' . $link . '">' . $term->name .'</a></span>';
                }

            } else {
                foreach ( $bildungsstufe as $bildung ) {
                    $term = get_term( $bildung, "bildungsstufe" );
                    if ( $term->parent != 0 ) {
	                    $link = "/facettierte-suche/?fwp_bildungsstufe=". $term->slug;
	                    $data .= '<span class="facet-tag"><a href="' . $link . '">' . $term->name .'</a></span>';
                    }
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
        $data = array();
        $bildungsstufe = get_metadata( 'post', $post->ID, 'material_bildungsstufe' );
        foreach ( $bildungsstufe as $bildung ) {
            if ( $bildung[ 'parent'] != 0 ) {
                $data[] =  $bildung[ 'name' ] ;
            }
        }
        if ( is_array( $data ) ) {
	        return implode( ', ', $data );
        } else {
            return $data;
        }
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
	            $link = "/facettierte-suche/?fwp_bildungsstufe=". $bildungsstufe[0][ 'slug' ];
	            $data .= '<span class="facet-tag"><a href="' . $link . '">' . $bildungsstufe[0][ 'name' ] .'</a></span>';
            } else {
                $data = "";
            }
        } else {
            foreach ( $bildungsstufe as $bildung ) {
	            $link = "/facettierte-suche/?fwp_bildungsstufe=". $bildung[ 'slug' ];
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
     * @return mixed
     */
    static public function is_old() {
        global $post;

        $back = false;
        $old =  get_metadata( 'post', $post->ID, 'materialpool_old', true );

        if ( $old == '1' ) {
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
        $ignore = get_metadata( 'post', $post->ID, 'material_no_viewer', true );
        if ( mb_endsWith($url, '.pdf' ) && $ignore != 1  ) {
            $back = true;
        }

        return $back;
    }

	/**
	 * @since 0.0.1
	 * @access public
	 * @return mixed
	 */
	static public function is_playable() {
		global $post;

		$back = false;
		$url =  get_metadata( 'post', $post->ID, 'material_url', true );
		$ignore = get_metadata( 'post', $post->ID, 'material_no_viewer', true );

		$re = '/^(http(s)??\:\/\/)?(www\.)?((youtube\.com\/watch\?v=)|(youtu.be\/))([a-zA-Z0-9\-_])+$/';
		preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);

		if ( count ( $matches ) > 0  && $ignore != 1 )  {
			$back = true;
		}

		return $back;
	}


    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function is_old_material() {
        global $post;

        $back = false;
        $meta = get_metadata( 'post', $post->ID, 'materialpool_old', true );
        if ( $meta == 1 ) {
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
        $ignore = get_metadata( 'post', $post->ID, 'material_no_viewer', true );
        if ($url === false || $ignore == 1 ) {
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
     * @return string
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
     * @return string
     */
	
	static public function get_medientypen() {
        global $post;

        $data = '';
        $medientypen = get_metadata( 'post', $post->ID, 'material_medientyp' );
        if ( sizeof( $medientypen ) == 1 ) {
            if ( $medientypen[ 0 ] !== false ) {
                if ( $data != '') $data .= ', ';
                $data .= $medientypen[ 0 ][ 'name' ];
            } else {
                $data = "";
            }
        } else {
            foreach ( $medientypen as $medientyp ) {
                if ( $data != '') $data .= ', ';
                $data .= $medientyp[ 'name' ];
            }
        }
        return $data;
    }

    /**
     * @since 0.0.1
     * @access public
     * prints feed item categpries
     */
	static public function the_rss_categories() {
        global $post;
		
		$categories = explode(',',self::get_bildungsstufen());
				
		
		foreach($categories as $category): if(!empty( $category )):?>
		<category><![CDATA[<?php echo $category;?>]]></category>
		<?php endif; endforeach;
		
	}

    /**
     * @since 0.0.1
     * @access public
     * prints tags inside item content
     */
	static public function the_rss_tags() {
        global $post;
		
		$medientypen = explode(',',self::get_medientypen());
		$schlagworte = explode(',',self::get_schlagworte());
        
		$tags = array_merge($schlagworte,$medientypen);
		
		echo '<p style="display:none">';
		
		foreach($tags as $tag): if(!empty( $tag )): ?>
		<a rel="tag"><?php echo $tag;?></a>
		<?php endif; endforeach;
		
		echo '<p>';
	}
	
	
    /**
     * @since 0.0.1
     * @access public
     * @return mixed
     */
    static public function get_schlagworte_html( $url = '' ) {
        global $post;
        if ( $url == '' ) {
            if (defined('REST_REQUEST') && REST_REQUEST) {
                $url = esc_url_raw( $_POST[ 'mp_url'] );
            } else {
                $url =  parse_url( $_SERVER[ 'REQUEST_URI' ], PHP_URL_PATH );
            }
        }
        $data = '';
        $schlagworte = get_metadata( 'post', $post->ID, 'material_schlagworte' );
        if ( sizeof( $schlagworte ) == 1 ) {
            if ( $schlagworte[ 0 ] !== false ) {
                $link = "/facettierte-suche/?fwp_schlagworte=". $schlagworte[0][ 'slug' ];
                if ( $data != '') $data .= ', ';
                $data .= '<a href="' . $link . '">' . $schlagworte[0][ 'name' ] .'</a>';
            } else {
                $data = "";
            }
        } else {
            foreach ( $schlagworte as $schlagwort ) {
	            $link = "/facettierte-suche/?fwp_schlagworte=". $schlagwort[ 'slug' ];
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
         return "<a class='cta-button' href=\"".  self::get_url() ."\">".$text."</a>";
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
	    if (defined('REST_REQUEST') && REST_REQUEST) {  
            $thema = (int) $_POST[ 'mp_thema'];
            $gruppe = (int) $_POST[ 'mp_gruppe'];

        } else {
            $thema = (int) $_GET[ 'thema'];
            $gruppe = (int) $_GET[ 'gruppe'];
        }
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

    /**
     *
     * @since 0.0.1
     * @access	public
     * @return string
     *
     */
    static public function write_javascript() {
        ?>

        <script>
        jQuery(document).ready(function(){
        jQuery(".pods-form-ui-field-name-pods-meta-material-bildungsstufe").click( function() {
            switch ( jQuery(this).val() ) {
<?php
        $terms = get_terms( array(
            'taxonomy' => 'bildungsstufe',
        ) );
        foreach ( $terms as $term ) {
            if ( $term->parent != 0 ) {
                ?>
                case "<?php echo $term->term_id; ?>":    // Elementarbereich
                set_bildungsstufe( <?php echo $term->parent; ?> );
                break;
            <?php
            }
        }
        ?>

            }
        });
        });
        function set_bildungsstufe( id ) {
            jQuery(".pods-form-ui-field-name-pods-meta-material-bildungsstufe").each( function() {
                if ( jQuery(this).val() == id ) {
                    jQuery(this).attr('checked', true);
                }
            })
        };

        jQuery(document).ready(function(){
            jQuery(".pods-form-ui-field-name-pods-meta-material-medientyp").click( function() {
                switch ( jQuery(this).val() ) {
                <?php
                    $terms = get_terms( array(
                        'taxonomy' => 'medientyp',
                    ) );
                    foreach ( $terms as $term ) {
                    if ( $term->parent != 0 ) {
                    ?>
                    case "<?php echo $term->term_id; ?>":  
                        set_medientyp( <?php echo $term->parent; ?> );
                        break;
                <?php
                    }
                    }
                    ?>

                }
            });
        });
        function set_medientyp( id ) {
            jQuery(".pods-form-ui-field-name-pods-meta-material-medientyp").each( function() {
                if ( jQuery(this).val() == id ) {
                    jQuery(this).attr('checked', true);
                }
            })
        };

        </script>
        <?php
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @return string
     *
     */
    static public function review_count() {
        global $wpdb;

        $result = $wpdb->get_var( $wpdb->prepare( "SELECT count( $wpdb->posts.ID) as anzahl  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value != '0000-00-00' order by post_title asc" , 'material_wiedervorlagedatum' ) );
        return $result;
    }

    /**
     *
     * @since 0.0.1
     * @access	public
     * @return string
     *
     */
    static public function depublication_count() {
        global $wpdb;

        $result = $wpdb->get_var( $wpdb->prepare( "SELECT count( $wpdb->posts.ID) as anzahl  FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value != '0000-00-00' order by post_title asc" , 'material_depublizierungsdatum' ) );
        return $result;

    }

    /**
     *
     * @since 0.0.1
     * @access	public
     *
     */
    static public function depublizierung() {
        global $wpdb;

        $result = $wpdb->get_results( $wpdb->prepare( "SELECT  $wpdb->posts.ID FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND  $wpdb->postmeta.meta_key = %s AND $wpdb->postmeta.meta_value <= CURRENT_DATE   AND $wpdb->postmeta.meta_value != '0000-00-00' " , 'material_depublizierungsdatum' ) );
        if ( is_array( $result ) ) {
            foreach ( $result as $obj ) {
                wp_trash_post(  $obj->ID );
            }
        } else {
            if ( ! is_wp_error( $result ) ) {
                wp_trash_post(  $result );
            }
        }

    }

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 *
	 */
    static public function set_createdate( $postID = null ) {
        global $post;

        if ( $postID == null ) {
            $postID = $post->ID;
        }
        $date = get_post_meta( $postID, 'create_date', true );
        if ( $date == '' ) {
            add_post_meta( $postID, 'create_date', date( 'Y-m-d' ), true );
        }
    }

	/**
	 *
	 * @since 0.0.1
	 * @access	public
	 * @return  int
	 */
    static public function submit_count() {
	    global $wpdb;

	    $query_str 		= $wpdb->prepare('SELECT count(id) as anzahl   FROM `' . $wpdb->posts . '`  
										 WHERE post_status = %s ', "vorschlag" );
	    $items_arr 		= $wpdb->get_results( $query_str , ARRAY_A );
	    return $items_arr[ 0 ][ 'anzahl' ];

    }


    static public function add_open_graph() {
        global $post;

        if ( 'material' != $post->post_type ) {
            return;
        }
        ?>
	    <meta property="og:title" content="<?php Materialpool_Material::title(); ?>" />
	    <meta property="og:type" content="article" />
	    <meta property="og:image" content="<?php echo Materialpool_Material::get_cover(); ?>" />
	    <meta property="og:url" content="<?php echo get_permalink(); ?>" />
	    <meta property="og:description" content="<?php echo  strip_tags( Materialpool_Material::get_description() ) ; ?>" />
	    <meta property="og:site_name" content="rpi-virtuell Materialpool" />
        <?php
    }

    static public function admin_posts_filter( $query ) {
	    global $pagenow, $wpdb;
	    if ( is_admin() && $pagenow=='edit.php' && isset($_GET['ORGA_FILTER_FIELD_NAME']) && $_GET['ORGA_FILTER_FIELD_NAME'] != '') {
		    $query->query_vars['meta_key'] = 'material_organisation';
		    $query->query_vars['meta_value'] = $_GET['ORGA_FILTER_FIELD_NAME'];
	    }
	    if ( is_admin() && $pagenow=='edit.php' && isset($_GET['AUTOR_FILTER_FIELD_NAME']) && $_GET['AUTOR_FILTER_FIELD_NAME'] != '') {
		    $query->query_vars['meta_key'] = 'material_autoren';
		    $query->query_vars['meta_value'] = $_GET['AUTOR_FILTER_FIELD_NAME'];
	    }

	    if ( is_admin() && $pagenow=='edit.php' && isset( $_REQUEST[ 'mode'] ) &&  $_REQUEST[ 'mode'] == 'incomplete' )  {
		    $result = $wpdb->get_results("
        SELECT distinct( $wpdb->posts.ID ) , $wpdb->posts.post_title, DATE_FORMAT ( post_date, '%d.%m.%y' ) AS datum  FROM 
	$wpdb->posts, $wpdb->postmeta 
WHERE 
	$wpdb->posts.ID = $wpdb->postmeta.post_id AND  
	$wpdb->posts.post_type = 'material' AND
	( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND
(
	( 
	(
	   not exists( select * from wp_postmeta where meta_key='material_schlagworte' and post_id = wp_posts.ID )
	 OR  
		( 
			wp_postmeta.meta_key = 'material_schlagworte' AND 
			wp_postmeta.meta_value = ''  
		)
		) 
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_url' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_beschreibung' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_kurzbeschreibung' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR	
	( 
	   not exists( select * from $wpdb->postmeta where meta_key='_pods_material_medientyp' and post_id = $wpdb->posts.ID )
	 OR  
		( 
			$wpdb->postmeta.meta_key = '_pods_material_medientyp' AND 
			$wpdb->postmeta.meta_value = ''  
		)
	)
	OR	
	( 
	   not exists( select * from $wpdb->postmeta where meta_key='material_bildungsstufe' and post_id = $wpdb->posts.ID )
	 OR  
		( 
			$wpdb->postmeta.meta_key = 'material_bildungsstufe' AND 
			$wpdb->postmeta.meta_value = ''  
		)
	)
)	
order by wp_posts.post_date  asc ") ;
		    $idlist = array();
		    foreach ( $result as $obj ) {
			    $idlist[] = $obj->ID ;
		    }
		    $query->query_vars['post__in'] = $idlist;
	    }


	    return $query;
    }

    static public function get_themenseiten_for_material( $material_id = 0 ) {
	    global $post;
	    global $wpdb;

	    $material_id = ($material_id>0)?$material_id:$post->ID;
	    $tablename = $wpdb->prefix . "pods_themenseitengruppen";
        $query = "select id, post_title  from $wpdb->posts where id in ( select  pandarf_parent_post_id   from $tablename  where ( auswahl like '%,{$material_id},%' or auswahl like  '%,{$material_id}'  ) ) and post_status = 'publish'   and post_type= 'themenseite' order by post_title;";

	    $count = $wpdb->get_results($query);
	    return $count;
    }

    static public function get_themenseiten_for_material_html( $material_id  = 0) {
        $result = Materialpool_Material::get_themenseiten_for_material( $material_id);
        if ( is_array( $result ) &&  sizeof( $result ) > 0 ) {
            echo "Dieses Material ist Teil folgender Themenseiten:<br>";
	        foreach ( $result as $item ) {
	            $url = get_permalink( $item->id );
	            echo "<a href='" . $url  ."'>".  $item->post_title . "</a><br>";

            }
            echo "<br>";
        }
    }

    static public function back_to_search() {
        if ( $_GET[ 'sq' ] ) {
            $sq = $_GET[ 'sq' ];
            ?>
	        <a class='cta-button' href="<?php echo urldecode( $sq ); ?>">Zurück zur Materialsuche</a><br>
            <?php
        }
    }

    static public function  rss_query_vars( $query_vars ) {
	    $query_vars[] = 'rss_organisation';
	    $query_vars[] = 'rss_per_page';
	    return $query_vars;
    }

	static public function rss_pre_get_posts( $query ) {
		if( $query->is_feed && $query->is_main_query() && $query->query[ 'post_type' ] == 'material' ) {
			if( isset( $query->query_vars[ 'rss_organisation' ] ) && ! empty( $query->query_vars[ 'rss_organisation' ] ) ) {
				if ( $post = get_page_by_path( $query->query_vars[ 'rss_organisation' ] , OBJECT, 'organisation' ) ) {
					$id = $post->ID;
				} else {
					$id = $query->query_vars['rss_organisation'];
				}
				$query->set( 'meta_key', 'material_organisation' );
				$query->set( 'meta_value', $id );
			}
			if( isset( $query->query_vars[ 'rss_per_page' ] ) && ! empty( $query->query_vars[ 'rss_per_page' ] ) ) {
				$query->set( 'posts_per_rss', (int) $query->query_vars[ 'rss_per_page' ] );
			}

		}
		//return $query;
	}

	static public function add_material_filter_view( $view ) {
		global $wpdb;
		$count = 0;
		$result = $wpdb->get_results("
        SELECT distinct( $wpdb->posts.ID ) , $wpdb->posts.post_title, DATE_FORMAT ( post_date, '%d.%m.%y' ) AS datum  FROM 
	$wpdb->posts, $wpdb->postmeta 
WHERE 
	$wpdb->posts.ID = $wpdb->postmeta.post_id AND  
	$wpdb->posts.post_type = 'material' AND
	( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' )  AND
(
	( 
	(
	   not exists( select * from wp_postmeta where meta_key='material_schlagworte' and post_id = wp_posts.ID )
	 OR  
		( 
			wp_postmeta.meta_key = 'material_schlagworte' AND 
			wp_postmeta.meta_value = ''  
		)
		) 
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_url' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_beschreibung' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR
	( 
		$wpdb->postmeta.meta_key = 'material_kurzbeschreibung' AND 
 			$wpdb->postmeta.meta_value = ''  
	)
OR	
	( 
	   not exists( select * from $wpdb->postmeta where meta_key='_pods_material_medientyp' and post_id = $wpdb->posts.ID )
	 OR  
		( 
			$wpdb->postmeta.meta_key = '_pods_material_medientyp' AND 
			$wpdb->postmeta.meta_value = ''  
		)
	)
	OR	
	( 
	   not exists( select * from $wpdb->postmeta where meta_key='material_bildungsstufe' and post_id = $wpdb->posts.ID )
	 OR  
		( 
			$wpdb->postmeta.meta_key = 'material_bildungsstufe' AND 
			$wpdb->postmeta.meta_value = ''  
		)
	)
)	
order by wp_posts.post_date  asc ") ;
		foreach ( $result as $obj ) {
			$count++;
		}

        $url= admin_url( 'edit.php?post_type=material&mode=incomplete' );
		$active = false;
		if ( isset( $_REQUEST[ 'mode'] ) &&  $_REQUEST[ 'mode'] == 'incomplete' ) {
		    $active = trueM;
        }
		$string = '<a href="'. $url  .'" ';
        if ( $active ) {
            $string .= ' class="current" ';
        }
		$string .= '>Unvollständig</a> (' . $count . ')';


		$view[ 'incomplete'] = $string;
        return $view;
    }


}
